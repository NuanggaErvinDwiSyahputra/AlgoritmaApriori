<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;
use App\Models\Variant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VariantController extends Controller
{
    public function index(Request $request) {
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'nama_menu'); // default sort by 'nama_menu'
        $sortOrder = $request->get('sort_order', 'asc'); // default ascending
    
        $query = Variant::query();
    
        // Untuk mencari berdasarkan search
        if ($search) {
            $query->where('variant', 'like', "%$search%")
                  ->orWhereHas('id_menu', function($q) use ($search) {
                      $q->where('nama_menu', 'like', "%$search%"); // Mencari berdasarkan nama_menu
                  })
                  ->orWhere('harga', 'like', "%$search%");
        }
    
        // Sort by based on the provided parameter (either 'nama_menu' or 'variant')
        if ($sortBy == 'nama_menu') {
            $query->join('menu', 'menu.id_menu', '=', 'variant.id_menu')
                  ->orderBy('menu.nama_menu', $sortOrder);
        } else {
            $query->orderBy('variant', $sortOrder);
        }
    
        // Handle pagination
        if ($perPage === 'all') {
            $variant = $query->get(); // All data, no pagination
        } else {
            $perPageInt = (int) $perPage;
            $variant = $query->paginate($perPageInt)->appends($request->all());
        }
    
        $lastVariant = Variant::orderBy('id_variant', 'desc')->first();
        $nextId = $lastVariant ? $lastVariant->id_variant + 1 : 1;
        $menu = Menu::all();
        return view('variant.variant', compact('variant', 'menu', 'nextId','sortBy', 'perPage', 'search', 'sortOrder'));
    }
    

    public function create()
    {
        $menu = Menu::all(); // Mendapatkan semua menu
        return view('variant.addvariant', compact('menu')); // Mengirim data menu ke view
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'menu' => 'required|exists:menu,nama_menu', // Memastikan nama menu ada di tabel menu
            'variant' => 'required|max:100|unique:variant,variant',
            'harga' => 'required|integer|digits_between:1,11',
        ],[
            'menu.required' => 'Nama menu harus diisi.',
            'menu.exists' => 'Nama menu tidak ditemukan.',
            'variant.required' => 'Nama variant harus diisi.',
            'variant.unique' => 'Variant sudah ada, silakan pilih variant lain.',
            'harga.required' => 'Harga harus diisi.',
            'harga.integer' => 'Harga harus berupa angka.',
            'harga.digits_between' => 'Harga tidak boleh lebih dari 11 digit.',
        ]);

        // Cari ID menu berdasarkan nama menu
        $menu = Menu::where('nama_menu', $request->menu)->first();
    
        if (!$menu) {
            return redirect()->back()->withErrors(['menu' => 'Menu tidak ditemukan']);
        }
    
        // Menyimpan data variant
        Variant::create([
            'id_menu' => $menu->id_menu,  // Menyimpan ID menu yang ditemukan
            'variant' => $request->variant,
            'harga' => $request->harga,
            'slug' => Str::slug($request->variant), // tambahkan slug di sini
        ]);
    
        return redirect()->route('variant')->with('success', 'Variant berhasil ditambahkan');
    }
    


    public function update(Request $request, $id)
{
    $request->validate([
        'menu' => 'required|exists:menu,id_menu',
        'variant' => 'required|max:100|unique:variant,variant,' . $id . ',id_variant',
        'harga' => 'required|integer|digits_between:1,11',
    ], [
        'variant.required' => 'Nama variant harus diisi.',
        'variant.unique' => 'Variant sudah ada, silakan pilih variant lain.',
        'harga.required' => 'Harga harus diisi.',
        'harga.integer' => 'Harga harus berupa angka.',
        'harga.digits_between' => 'Harga tidak boleh lebih dari 11 digit.',
    ]);

    $data = Variant::findOrFail($id);
    $menu = Menu::find($request->menu); // Cari berdasarkan id_menu

    if (!$menu) {
        return redirect()->back()->withErrors(['menu' => 'Menu tidak ditemukan']);
    }

    $data->update([
        'id_menu' => $menu->id_menu,
        'variant' => $request->variant,
        'harga' => $request->harga,
    ]);

    return redirect()->route('variant')->with('success', 'Variant berhasil diupdate');
}



    public function destroy(string $id_variant)
    {
        $data = Variant::find($id_variant);
        if ($data) {
            $data->delete();
            return redirect()->route('variant')->with('success', 'Variant berhasil dihapus');
        } else {
            return redirect()->route('variant')->with('error', 'Variant tidak ditemukan');
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected'); // Mendapatkan ID yang dipilih
    
        // Jika ada ID yang dipilih, lakukan penghapusan
        if ($ids) {
            Variant::whereIn('id_variant', $ids)->delete(); // Menghapus data berdasarkan ID
            return redirect()->route('variant')->with('success', 'Data yang dipilih berhasil dihapus.');
        }
    
        // Jika tidak ada data yang dipilih
        return redirect()->route('variant')->with('error', 'Tidak ada data yang dipilih.');
    }
}
