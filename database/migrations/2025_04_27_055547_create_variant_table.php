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
    Schema::create('variant', function (Blueprint $table) {
        $table->id('id_variant');
        $table->unsignedBigInteger('id_menu'); // <- ini
        $table->string('variant');
        $table->string('slug');
        $table->integer('harga');
        $table->timestamps();

        // foreign key ke menu
        $table->foreign('id_menu')->references('id_menu')->on('menu')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('variant');
    }
};
