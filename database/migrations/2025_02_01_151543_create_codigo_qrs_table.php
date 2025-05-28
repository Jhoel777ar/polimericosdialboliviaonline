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
        Schema::create('codigo_qrs', function (Blueprint $table) {
            $table->id();
            $table->string('qr_image_url');
            $table->decimal('monto_aceptado', 10, 2);
            $table->timestamp('fecha_expiracion')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codigo_qrs');
    }
};
