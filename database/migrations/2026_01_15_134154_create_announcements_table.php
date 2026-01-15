<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();

            // Judul Pengumuman
            $table->string('judul');

            // Isi detail pengumuman (bisa panjang)
            $table->text('isi');

            // Tipe untuk menentukan warna (Info=Biru, Penting=Kuning, Libur=Merah)
            $table->enum('tipe', ['info', 'penting', 'libur'])->default('info');

            // Status aktif (1 = Tampil, 0 = Sembunyi)
            $table->boolean('is_active')->default(true);

            // Created_at & Updated_at
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('announcements');
    }
};
