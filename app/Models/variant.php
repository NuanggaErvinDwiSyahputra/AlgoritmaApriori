<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class variant extends Model
{
    use HasFactory;
    protected $table = 'variant';
    protected $primaryKey = 'id_variant';
    protected $guarded = [];
    public $timestamps = true;
    protected $fillable = ['id_menu', 'variant', 'slug', 'harga'];
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }
    public function transactions()
    {
        return $this->hasMany(Transaksi::class, 'id_variant');
    }
}
