<?php

namespace App\Http\Livewire;

use App\Models\Produk as ProdukModel;
use Carbon\Carbon;
use Livewire\Component;

class Cart extends Component
{
    public $tax = "0%";

    public function render()
    {
        $produk = ProdukModel::orderBy('created_at', 'desc')->get();

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
        $cart = \Cart::session(Auth()->id())->getContent();
        $cekItemId = $cart->whereIn('id', $rowId);

        if ($cekItemId->isNotEmpty()) {
            \Cart::session(Auth()->id())->update($rowId, [
                'quantity' => [
                    'relative' => true,
                    'value' => 1,
                ]
            ]);
        } else {
            $produk = ProdukModel::findOrfail($id);
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
            \Cart::session(Auth()->id())->update($rowId, [
                'quantity' => [
                    'relative' => true,
                    'value' => 1,
                ]
            ]);
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
}
