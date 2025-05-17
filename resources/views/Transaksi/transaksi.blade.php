@extends('index')

@section('tittle','Data Transaksi')


@section('content')

<div class="card shadow-lg" style="border-radius: 2rem; overflow: hidden;">
    <div class="card-body" style="border-radius: 2rem; background-color: #f9f9f9">
        <div class="mt-2 mb-4">
            <h5>Dashboard > Transaksi</h5>
        </div>
        <div class="d-flex align-items-center justify-content-between mb-4 gap 7">
            {{-- form Search --}}
            <form method="get" action="{{ route('transaksi') }}" class="d-flex align-items-center" id="search-form"
                style="gap: 0.5rem; border-radius: 1rem;  transition: all 0.3s;">
                <h5 for="per_page" class="mb-0 fw-semibold" style="color: #000">Search</h5>
                <input type="text" name="search" id="search-input" class="form-control form-control-sm"
                    value="{{ $search }}">
            </form>
            <div>
                <button class="btn btn-primary px-4 py-2" style="border-radius: 1rem; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); transition: all 0.3s;" onclick="location.href='{{ route('transaksi-entry') }}'">
                    Add Transaksi
                </button>
                <button class="btn btn-primary px-4 py-2" data-toggle="modal" data-target="#importModal"
                    style="border-radius:1rem; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); transition:all 0.3s;">
                    Import Data
                </button>
            </div>            
        </div>
        {{-- Form Filtering Tanggal --}}
        <form method="GET" action="{{ route('transaksi') }}" id="filterTransaksiForm" class="row mb-3">
            {{-- Tanggal Awal --}}
            <div class="col-md-3">
                <label for="start_date">Tanggal Awal</label>
                <input type="date" name="start_date" class="form-control"
                    value="{{ request('start_date') }}" 
                    onchange="document.getElementById('filterTransaksiForm').submit();">
            </div>
            {{-- Tanggal Akhir --}}
            <div class="col-md-3">
                <label for="end_date">Tanggal Akhir</label>
                <input type="date" name="end_date" class="form-control"
                    value="{{ request('end_date') }}" 
                    onchange="document.getElementById('filterTransaksiForm').submit();">
            </div>
        </form>
        {{-- Menampilkan Pesan Error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="table-responsive">
            <form method="POST" action="{{ route('transaksi.bulkDelete') }}" id="bulk-delete-form">
                @csrf
                @method('DELETE')
                {{-- button delete all --}}
                <button type="submit" class="btn btn-primary mb-3 d-none" id="delete-selected-btn"
                    onclick="return confirm('Yakin ingin menghapus data yang dipilih?')">
                    Delete Selected
                </button>
                <table class="table table-bordered mb-0 text-center"
                    style="border-radius: 1.5rem; overflow: hidden; border: 1px solid #ddd;">
                    <thead class="table-light" style="border-radius: 1.5rem 1.5rem 0 0;">

                        <tr>
                            <th scope="col" style="width: 50px;"><input type="checkbox" id="select-all"></th>
                            <th>No Transaksi</th>
                            <th>Tanggal</th>
                            <th>Nama Menu</th>
                            <th>Variant</th>
                            <th>Harga</th>
                            <th>Jumlah</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>

                    </thead>
                    <tbody>
                        @forelse ($transaksi as $tr)
                        <tr>
                            <td>
                                <input type="checkbox" name="selected[]" value="{{ $tr->id_transaksi }}"
                                    class="row-checkbox">
                            </td>
                            <td>{{ $tr->no_transaksi }}</td>
                            <td>{{ $tr->tgl_transaksi }}</td>
                            <td>{{ $tr->menu->nama_menu ?? '-' }}</td>
                            <td>{{ $tr->variant->variant ?? '-' }}</td>
                            <td>{{ $tr->harga }}</td>
                            <td>{{ $tr->jumlah }}</td>
                            <td>{{ $tr->total }}</td>
                            <td>
                                <button type="button" class="btn btn-outline-danger px-4 py-2"
                                    style="border-radius: 1rem; transition: all 0.3s;"
                                    onclick="if(confirm('Yakin ingin menghapus data yang dipilih?')) { window.location='{{ route('transaksi-destroy', $tr->id_transaksi) }}'; }">
                                    Delete
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data transaksi</td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </form>
        </div>
        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-3">
            {{-- Kiri: Info jumlah entri --}}
            <div class="text-muted">
                Showing
                {{ $transaksi instanceof \Illuminate\Pagination\LengthAwarePaginator ? $transaksi->firstItem() : ($transaksi->count() ? 1 : 0) }}
                to
                {{ $transaksi instanceof \Illuminate\Pagination\LengthAwarePaginator ? $transaksi->lastItem() : $transaksi->count() }}
                of
                {{ $transaksi instanceof \Illuminate\Pagination\LengthAwarePaginator ? $transaksi->total() : $transaksi->count() }}
                entries
            </div>
            {{-- Dropdown show per page --}}
            <form method="GET" class="d-flex align-items-center mx-auto" style="gap: 1px;">
                <label for="per_page" class="mb-0 fw-semibold text-secondary">Show</label>
                <select name="per_page" id="per_page" class="form-select form-select-sm w-auto"
                    onchange="this.form.submit()">
                    <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>Semua</option>
                    <input type="hidden" name="search" value="{{ request('search') }}">
                    <input type="hidden" name="start_date" value="{{ request('start_date') }}">
                    <input type="hidden" name="end_date" value="{{ request('end_date') }}">

                </select>
                <span class="text-secondary">entries</span>
            </form>
            {{-- Kanan: Pagination --}}
            @if($perPage !== 'all')
            <div class="d-flex justify-content-end">
                {{ $transaksi->links('pagination::bootstrap-4') }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal Import Excel -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" >
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">       
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">Import Transaksi</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- <form method="POST" action="{{ route('transaksi.import') }}" class="dropzone" id="excelDropzoneForm" enctype="multipart/form-data">
                @csrf
                
   
                <div class="modal-body">
                    <div class="dz-message text-center" style="padding: 2rem;">
                        <h6>Drag & Drop file Excel di sini</h6>
                        <p class="text-muted">Atau klik untuk memilih file (.xlsx / .xls / .csv)</p>
                    </div>
                </div>
            </form>  --}}
            <form action="{{ route('transaksi.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="file" name="file" required>
                <button type="submit">Import</button>
            </form>
            <div class="modal-footer">
                <button type="submit" class="btn btn-outline-primary" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
  
@endsection



@section('css')
    <style>
    #excelDropzone {
        border: none !important;
        box-shadow: none !important;
        background: #f8f9fa;
        padding: 30px;
        text-align: center;
        border-radius: 10px;
    } 

     .modal-dialog {
            position: fixed;
            transform: translate(-50%, -50%);
            margin: 0;
            max-width: 700px;
            width: 100%;
        }
    .modal-backdrop.show {
        backdrop-filter: blur(10px);
        filter: blur(20px);
        background-color: rgba(0, 0, 0, 0.1);
    } 

