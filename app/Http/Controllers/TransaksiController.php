<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\menu;
use App\Models\variant;
use App\Models\transaksi;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TransaksiImport; 


    class TransaksiController extends Controller
    {
        public function index(Request $request) {
            $perPage = $request->get('per_page') ?? 5;
            $search = $request->get('search');
            $sortBy = $request->get('sort_by', 'nama_menu');
            $sortOrder = $request->get('sort_order', 'asc'); 
            $start = $request->get('start_date');
            $end = $request->get('end_date');
        
            $query = Transaksi::query();
        
            // Validasi dan filter tanggal
            if ($start && $end) {
                // Pastikan start_date <= end_date
                if ($start > $end) {
                    return redirect()->route('transaksi')->withErrors(['error' => 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.']);
                }
                $query->whereBetween('tgl_transaksi', [$start, $end]);
            }

            //untuk search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('no_transaksi', 'like', "%$search%")
                    ->orWhere('tgl_transaksi', 'like', "%$search%")
                    ->orWhereHas('menu', function ($q2) use ($search) {
                        $q2->where('nama_menu', 'like', "%$search%");
                    })
                    ->orWhereHas('variant', function ($q3) use ($search) {
                        $q3->where('variant', 'like', "%$search%");
                    });
                });
            }


            $query->orderBy('no_transaksi', 'asc')->orderBy('tgl_transaksi', 'asc')->orderBy('id_menu', 'asc');
        
            // Handle pagination
            if ($perPage === 'all') {
                $transaksi = $query->get(); // All data, no pagination
            } else {
                $perPageInt = (int) $perPage;
                $transaksi = $query->paginate($perPageInt)->appends($request->all());
            }

            $lastMenu = Transaksi::orderBy('id_transaksi', 'desc')->first();
            $menu = menu::all();
            $variant = variant::all();
            return view('transaksi.transaksi', compact('transaksi','variant', 'menu', 'perPage', 'search', 'sortBy', 'sortOrder', 'lastMenu', 'start', 'end'));
        }   

        public function create()
        {
            $menu = menu::all();
            $variant = variant::all();
            $transaksi = transaksi::all();
            return view('transaksi.transaksi-entry', compact('menu', 'variant', 'transaksi'));
        }

        public function store(Request $request)
        {
            
            //Validasi data yang diterima dari form
            $request->validate([
                'tgl'             => 'required|date',
                'variant'         => 'required|array',
                'variant.*'       => 'required|exists:variant,id_variant',
                'harga'           => 'required|array',
                'harga.*'         => 'required|numeric',
                'jumlah'          => 'required|array',
                'jumlah.*'        => 'required|integer',
                'total_harga'     => 'required|array',
                'total_harga.*'   => 'required|numeric',
            ],[
                'tgl' => 'Pilih Tanggal Transaksi',
                'variant.*' => 'Pilih Variant yang Tersedia',
                'jumlah.*' => 'Masukkan Jumlah (Berupa Angka)',
            ]);

            // Simpan data transaksi utama (kode dan tgl)
            $last = Transaksi::orderBy('id_transaksi', 'desc')->first();
            $nextNumber = $last ? ((int) str_replace('TRX', '', $last->no_transaksi)) + 1 : 1;
            $no_transaksi = 'TRX' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

            // Menyimpan detail transaksi untuk setiap item yang ada pada form
            foreach ($request->variant as $i => $id_variant) {
                $variant = \App\Models\Variant::with('menu')->findOrFail($id_variant);

                Transaksi::create([
                    'no_transaksi'  => $no_transaksi,
                    'tgl_transaksi' => $request->tgl,
                    'id_menu'     => $variant->menu->id_menu,
                    'id_variant'       => $variant->id_variant,
                    'harga'         => $request->harga[$i],
                    'jumlah'        => $request->jumlah[$i],
                    'total'         => $request->total_harga[$i],
                ]);
            }
            // Redirect ke halaman transaksi atau halaman lain setelah berhasil
            return redirect()->route('transaksi')->with('success', 'Transaksi berhasil disimpan!');
        }

        public function destroy(string $id_transaksi)
        {
            $data = transaksi::find($id_transaksi);
            $data->delete();
            return redirect()->route('transaksi')->with('success', 'Menu berhasil dihapus');
        }

        public function bulkDelete(Request $request)
        {
            $ids = $request->input('selected'); // Mendapatkan ID yang dipilih
        
            // Jika ada ID yang dipilih, lakukan penghapusan
            if ($ids) {
                transaksi::whereIn('id_transaksi', $ids)->delete(); // Menghapus data berdasarkan ID
                return redirect()->route('transaksi')->with('success', 'Data yang dipilih berhasil dihapus.');
            }
            // Jika tidak ada data yang dipilih
            return redirect()->route('transaksi')->with('error', 'Tidak ada data yang dipilih.');
        }    

        public function import(Request $request)
        {
            $request->validate([
                'file' => 'required|mimes:xlsx,csv,xls',
            ]);

           

            Excel::import(new TransaksiImport, $request->file('file'));

            return back()->with('success', 'Data transaksi berhasil diimport.');

        }
    }
