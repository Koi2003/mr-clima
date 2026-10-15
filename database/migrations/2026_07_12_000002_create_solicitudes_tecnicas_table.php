<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_tecnicas', function (Blueprint $table): void {
            $table->id();
            $table->string('cliente_nombre', 100);
            $table->string('cliente_telefono', 15);
            $table->string('tipo_aparato', 50);
            $table->text('descripcion_averia');
            $table->enum('estado', ['pendiente', 'en_proceso', 'finalizada'])
                  ->default('pendiente');
            $table->timestamps();

            $table->index('estado'); // El panel admin filtra por estado.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_tecnicas');
    }
};
