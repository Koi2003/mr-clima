<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
| Añade la fecha de caducidad de la reserva. Es NULL salvo cuando la
| lavadora está en estado 'reservado': ahí guarda el instante límite
| hasta el que se mantiene bloqueada para el cliente que la reservó.
*/
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('productos', function (Blueprint $table): void {
            // Nullable: solo tiene valor mientras exista una reserva activa.
            $table->timestamp('reservado_hasta')->nullable()->after('estado_stock');

            // Índice para que el comando de liberación localice rápido
            // las reservas caducadas sin recorrer toda la tabla.
            $table->index(['estado_stock', 'reservado_hasta']);
        });
    }

    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table): void {
            $table->dropIndex(['estado_stock', 'reservado_hasta']);
            $table->dropColumn('reservado_hasta');
        });
    }
};
