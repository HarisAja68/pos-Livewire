<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h2 class="font-weight-bold">Produk List</h2>
                    </div>
                    <div class="col-md-6 mb-3">
                        <input wire:model="search" type="text" class="form-control" placeholder="Search Produk...">
                    </div>
                </div>
                <div class="row">
                    @forelse ($produk as $item)
                    <div class="col-md-3 mb-3">
                        <div class="card">
                            <div class="card-body">
                                <img src="{{ asset('storage/images/'.$item->image) }}" alt="Produk" style="object-fit: contain; width: 100%; height:125px">
                            </div>
                            <div class="card-footer">
                                <h5 class="text-center font-weight-bold">{{ $item->name }}</h5>
                                <button wire:click="addItem({{ $item->id }})" class="btn btn-primary btn-sm btn-block">Tambah Ke Cart</button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-md-12 mt-2">
                        <h2 class=" text-center font-weight-bold">Tidak ditemukan produk yang dicari</h2>
                    </div>
                    @endforelse
                </div>
                <div class="d-flex justify-content-center">
                    {{ $produk->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h2 class="font-weight-bold">Cart</h2>
                <p class="text-danger font-weight-bold">
                    @if (session()->has('error'))
                        {{ session('error') }}
                    @endif
                </p>
                <table class="table table-bordered table-striped">
                    <thead class="bg-secondary text-white">
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cart as $key => $item)
                            <tr>
                                <td>{{ $key+1 }}</td>
                                <td>
                                    <h5 class="font-weight-bold">{{ $item['name'] }}</h5>
                                    Jumlah: {{ $item['qty'] }}
                                    <h6 wire:click="increaseItem('{{ $item['rowId'] }}')" class="font-weight-bold d-inline" style="cursor:pointer"><i class="fa fa-plus"></i></h6>
                                    <h6 wire:click="decreaseItem('{{ $item['rowId'] }}')" class="font-weight-bold d-inline" style="cursor:pointer"><i class="fa fa-minus"></i></h6>
                                    <h6 wire:click="removeItem('{{ $item['rowId'] }}')" class="font-weight-bold text-danger d-inline" style="cursor:pointer"><i class="fa fa-trash-alt"></i></h6>
                                </td>
                                <td>Rp. {{ format_uang($item['price']) }}</td>
                            </tr>
                        @empty
                            <td colspan="3"><h5 class="text-center">Cart Kosong</h5></td>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div><br>
        <div class="card">
            <div class="card-body">
                <h4 class="font-weight-bold">Summary</h4>
                <h4 class="font-weight-bold">Sub Total: Rp. {{ format_uang($summary['sub_total']) }}</h4>
                <h4 class="font-weight-bold">Pajak: Rp. {{ format_uang($summary['pajak']) }}</h4>
                <h4 class="font-weight-bold">Total: Rp. {{ format_uang($summary['total']) }}</h4>
            </div>
            <div>
                <button wire:click="enableTax" class="btn btn-primary btn-block">Tambah Pajak</button>
                <button wire:click="disableTax" class="btn btn-danger btn-block">Hapus Pajak</button>
            </div>
            <div class="mt-4">
                <button class="btn btn-success btn-block active">Simpan Transaksi</button>
            </div>
        </div>
    </div>
</div>
