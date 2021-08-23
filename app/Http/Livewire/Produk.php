<?php

namespace App\Http\Livewire;

use App\Models\Produk as ProdukModel;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Produk extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search;
    public function updatingSearch()
    {
        $this->resetPage();
    }

    use WithFileUploads;

    public $name, $image, $description, $qty, $price;

    public function render()
    {
        $produk = ProdukModel::where('name', 'like', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(5);
        return view('livewire.produk', [
            'produk' => $produk
        ]);
    }

    public function previewImage()
    {
        $this->validate([
            'image' => 'image|max:2048'
        ]);
    }

    public function store()
    {
        $this->validate([
            'name' => 'required',
            'image' => 'image|max:2048|required',
            'description' => 'required',
            'qty' => 'required',
            'price' => 'required',
        ]);

        $imageName = md5($this->image . microtime() . '.' . $this->image->extension());

        Storage::putFileAs(
            'public/images',
            $this->image,
            $imageName,
        );

        ProdukModel::create([
            'name' => $this->name,
            'image' => $imageName,
            'description' => $this->description,
            'qty' => $this->qty,
            'price' => $this->price,
        ]);

        session()->flash('info', 'Produk berhasil ditambahkan');

        $this->name = '';
        $this->image = '';
        $this->description = '';
        $this->qty = '';
        $this->price = '';
    }
}
