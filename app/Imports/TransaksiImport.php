<?php

namespace App\Imports;

use App\Models\Menu;
use App\Models\Variant;
use App\Models\Transaksi;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Illuminate\Support\Facades\Log;

class TransaksiImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
       foreach ($rows as $row) {
            // Ambil nilai berdasarkan nama kolom dari header
            $noTransaksi = $row['no_transaksi'] ?? null;
            $tglTransaksi = $row['tgl_transaksi'] ?? null;
            $namaMenu = $row['nama_menu'] ?? null;
            $variantNama = $row['variant'] ?? null;
            $harga = $row['harga'] ?? 0;
            $jumlah = $row['jumlah'] ?? 0;
            $total = $row['total'] ?? 0;

            if (!$noTransaksi || !$tglTransaksi || !$namaMenu || !$variantNama) {
                continue;
            }

             try {
                if (is_numeric($tglTransaksi)) {
                    $parsedDate = Date::excelToDateTimeObject($tglTransaksi);
                } else {
                    $parsedDate = Carbon::parse($tglTransaksi);
                }
            } catch (\Exception $e) {
                $parsedDate = null;
            }

            if (!$parsedDate) {
                continue;
            }

            $menuSlug = \Str::slug($namaMenu);
            $variantSlug = \Str::slug($variantNama);

            $menu = Menu::where('slug', $menuSlug)->first();
            if (!$menu) continue;

            $variant = Variant::where('slug', $variantSlug)->where('id_menu', $menu->id_menu)->first();
            if (!$variant) continue;

            Transaksi::create([
                'no_transaksi' => $noTransaksi,
                'tgl_transaksi' => $parsedDate->format('Y-m-d'),
                'id_menu' => $menu->id_menu,
                'id_variant' => $variant->id_variant,
                'harga' => $harga,
                'jumlah' => $jumlah,
                'total' => $total,
            ]);
        }
    }
}

