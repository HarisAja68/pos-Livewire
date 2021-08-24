<div class="row">
    <div class="col-md-7">
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
                                <button wire:click="addItem({{ $item->id }})" class="btn btn-primary btn-sm" style="position: absolute; top:0; right:0; padding: 10px 15px"><i class="fa fa-cart-arrow-down fa-lg"></i></button>
                            </div>
                            <div class="card-footer bg-white">
                                <h5 class="text-center font-weight-bold">{{ $item->name }}</h5>
                                <h6 class="text-center font-weight-bold">Rp. {{ format_uang($item->price) }}</h6>
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
    <div class="col-md-5">
        <div class="card">
            <div class="card-body">
                <h2 class="font-weight-bold">Cart</h2>
                <p class="text-danger font-weight-bold">
                    @if (session()->has('error'))
                        {{ session('error') }}
                    @endif
                </p>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th class="font-weight-bold">No</th>
                            <th class="font-weight-bold">Nama</th>
                            <th class="font-weight-bold">Jumlah</th>
                            <th class="font-weight-bold">Harga</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cart as $key => $item)
                            <tr>
                                <td>
                                    {{ $key+1 }}
                                    <br>
                                    <h6 wire:click="removeItem('{{ $item['rowId'] }}')" class="font-weight-bold text-danger d-inline" style="cursor:pointer"><i class="fa fa-trash-alt"></i></h6>
                                </td>
                                <td>
                                    <h5 class="font-weight-bold">{{ $item['name'] }}</h5>
                                    <h6 class="font-weight-bold">Rp. {{ format_uang($item['pricesingle']) }}</h6>
                                </td>
                                <td>
                                    <button class="btn btn-info" style="padding: 5px 7px" wire:click="decreaseItem('{{ $item['rowId'] }}')"><i class="fa fa-minus"></i></button>
                                    {{ $item['qty'] }}
                                    <button class="btn btn-primary" style="padding: 5px 7px" wire:click="increaseItem('{{ $item['rowId'] }}')"><i class="fa fa-plus"></i></button>
                                </td>
                                <td>Rp. {{ format_uang($item['price']) }}</td>
                            </tr>
                        @empty
                            <td colspan="4"><h5 class="text-center">Cart Kosong</h5></td>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div><br>
        <div class="card">
            <div class="card-body">
                <h4 class="font-weight-bold">Cart Summary</h4>
                <h5 class="font-weight-bold">Sub Total: Rp. {{ format_uang($summary['sub_total']) }}</h5>
                <h5 class="font-weight-bold">Pajak: Rp. {{ format_uang($summary['pajak']) }}</h5>
                <h5 class="font-weight-bold">Total: Rp. {{ format_uang($summary['total']) }}</h5>
            </div>
            <div class="row mt-1 container-fluid">
                <div class="col-md-6">
                    <button wire:click="enableTax" class="btn btn-primary btn-block mb-1">Tambah Pajak</button>
                </div>
                <div class="col-md-6">
                    <button wire:click="disableTax" class="btn btn-danger btn-block">Hapus Pajak</button>
                </div>
            </div>
            <div class="container-fluid mt-3">
                <input type="number" class="form-control" id="pembayaran" placeholder="Isi pembayaran pelanggan">
                <input type="hidden" id="total" value="{{ $summary['total'] }}">
            </div>
            <div class="container-fluid mt-2">
                <label>Pembayaran</label>
                <h1 id="pembayaranTex">Rp. 0</h1>
            </div>
            <div class="container-fluid mt-2">
                <label>Kembalian</label>
                <h1 id="kembalianTex">Rp. 0</h1>
            </div>
            <div class="container-fluid mt-2 mb-2">
                <button wire:ignore id="saveButton" class="btn btn-success btn-block" disabled><i class="fa fa-save"> Simpan Transaksi</i></button>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
    pembayaran.oninput = () => {
        const pembayaranAmount = document.getElementById("pembayaran").value;
        const totalAmount = document.getElementById("total").value;
        const kembalian = pembayaranAmount - totalAmount;

        document.getElementById("pembayaranTex").innerHTML = `Rp. ${rupiah(pembayaranAmount)}`;
        document.getElementById("kembalianTex").innerHTML = `Rp. ${rupiah(kembalian)}`;

        const saveButton = document.getElementById("saveButton");
        if (kembalian < 0) {
            saveButton.disabled = true;
        } else {
            saveButton.disabled = false;
        }
    }

    const rupiah = (angka) => {
            const numberString = angka.toString();
            const split = numberString.split(',');
            const sisa = split[0].length % 3;
            let rupiah = split[0].substr(0, sisa);
            const ribuan = split[0].substr(sisa).match(/\d{1,3}/gi);

            if(ribuan){
                const separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
        }
</script>
@endpush
