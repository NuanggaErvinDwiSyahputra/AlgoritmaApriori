@extends('index')

@section('tittle','Rekomendasi Paket Bundling Menu')

@section('content')
    <div class="card shadow-lg" style="border-radius : 2rem; overflow : hidden;">
        <div class="card-body row d-flex justify-content-center"> 
            <form method="GET" action="{{ route('rekomendasi.generate') }}" id="mainForm" class="row d-flex justify-content-center">
                <div>
                    <h6 class="d-flex justify-content-center">Pilih Tanggal Awal</h6>
                    <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}" onchange="submitToFilter()">
                </div>
                <div class="mr-2 ml-2">
                    <h6 class="d-flex justify-content-center">Pilih Tanggal Akhir</h6>
                    <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}" onchange="submitToFilter()">
                </div>
                <div class="flex col-lg-3">
                    <h6 class="d-flex justify-content-center">Min Support (0.01 - 1)</h6>
                    <input type="number" step="0.01" class="form-control" name="min_support" value="0.5" min="0.01" max="1" required>
                </div>
                <div class="flex col-lg-3">
                    <h6 class="d-flex justify-content-center">Min Confidence (0.01 - 1)</h6>
                    <input type="number" step="0.01" class="form-control" name="min_confidence" value="0.5" min="0.01" max="1" required>
                </div>
                
                <div class="mt-3 d-flex justify-content-center mx-auto">
                    <button type="submit" class="btn btn-outline-primary">Generate Bundling</button>
                </div>
            </form>
        </div>    
        @if ($errors->has('error'))
                    <div class="alert alert-danger">
                        {{ $errors->first('error') }}
                    </div>
                @endif
    </div>
    <div class="card shadow-lg mt-3" style="border-radius : 2rem; overflow : hidden;">
        <div class="card-body col-md-12 col-lg-12 col-sm-12">
            <div class="table-responsive">
                <table id="tablerekomendasi" class="table table-bordered mb-0 text-center" style="border-radius: 1.5rem; overflow : hidden; border : 1px solid #ddd;">
                    <thead class="table-light" style="border-radius: 1.5rem 1.5rem 0 0;">
                        <tr>
                            <th>No</th>
                            <th>Variant</th>
                            <th>Frekuensi Variant</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($frekuensi as $index => $item)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $item->slug }}</td>
                                <td>{{ $item->frekuensi }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>            
        </div>    
    </div>

<script>
function submitToFilter() {
    const form = document.getElementById('mainForm');

    // Buat action-nya ke route rekomendasi biasa (bukan generate)
    form.action = "{{ route('rekomendasi') }}";

    // Reset support/confidence agar tidak ikut saat filter
    form.querySelector('[name="min_support"]').value = '';
    form.querySelector('[name="min_confidence"]').value = '';

    form.submit();
}
</script>
@endsection