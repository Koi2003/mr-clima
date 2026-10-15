<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table): void {
            $table->id();
            $table->string('marca', 60);
            $table->string('modelo', 100);
            $table->decimal('precio', 8, 2)->unsigned();
            $table->unsignedTinyInteger('meses_garantia')->default(6);
            $table->text('descripcion')->nullable();
            // Enum a nivel de BD: MySQL rechaza cualquier valor fuera del dominio.
            $table->enum('estado_stock', ['disponible', 'reservado', 'vendido'])
                  ->default('disponible');
            $table->string('imagen_url', 2048)->nullable();
            $table->timestamps();

            // Índices orientados a los filtros del catálogo público.
            $table->index(['estado_stock', 'precio']);
            $table->index('marca');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