</style>
@endsection

@section('js')


    <!-- Display success or error messages -->
@if (session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '{{ session('
            success ') }}',
            showConfirmButton: false,
            timer: 2000
        });
    });

</script>
@elseif (session('error'))
<div class="alert alert-danger mt-3">
    {{ session('error') }}
</div>
@endif

{{-- delete all --}}
<script>
    document.getElementById('select-all').addEventListener('change', function () {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
    });

</script>
<script>
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const deleteButton = document.getElementById('delete-selected-btn');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
            // Show or hide delete button based on whether any checkboxes are selected
            deleteButton.classList.toggle('d-none', !anyChecked);
        });
    });
    const selectAllCheckbox = document.getElementById('select-all');
    selectAllCheckbox.addEventListener('change', function () {
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAllCheckbox.checked;
        });
        // Show delete button if any checkbox is checked
        const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
        deleteButton.classList.toggle('d-none', !anyChecked);
    });

</script>

{{-- search --}}
<script>
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');

    let debounceTimeout;
    searchInput.addEventListener('input', function () {
        clearTimeout(debounceTimeout); // Reset timer setiap kali ngetik
        debounceTimeout = setTimeout(() => {
            searchForm.submit(); // Submit setelah 500ms tidak ngetik
        }, 1000);
    });

</script>

{{-- Harga --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selects = document.querySelectorAll('.variant-select');

        selects.forEach(select => {
            select.addEventListener('change', function () {
                const selectedOption = this.options[this.selectedIndex];
                const harga = selectedOption.getAttribute('data-harga');
                const targetInputSelector = this.getAttribute('data-target');
                const targetInput = document.querySelector(targetInputSelector);

                if (targetInput) {
                    targetInput.value = harga;
                }
            });
        });
    });

</script>

<script>
    Dropzone.autoDiscover = false;
    document.addEventListener("DOMContentLoaded", function () {
        const excelDropzone = new Dropzone("#excelDropzoneForm", {
            url: "{{ url('/transaksi/import') }}",
            paramName: "file",
            maxFilesize: 2, // MB
            acceptedFiles: ".xlsx,.xls,.csv",
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            dictDefaultMessage: "Klik atau seret file Excel ke sini untuk mengimpor.",
            init: function () {
                this.on("success", function (file, response) {
                    // Tampilkan notifikasi sukses
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: 'Data berhasil diimport!',
                        showConfirmButton: false,
                        timer: 1500
                    });
    
                    // Tutup modal import
                    $('#importModal').modal('hide');
    
                    // Reload data atau halaman setelah sukses
                    setTimeout(() => window.location.reload(), 1500);
                });
    
                this.on("error", function (file, response) {
                    // Tampilkan notifikasi gagal
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: (typeof response === 'string') ? response : (response?.errors?.file?.[0] || response?.error || 'Gagal mengimpor file.'),
                        showConfirmButton: false,
                        timer: 2000
                    });
                });
            }
        });
    });
</script>
@endsection