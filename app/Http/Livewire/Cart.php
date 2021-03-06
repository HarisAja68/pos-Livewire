<?php

namespace App\Http\Livewire;

use Haruncpi\LaravelIdGenerator\IdGenerator;
use App\Models\Produk as ProdukModel;
use App\Models\ProdukTransaksi;
use App\Models\Transaksi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Cart extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public $tax = "0%";
    public $payment = 0;

    public function render()
    {
        $produk = ProdukModel::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(4);

        $condition = new \Darryldecode\Cart\CartCondition([
            'name' => 'pajak',
            'type' => 'tax',
            'target' => 'total',
            'value' => $this->tax,
            'order' => 1,
        ]);

        \Cart::session(Auth()->id())->condition($condition);
        $items = \Cart::session(Auth()->id())->getContent()->sortBy(function ($cart) {
            return $cart->attributes->get('added_at');
        });

        if (\Cart::isEmpty()) {
            $cartData = [];
        } else {
            foreach ($items as $item) {
                $cart[] = [
                    'rowId' => $item->id,
                    'name' => $item->name,
                    'qty' => $item->quantity,
                    'pricesingle' => $item->price,
                    'price' => $item->getPriceSum(),
                ];
            }

            $cartData = collect($cart);
        }

        $sub_total = \Cart::session(Auth()->id())->getSubTotal();
        $total =  \Cart::session(Auth()->id())->getTotal();

        $newCondition = \Cart::session(Auth()->id())->getCondition('pajak');
        $pajak = $newCondition->getCalculatedValue($sub_total);

        $summary = [
            'sub_total' => $sub_total,
            'pajak' => $pajak,
            'total' => $total,
        ];

        return view('livewire.cart', [
            'produk' => $produk,
            'cart' => $cartData,
            'summary' => $summary,
        ]);
    }

    public function addItem($id)
    {
        $rowId = "Cart" . $id;
        $idProduk = substr($rowId, 4, 5);
        $produk = ProdukModel::find($idProduk);

        $cart = \Cart::session(Auth()->id())->getContent();
        $cekItemId = $cart->whereIn('id', $rowId);

        if ($cekItemId->isNotEmpty()) {
            if ($produk->qty == $cekItemId[$rowId]->quantity) {
                session()->flash('error', 'Jumlah Item Kurang');
            } else {
                \Cart::session(Auth()->id())->update($rowId, [
                    'quantity' => [
                        'relative' => true,
                        'value' => 1,
                    ]
                ]);
            }
        } else {
            if ($produk->qty == 0) {
                session()->flash('error', 'Jumlah Item Kurang');
            } else {
                \Cart::session(Auth()->id())->add([
                    'id' => "Cart" . $produk->id,
                    'name' => $produk->name,
                    'price' => $produk->price,
                    'quantity' => 1,
                    'attributes' => [
                        'added_at' => Carbon::now(),
                    ],
                ]);
            }
        }
    }

    public function enableTax()
    {
        $this->tax = "+10%";
    }

    public function disableTax()
    {
        $this->tax = "0%";
    }

    public function increaseItem($rowId)
    {
        $idProduk = substr($rowId, 4, 5);
        $produk = ProdukModel::find($idProduk);

        $cart = \Cart::session(Auth()->id())->getContent();
        $cekItem = $cart->whereIn('id', $rowId);

        if ($produk->qty == $cekItem[$rowId]->quantity) {
            session()->flash('error', 'Jumlah Item Kurang');
        } else {
            if ($produk->qty == 0) {
                session()->flash('error', 'Jumlah Item Kurang');
            } else {
                \Cart::session(Auth()->id())->update($rowId, [
                    'quantity' => [
                        'relative' => true,
                        'value' => 1,
                    ]
                ]);
            }
        }
    }

    public function decreaseItem($rowId)
    {
        $idProduk = substr($rowId, 4, 5);
        $produk = ProdukModel::find($idProduk);

        $cart = \Cart::session(Auth()->id())->getContent();
        $cekItem = $cart->whereIn('id', $rowId);

        if ($cekItem[$rowId]->quantity == 1) {
            $this->removeItem($rowId);
        } else {
            \Cart::session(Auth()->id())->update($rowId, [
                'quantity' => [
                    'relative' => true,
                    'value' => -1,
                ]
            ]);
        }
    }

    public function removeItem($rowId)
    {
        \Cart::session(Auth()->id())->remove($rowId);
    }

    public function handleSubmit()
    {
        $cartTotal = \Cart::session(Auth()->id())->getTotal();
        $bayar = $this->payment;
        $kembalian = (int) $bayar - (int) $cartTotal;

        if ($kembalian >= 0) {
            DB::beginTransaction();

            try {
                $allCart = \Cart::session(Auth()->id())->getContent();
                $filterCart = $allCart->map(function ($item) {
                    return [
                        'id' => substr($item->id, 4, 5),
                        'quantity' => $item->quantity,
                    ];
                });
                foreach ($filterCart as $cart) {
                    $produk = ProdukModel::find($cart['id']);

                    if ($produk->qty == 0) {
                        session()->flash('error', 'Jumlah Item Kurang');
                    }

                    $produk->decrement('qty', $cart['quantity']);
                }

                $id = IdGenerator::generate([
                    'table' => 'transaksi',
                    'length' => 10,
                    'prefix' => 1,
                    'field' => 'invoice_number',
                ]);

                Transaksi::create([
                    'invoice_number' => $id,
                    'user_id' => Auth()->id(),
                    'pay' => $bayar,
                    'total' => $cartTotal,
                ]);

                foreach ($filterCart as $cart) {
                    ProdukTransaksi::create([
                        'produk_id' => $cart['id'],
                        'invoice_number' => $id,
                        'qty' => $cart['quantity'],
                    ]);
                }
                \Cart::session(Auth()->id())->clear();
                $this->payment = 0;

                DB::commit();
                session()->flash('error', 'Transaksi berhasil disimpan');
            } catch (\Throwable $th) {
                DB::rollBack();
                return session()->flash('error', $th);
            }
        }
    }
}
