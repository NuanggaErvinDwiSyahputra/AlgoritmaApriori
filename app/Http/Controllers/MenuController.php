<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\menu;
use Illuminate\Support\Facades\Hash;

class MenuController extends Controller
{
    public function index(Request $request){
        $perPage = $request->get('per_page', 5);
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'nama_menu'); // default sort by 'menu'
        $sortOrder = $request->get('sort_order', 'asc'); // default ascending

        $query = menu::query();

        //untuk search
        if ($search) {
            $query->orWhere('nama_menu', 'like', "%$search%");
        }

        $query->orderBy('nama_menu', 'asc');

        // Handle pagination
        if ($perPage === 'all') {
            $menu = $query->get(); // All data, no pagination
        } else {
            $perPageInt = (int) $perPage;
            $menu = $query->paginate($perPageInt)->appends($request->all());
        }

        $lastMenu = Menu::orderBy('id_menu', 'desc')->first();
        $nextId = $lastMenu ? $lastMenu->id_menu + 1 : 1;
        return view('menu.menu', compact('menu', 'nextId', 'perPage', 'search', 'sortBy', 'sortOrder'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'menu' => 'required|max:100|unique:menu,nama_menu',
        ], [
            'menu.required' => 'Nama menu harus diisi.',
            'menu.max' => 'Nama menu tidak boleh lebih dari 100 karakter.',
            'menu.unique' => 'Nama menu sudah ada, silakan pilih nama lain.',
        ]);

        $lastMenu = Menu::orderBy('id_menu', 'desc')->first();
        $nextId = $lastMenu ? $lastMenu->id_menu + 1 : 1;

        menu::create([
            'id_menu' => $nextId,
            'nama_menu' => $request->menu,
            'slug' => \Str::slug($request->menu),
        ]);

        return redirect()->route('menu')->with('success', 'Menu berhasil ditambahkan');
        
    }

public function update(Request $request,  $id)
    {
        $this->validate($request, [
            'nama_menu' => $request->menu,
        ]);

        $data= menu::find($id);
        $data->update([
            'nama_menu' => $request->menu,
        ]);

        return redirect()->route('menu')->with('success', 'Menu berhasil diupdate');
    }

    public function destroy(string $id_menu)
    {
        $data = menu::find($id_menu);
        $data->delete();
        return redirect()->route('menu')->with('success', 'Menu berhasil dihapus');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('selected'); // Mendapatkan ID yang dipilih
    
        // Jika ada ID yang dipilih, lakukan penghapusan
        if ($ids) {
            menu::whereIn('id_menu', $ids)->delete(); // Menghapus data berdasarkan ID
            return redirect()->route('menu')->with('success', 'Data yang dipilih berhasil dihapus.');
        }
        // Jika tidak ada data yang dipilih
        return redirect()->route('menu')->with('error', 'Tidak ada data yang dipilih.');
    }    
}
