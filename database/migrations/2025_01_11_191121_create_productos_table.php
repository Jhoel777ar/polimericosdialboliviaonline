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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 150)->index();
            $table->text('descripcion')->nullable();
            $table->foreignId('categoria_id')->constrained('categorias')->cascadeOnDelete();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->text('imagen_url')->nullable();
            $table->enum('estado', ['disponible', 'descontinuado'])->default('disponible')->index();
            $table->foreignId('proveedor_id')->nullable()->constrained('proveedors')->cascadeOnDelete();
            $table->foreignId('color_id')->nullable()->constrained('colors')->cascadeOnDelete();
            $table->boolean('activo')->default(1)->index();
            $table->decimal('precio_estudiante', 10, 2)->nullable();
            $table->decimal('precio_proveedor', 10, 2)->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn('deleted_at');
        });
    }
};
