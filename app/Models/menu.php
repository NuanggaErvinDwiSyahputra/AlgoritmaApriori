<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class menu extends Model
{
    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    protected $guarded = [];
    public $timestamps = true;
    protected $fillable = ['nama_menu', 'slug'];

    protected static function booted()
    {
        static::creating(function ($menu) {
            $menu->slug = \Str::slug($menu->nama_menu);
        });
    }
    public function variants()
    {
        return $this->hasMany(Variant::class, 'id_menu');
    }

    public function transactions()
    {
        return $this->hasMany(Transaksi::class, 'id_menu');
    }
}

