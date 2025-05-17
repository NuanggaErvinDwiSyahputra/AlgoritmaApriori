<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class transaksi extends Model
{
    use HasFactory;
    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi'; 
    public $timestamps = false;
    protected $fillable = [ 'id_transaksi', 'id_menu', 'id_variant', 'no_transaksi', 'tgl_transaksi', 'harga', 'jumlah', 'total'];

    // app/Models/Transaction.php
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu');
    }

    public function variant()
    {
        return $this->belongsTo(Variant::class, 'id_variant');
    }
}
