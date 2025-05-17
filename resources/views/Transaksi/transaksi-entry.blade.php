@extends('index')

@section('tittle', 'Add Data Transaksi')

@section('content')
<form action="{{ route('transaksi-entry.store') }}" method="post">
    @csrf
    <div class="card shadow-lg" style="border-radius: 2rem; background-color: #f9f9f9; color:#000; overflow: visible;">
            <div class="card-header" style="border-radius: 2rem;background-color: #fff; color:#000">
                <div class="row d-flex justify-content-between">
                    <h5 class="mt-2">General</h5>
                    <div class="row" onclick="location.href='{{ route('transaksi') }}'">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="1e1e1e" d="m3.55 12l7.35 7.35q.375.375.363.875t-.388.875t-.875.375t-.875-.375l-7.7-7.675q-.3-.3-.45-.675T.825 12t.15-.75t.45-.675l7.7-7.7q.375-.375.888-.363t.887.388t.375.875t-.375.875z"/></svg>
                        <h5 class="mr-4" >
                            Back
                        </h5>
                    </div>
                </div>
            </div>
            <div class="card-body" style="border-radius: 2rem; background-color: #f9f9f9; color:#000">
                    <div class="form-group">
                        <label for="tgl">Tanggal Transaksi</label>
                        <input type="date" class="form-control input @error('tgl') is-invalid @enderror" id="tgl_transaksi" name="tgl" value="{{date ('Y-m-d')}}">
                        @error('tgl')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>            
                    <div class="form-group">
                        <label for="totaltransaksi">Total Transaksi</label>
                        <input type="text" class="form-control input @error('tgl') is-invalid @enderror" id="totaltransaksi" name="totaltransaksi" value="{{old('totaltransaksi')}}" readonly>
                        @error('totaltransaksi')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>            
            </div>
    </div>
    <h5 class="font-weight-bold mt-5">Transaksi</h5>
    <div id="card-container">
        <div class="card shadow-lg mt-2 transaksi-card" style="border-radius: 2rem; overflow: hidden;">
            <div class="card-header d-flex justify-content-end" style="border-radius: 2rem;background-color: #fff; color:#000">
                    <a href="#" class="btn-delete-card">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path fill="#08060d" d="M18 19a3 3 0 0 1-3 3H8a3 3 0 0 1-3-3V7H4V4h4.5l1-1h4l1 1H19v3h-1zM6 7v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2V7zm12-1V5h-4l-1-1h-3L9 5H5v1zM8 9h1v10H8zm6 0h1v10h-1z"/></svg>
                    </a>
            </div>
            <div class="card-body" style="border-radius: 2rem; background-color: #f9f9f9; color:#000">
                <div class="form-row d-flex justify-content-between">
                    <div class="form-group col-md-3">
                        <label for="menu">Menu</label>
                        <input type="text" class="form-control" name="id_menu[]" id="menu" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="variant">Variant</label>
                        <select class="form-control @error('variant.*') is-invalid @enderror" name="variant[]" id="variant" onchange="updateMenu(this)">
                            <option value="">- Pilih Variant -</option>
                            @foreach ($variant as $vr)
                                <option value="{{ $vr->id_variant }}" data-menu="{{ $vr->menu->nama_menu }}" data-harga="{{ $vr->harga }}">
                                    {{ $vr->variant }}
                                </option>
                            @endforeach
                        </select>
                        @error('variant.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-3">
                        <label for="harga">Harga</label>
                        <input type="text" class="form-control" name="harga[]" readonly>
                    </div>
                    <div class="form-group col-md-3">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" class="form-control input @error('jumlah.*') is-invalid @enderror" name="jumlah[]" id="jumlah" oninput="calculateTotal(this)">
                        @error('jumlah.*')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group col-md-12">
                        <label for="total_harga">Total Harga</label>
                        <input type="text" class="form-control" name="total_harga[]" id="total_harga" readonly>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Tombol tambah card -->
    <div class="mt-3 d-flex justify-content-center mx-auto">
        <button type="button" class="btn btn-outline-primary" id="btn-tambah-card">Tambahkan Transaksi Menu</button>
    </div>
    <!-- Tombol Submit -->
    <div class="mt-3 d-flex justify-content-center mx-auto">
        <button type="submit" class="btn btn-primary" id="submit-btn">Simpan Transaksi</button>
    </div>
</form>

<script>
    // Fungsi untuk mengupdate menu dan harga
    function updateMenu(selectElement) {
        var formRow = selectElement.closest('.form-row');
        var menuField = formRow.querySelector('input[name="id_menu[]"]');
        var hargaField = formRow.querySelector('input[name="harga[]"]');
        var jumlahField = formRow.querySelector('input[name="jumlah[]"]');
        
        var selectedOption = selectElement.options[selectElement.selectedIndex];
        var menuName = selectedOption.getAttribute('data-menu');
        var harga = selectedOption.getAttribute('data-harga');
        
        menuField.value = menuName; // Update input menu dengan nama menu
        hargaField.value = harga;   // Update input harga dengan harga variant yang dipilih
        
        calculateTotal(jumlahField); // Hitung total harga ketika variant dipilih
    }

    // Fungsi untuk menghitung total harga di setiap baris
    function calculateTotal(jumlahField) {
        var formRow = jumlahField.closest('.form-row');
        var hargaField = formRow.querySelector('input[name="harga[]"]');
        var totalHargaField = formRow.querySelector('input[name="total_harga[]"]');
        
        var harga = parseFloat(hargaField.value) || 0;
        var jumlah = parseFloat(jumlahField.value) || 0;
        
        var totalHarga = harga * jumlah;
        
        totalHargaField.value = totalHarga.toFixed(0); // Set total harga dengan 2 desimal
        
        // Panggil fungsi untuk menghitung total transaksi
        calculateTotalPrice();
    }
    
    // Fungsi untuk menghitung total transaksi dari semua card
    function calculateTotalPrice() {
        var totalTransaksi = 0;

        // Looping melalui semua input total_harga[] dan menghitung total transaksi
        document.querySelectorAll('[name="total_harga[]"]').forEach(function (totalHargaInput) {
            const totalHarga = parseFloat(totalHargaInput.value) || 0;
            totalTransaksi += totalHarga;
        });

        // Update total transaksi
        document.getElementById('totaltransaksi').value = totalTransaksi.toFixed(0); // Menampilkan dengan 2 decimal
    }

    // Tombol tambah card
    document.getElementById("btn-tambah-card").addEventListener("click", function () {
        const container = document.getElementById("card-container");
        const cards = container.querySelectorAll(".transaksi-card");
        const lastCard = cards[cards.length - 1];
        const newCard = lastCard.cloneNode(true); // Kloning card

        // Kosongkan input yang tidak disabled
        newCard.querySelectorAll("input").forEach(input => {
            if (!input.disabled) {
                input.value = "";
            }
        });

        container.appendChild(newCard); // Tambahkan card baru
    });

    // Event untuk hapus card saat tombol delete diklik
    document.addEventListener("click", function (e) {
        if (e.target.closest(".btn-delete-card")) {
            e.preventDefault(); // Hindari reload dari href="#"
            const card = e.target.closest(".transaksi-card");
            const totalCards = document.querySelectorAll(".transaksi-card").length;

            if (totalCards > 1) {
                card.remove(); // Hapus card
                calculateTotalPrice(); // Update total transaksi setelah card dihapus
            } else {
                alert("Minimal satu transaksi harus ada.");
            }
        }
    });

    // Event listener untuk menghitung total harga setiap kali jumlah diubah
    document.querySelectorAll('[name="jumlah[]"]').forEach(function (jumlahInput) {
        jumlahInput.addEventListener('input', function () {
            calculateTotal(this);
        });
    });

    // Event listener untuk update menu dan harga saat variant dipilih
    document.querySelectorAll('[name="variant[]"]').forEach(function (variantSelect) {
        variantSelect.addEventListener('change', function () {
            updateMenu(this);
        });
    });

    // Inisialisasi ketika halaman pertama kali dimuat
    window.onload = function () {
        calculateTotalPrice();
    };
</script>
@endsection
