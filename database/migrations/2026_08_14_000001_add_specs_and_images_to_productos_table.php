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
            $table->decimal('capacidad_kg', 4, 1)->unsigned()->nullable()->after('meses_garantia');
            $table->unsignedInteger('rpm')->nullable()->after('capacidad_kg');
            $table->string('eficiencia_energetica', 5)->nullable()->after('rpm');
            $table->text('notas_tecnicas')->nullable()->after('eficiencia_energetica');
            $table->json('imagenes')->nullable()->after('imagen_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->dropColumn([
                'capacidad_kg',
                'rpm',
                'eficiencia_energetica',
                'notas_tecnicas',
                'imagenes',
            ]);
        });
    }
};
