<div>
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="font-weight-bold mb-3">Produk List</h2>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th class="font-weight-bold">No</th>
                                <th class="font-weight-bold">Nama</th>
                                <th class="font-weight-bold" width="20%">Image</th>
                                <th class="font-weight-bold">Deskripsi</th>
                                <th class="font-weight-bold">Jumlah</th>
                                <th class="font-weight-bold">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produk as $key => $item)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $item->name }}</td>
                                <td><img src="{{ asset('storage/images/'.$item->image) }}" alt="image produk" class="img-fluid" width="75%"></td>
                                <td>{{ $item->description }}</td>
                                <td>{{ $item->qty }}</td>
                                <td>{{ $item->price }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h2 class="font-weight-bold mb-3">Tambah Produk</h2>
                    <form wire:submit.prevent="store">
                        <div class="form-group">
                            <label>Nama Produk</label>
                            <input wire:model="name" type="text" class="form-control">
                            @error('name')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Image Produk</label>
                            <div class="custom-file">
                                <input wire:model="image" type="file" class="custom-file-input" id="customFile">
                                <label for="customFile" class="custom-file-label">Pilih Image</label>
                                @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                            @if ($image)
                                <label class="mt-2">Preview Image:</label><br>
                                <img src="{{ $image->temporaryUrl() }}" class="img-fluid" alt="Preview Image">
                            @endif
                        </div>
                        <div class="form-group">
                            <label>Deskirpsi Produk</label>
                            <textarea wire:model="description" class="form-control"></textarea>
                            @error('description')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Jumlah Produk</label>
                            <input wire:model="qty" type="number" class="form-control">
                            @error('qty')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label>Harga Produk</label>
                            <input wire:model="price" type="number" class="form-control">
                            @error('price')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Tambah Produk</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card mt-3">
                <div class="card-body">
                    <h3>{{ $name }}</h3>
                    <h3>{{ $image }}</h3>
                    <h3>{{ $description }}</h3>
                    <h3>{{ $qty }}</h3>
                    <h3>{{ $price }}</h3>
                </div>
            </div>
        </div>
    </div>
</div>
