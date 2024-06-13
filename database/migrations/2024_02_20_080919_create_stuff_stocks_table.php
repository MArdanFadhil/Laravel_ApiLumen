<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //Method Up() berfungsi untuk mendefinisikan perubahan yang akan dilakukan pada skema database (menambah tabel, kolom, atau index pada tabel)
        //Method up() berjalan ketika menjalankan php artisan migrate
        Schema::create('stuff_stocks', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("stuff_id");
            $table->integer("total_availble");
            $table->integer("total_defec");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Method down() berfungsi unutk mendefinisikan pembatalan yang akan dilakukan pada skema database (mengembalikkan status pada posisi sebelum method up dijalankan)
        //php artisan migrate : rollback
        Schema::dropIfExists('stuff_stocks');
    }
};
