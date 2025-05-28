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
        Schema::create('venta_eliminadas', function (Blueprint $table) {
            $table->id();
            $table->integer('ID_Usuario')->nullable(); 
            $table->integer('Cantidad')->nullable();
            $table->dateTime('Fecha_Venta')->nullable(); 
            $table->decimal('Total', 10, 2)->nullable();
            $table->decimal('Totalcondescuento', 10, 2)->nullable();
            $table->enum('Estado', ['cancelado', 'pendiente', 'pagado', 'enviado', 'entregado'])->nullable(); // Puede ser nulo
            $table->enum('Método_Pago', ['tarjeta', 'paypal', 'transferencia', 'efectivo'])->nullable(); // Puede ser nulo
            $table->enum('tipo_entrega', ['envio', 'local'])->nullable();
            $table->json('detalles_venta')->nullable(); 
            $table->string('error_detalle')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_eliminadas');
    }
};
