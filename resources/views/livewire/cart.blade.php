<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h2 class="font-weight-bold">Produk List</h2>
                <div class="row">
                    @foreach ($produk as $item)
                        <div class="col-md-3 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <img src="{{ asset('storage/images/'.$item->image) }}" alt="Produk" class="img-fluid">
                                </div>
                                <div class="card-footer">
                                    <h5 class="text-center font-weight-bold">{{ $item->name }}</h5>
                                    <button wire:click="addItem({{ $item->id }})" class="btn btn-primary btn-sm btn-block">Tambah Ke Cart</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
                                    <a href="#" wire:click="increaseItem('{{ $item['rowId'] }}')" class="font-weight-bold d-inline">+</a>
                                    <a href="#" wire:click="decreaseItem('{{ $item['rowId'] }}')" class="font-weight-bold d-inline">-</a>
                                    <a href="#" wire:click="removeItem('{{ $item['rowId'] }}')" class="font-weight-bold d-inline">x</a>
                                </td>
                                <td>{{ $item['price'] }} </td>
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
                <h4 class="font-weight-bold">Sub Total: {{ $summary['sub_total'] }}</h4>
                <h4 class="font-weight-bold">pajak: {{ $summary['pajak'] }}</h4>
                <h4 class="font-weight-bold">Total: {{ $summary['total'] }}</h4>
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
