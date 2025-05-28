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
        Schema::create('comprobante_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ID_Venta')->constrained('ventas')->onDelete('cascade');
            $table->string('image_url');
            $table->timestamp('Fecha_Subida')->useCurrent();
            $table->decimal('monto_reportado', 10, 2);
            $table->enum('estado', ['pendiente', 'aprobado', 'rechazado'])->default('pendiente');
            $table->boolean('subido')->default(1);
            $table->text('description')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comprobante_pagos', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
