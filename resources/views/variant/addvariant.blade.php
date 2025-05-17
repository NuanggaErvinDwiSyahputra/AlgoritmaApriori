@extends('index')

@section('tittle', 'Add Variant')

@section('content')
<div class="row ml-2" onclick="location.href='{{ route('variant') }}'">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="1e1e1e" d="m3.55 12l7.35 7.35q.375.375.363.875t-.388.875t-.875.375t-.875-.375l-7.7-7.675q-.3-.3-.45-.675T.825 12t.15-.75t.45-.675l7.7-7.7q.375-.375.888-.363t.887.388t.375.875t-.375.875z"/></svg>
    <h5> Back </h5>
</div>
<div class="card shadow-lg" style="border-radius: 2rem; overflow: hidden;">
    <div class="card-body" style="border-radius: 2rem; background-color: #fff; color:black">
        <form action="{{ route('addvariant.store') }}" method="post">
            @csrf
            <div class="form-group">
                <label for="menu">Menu</label>
                <select class="form-control @error('menu') is-invalid @enderror" name="menu">
                    <option value="">- Silahkan Pilih Menu -</option>
                    @foreach ($menu as $m)
                        <option value="{{ $m->nama_menu }}" {{ old('menu') == $m->nama_menu ? 'selected' : '' }}>
                            {{ $m->nama_menu }}
                        </option>
                    @endforeach
                </select>
                @error('menu')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        
            <div class="form-group">
                <label for="variant">Variant</label>
                <input type="text" class="form-control @error('variant') is-invalid @enderror" id="variant" name="variant" placeholder="Input Nama Variant" >
                @error('variant')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>    
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" class="form-control @error('harga') is-invalid @enderror" id="harga" name="harga" placeholder="Input Nilai Harga">
                @error('harga')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>        
            <button type="submit" class="btn btn-outline-primary py-2 px-5 rounded-3 w-100 mt-3">Submit</button>
        </form>
    </div>
</div>
@endsection
