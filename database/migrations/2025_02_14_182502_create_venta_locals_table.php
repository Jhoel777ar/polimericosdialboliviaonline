<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('venta_locals', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_usuario');
            $table->string('telefono');
            $table->string('CI')->nullable();
            $table->integer('cantidad');
            $table->dateTime('fecha_venta')->default(DB::raw('CURRENT_TIMESTAMP'))->index();
            $table->decimal('total', 10, 2);
            $table->enum('estado', ['entregado'])->default('entregado')->index();
            $table->json('detalle_compra');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_locals');
    }
};
