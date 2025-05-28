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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ID_Usuario')->constrained('users')->onDelete('cascade');
            $table->integer('Cantidad');
            $table->dateTime('Fecha_Venta')->default(DB::raw('CURRENT_TIMESTAMP'))->index();
            $table->decimal('Total', 10, 2);
            $table->decimal('Totalcondescuento', 10, 2);
            $table->enum('Estado', ['cancelado','pendiente', 'pagado', 'enviado', 'entregado'])->default('pendiente')->index();
            $table->enum('Método_Pago', ['tarjeta', 'paypal', 'transferencia', 'efectivo'])->index();
            $table->enum('tipo_entrega', ['envio', 'local'])->default('local')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
