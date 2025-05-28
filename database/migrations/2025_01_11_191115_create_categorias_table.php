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
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique()->index();
            $table->text('descripcion')->nullable();
            $table->text('imagen_url')->nullable();
            $table->boolean('activo')->default(1)->index();
            $table->foreignId('parent_id')->nullable()->constrained('categorias')->cascadeOnDelete()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorias');
    }
};
