@extends('index')

@section('tittle','Menu')

@section('content')
    <div class="card shadow-lg" style="border-radius: 2rem; overflow: hidden;">
        <div class="card-body" style="border-radius: 2rem; background-color: #f9f9f9;">
            <div class="mt-2 mb-4">
                <h5>Dashboard > Menu</h5>
            </div>
            <div class="d-flex align-items-center justify-content-between mb-4 gap-7">  
                 {{-- Form Search --}}
                 <form method="get" action="{{ route('menu') }}" class="d-flex align-items-center" id="search-form" style="gap: 0.5rem; border-radius: 1rem;  transition: all 0.3s;">
                    <h5 for="per_page" class="mb-0 fw-semibold" style="color: #000">Search</h5>
                    <input type="text" name="search" id="search-input" class="form-control form-control-sm" value="{{ $search }}">
                </form>                             
                {{-- Tombol Add Variant --}}
                <button type="button" class="btn btn-primary px-4 py-2" data-toggle="modal" data-target="#exampleModal" style="border-radius: 1rem; box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); transition: all 0.3s;">
                    Add Menu
                </button>
            </div>
            <div class="table-responsive">
                <form method="POST" action="{{ route('menu.bulkDelete') }}" id="bulk-delete-form">
                    @csrf
                    @method('DELETE')
                    {{-- button delete all --}}
                    <button type="submit" class="btn btn-primary mb-3 d-none" id="delete-selected-btn" onclick="return confirm('Yakin ingin menghapus data yang dipilih?')">
                        Delete Selected
                    </button>
                    <table class="table table-bordered mb-0 text-center" style="border-radius: 1.5rem; overflow: hidden; border: 1px solid #ddd;">
                        <thead class="table-light" style="border-radius: 1.5rem 1.5rem 0 0;">
                            <tr>
                                <th scope="col" style="width: 50px;">
                                    <input type="checkbox" id="select-all">
                                </th>
                                <th style="width: 100px;">NO</th>
                                <th>Menu</th>
                                <th style="width: 250px;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($menu as $index => $m)
                            <tr style="transition: background-color 0.3s;">
                                <td>
                                    <input type="checkbox" name="selected[]" value="{{ $m->id_menu }}" class="row-checkbox">
                                </td>
                                <td>
                                    {{ $perPage === 'all' ? $loop->iteration : $menu->firstItem() + $loop->index }}
                                </td>                                      
                                <td>{{ $m->nama_menu }}</td>
                                <td> 
                                    <button type="button" class="btn btn-outline-primary px-4 py-2" data-toggle="modal" data-target="#target{{ $m->id_menu }}" style="border-radius: 1rem;">
                                        Edit
                                    </button>
                                    <button type="button" class="btn btn-outline-danger px-4 py-2"
                                    style="border-radius: 1rem; transition: all 0.3s;"
                                    onclick="if(confirm('Yakin ingin menghapus data yang dipilih?')) { window.location='{{ route('menu-destroy', $m->id_menu) }}'; }">
                                    Delete
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data Menu</td>
                            </tr>
                            @endforelse
                            </tr>
                        </tbody>                
                    </table>
                </form>
            </div>
            <div class="d-flex flex-wrap justify-content-between align-items-center mt-3 gap-3">
                {{-- Kiri: Info jumlah entri --}}
                <div class="text-muted">
                    Showing 
                    {{ $menu instanceof \Illuminate\Pagination\LengthAwarePaginator ? $menu->firstItem() : ($menu->count() ? 1 : 0) }}
                    to 
                    {{ $menu instanceof \Illuminate\Pagination\LengthAwarePaginator ? $menu->lastItem() : $menu->count() }}
                    of 
                    {{ $menu instanceof \Illuminate\Pagination\LengthAwarePaginator ? $menu->total() : $menu->count() }} 
                    entries
                </div>
                {{-- Dropdown show per page --}}
                <form method="GET" class="d-flex align-items-center mx-auto" style="gap: 8px;">
                    <label for="per_page" class="mb-0 fw-semibold text-secondary">Show</label>
                    <select name="per_page" id="per_page" class="form-select form-select-sm w-auto" onchange="this.form.submit()">
                        <option value="5" {{ $perPage == 5 ? 'selected' : '' }}>5</option>
                        <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                        <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>Semua</option>
                    </select>
                    <span class="text-secondary">entries</span>
                </form>
            
                {{-- Kanan: Pagination --}}
                @if($perPage !== 'all')
                    <div class="d-flex justify-content-end">
                        {{ $menu->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>   
        </div>
    </div>

    {{-- Add Data --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('menu-entry') }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Form Add Menu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="nama-menu" class="form-label">Menu</label>
                            <input type="text" class="form-control input @error('menu') is-invalid @enderror" id="nama-menu" name="menu" placeholder="Input Nama Menu">
                            @error('menu')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </form>
        </div>
    </div>    

    {{-- Edit Data --}}
    @foreach($menu as $m)
    <div class="modal fade" id="target{{ $m->id_menu }}" tabindex="-1" aria-labelledby="exampleModalLabel{{ $m->id_menu }}" aria-hidden="true">
        <div class="modal-dialog">
            <form action="{{ route('menu-update' , ['id' => $m->id_menu]) }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel{{ $m->id_menu }}">Form Edit Menu</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-4">
                            <label for="nama-menu-{{ $m->id_menu }}" class="form-label">Menu</label>
                            <input type="text" class="form-control input" id="nama-menu-{{ $m->id_menu }}" name="menu" placeholder="Input Nama Menu" value="{{ $m->nama_menu }}">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @endforeach
    
    @if (session('success'))
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        </script>
    @endif
    @if ($errors->any())
        <script>
            $(document).ready(function() {
                $('#exampleModal').modal('show');
            });
        </script>
    @endif

    {{-- delete all --}}
    <script>
        document.getElementById('select-all').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    </script>
    <script>
    const checkboxes = document.querySelectorAll('.row-checkbox');
    const deleteButton = document.getElementById('delete-selected-btn');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const anyChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);
            // Show or hide delete button based on whether any checkboxes are selected
            deleteButton.classList.toggle('d-none', !anyChecked);
        });
    });
    const selectAllCheckbox = document.getElementById('select-all');
    selectAllCheckbox.addEventListener('change', function() {
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
            }, 500);
        });
    </script>    
@endsection