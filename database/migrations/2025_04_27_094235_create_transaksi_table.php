<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');  
            $table->unsignedBigInteger('id_menu'); // <- ini
            $table->unsignedBigInteger('id_variant'); // <- ini
            $table->string('no_transaksi');  
            $table->date('tgl_transaksi');  
            $table->integer('harga'); 
            $table->integer('jumlah');  
            $table->decimal('total', 15, 2);  // Total harga (harga * jumlah)
            $table->timestamps();  

            $table->foreign('id_menu')->references('id_menu')->on('menu')->onDelete('cascade');
            $table->foreign('id_variant')->references('id_variant')->on('variant')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
