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
        Schema::create('envios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ID_Venta')->constrained('ventas')->onDelete('cascade');
            $table->string('Empresa_Envio', 150)->nullable();
            $table->string('Numero_Guia', 100)->nullable();
            $table->string('Dirrecion', 200)->nullable();
            $table->date('Fecha_Envio')->nullable()->index();
            $table->enum('Estado_Envio', ['pendiente', 'en tránsito', 'entregado'])->default('pendiente');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};
