<?php

declare(strict_types=1);

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
        Schema::table('productos', function (Blueprint $table) {
            $table->enum('condicion', ['reacondicionado', 'nuevo'])->default('reacondicionado')->after('estado_stock');
            $table->string('garantia_tipo', 100)->nullable()->default('Taller Mr. Clima')->after('condicion');
        });

        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('producto_id')->nullable()->constrained('productos')->onDelete('set null');
            $table->string('cliente_nombre', 150);
            $table->string('cliente_telefono', 30);
            $table->string('cliente_email', 150)->nullable();
            $table->string('cliente_direccion', 255);
            $table->string('cliente_localidad', 100)->default('Alicante / Vega Baja');
            $table->decimal('total', 10, 2);
            $table->enum('metodo_pago', ['stripe', 'bizum', 'efectivo'])->default('stripe');
            $table->enum('estado_pago', ['pendiente', 'pagado', 'fallido'])->default('pendiente');
            $table->enum('estado_envio', ['pendiente', 'en_reparto', 'entregado'])->default('pendiente');
            $table->string('transaction_id', 150)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');

        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn(['condicion', 'garantia_tipo']);
        });
    }
};
