<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoStock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    /*
    | Campos rellenables por mass-assignment (formularios del panel admin).
    | 'reservado_hasta' queda fuera a propósito: solo lo gestiona la lógica
    | de reserva/liberación, nunca llega directamente de una petición.
    */
    protected $fillable = [
        'marca',
        'modelo',
        'precio',
        'meses_garantia',
        'capacidad_kg',
        'rpm',
        'eficiencia_energetica',
        'notas_tecnicas',
        'descripcion',
        'estado_stock',
        'condicion',
        'garantia_tipo',
        'imagen_url',
        'imagenes',
    ];

    /* Ocultos en la serialización JSON: el frontend no los necesita. */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /*
    | Default también en memoria: sin esto, un create() que no envíe
    | estado_stock dejaría el atributo a null en la instancia (el default
    | de MySQL solo aplica en la BD) y la serialización del enum fallaría.
    */
    protected $attributes = [
        'estado_stock' => 'disponible',
    ];

    /* Casts: tipado fuerte al leer/escribir atributos. */
    protected function casts(): array
    {
        return [
            'precio'                => 'decimal:2',
            'meses_garantia'        => 'integer',
            'capacidad_kg'          => 'float',
            'rpm'                   => 'integer',
            'imagenes'              => 'array',
            'estado_stock'          => EstadoStock::class,
            'reservado_hasta'       => 'datetime',
        ];
    }

    /* ---------------------------------------------------------------------
     | Scopes de consulta
     * -------------------------------------------------------------------*/

    /** Solo unidades a la venta: el catálogo público nunca muestra reservadas/vendidas. */
    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('estado_stock', EstadoStock::Disponible->value);
    }

    /** Filtro opcional por marca (no aplica nada si llega null o vacío). */
    public function scopeMarca(Builder $query, ?string $marca): Builder
    {
        // Comparación explícita con null: when($marca) trataría "0" como
        // "sin filtro" por ser falsy, ignorando la búsqueda del usuario.
        return $query->when(
            $marca !== null && $marca !== '',
            fn (Builder $q) => $q->where('marca', $marca)
        );
    }

    /** Filtro opcional por precio máximo (no aplica nada si llega null). */
    public function scopePrecioMax(Builder $query, ?float $precioMax): Builder
    {
        // Comparación explícita con null: when($precioMax) ignoraría el
        // valor 0 por ser falsy, devolviendo todo en vez de nada.
        return $query->when(
            $precioMax !== null,
            fn (Builder $q) => $q->where('precio', '<=', $precioMax)
        );
    }

    /** Reservas cuyo plazo ya venció: candidatas a volver a 'disponible'. */
    public function scopeReservasCaducadas(Builder $query): Builder
    {
        return $query
            ->where('estado_stock', EstadoStock::Reservado->value)
            ->whereNotNull('reservado_hasta')
            ->where('reservado_hasta', '<', now());
    }

    /* ---------------------------------------------------------------------
     | Lógica de dominio
     * -------------------------------------------------------------------*/

    public function estaDisponible(): bool
    {
        return $this->estado_stock === EstadoStock::Disponible;
    }

    /**
     * Marca la unidad como reservada y fija la caducidad de la reserva.
     * Debe invocarse SIEMPRE dentro de una transacción con lockForUpdate().
     *
     * @param int|null $minutos Reserva corta (p. ej. mientras el cliente paga
     *                          con tarjeta). Si es null se aplica el plazo
     *                          general de config('mrclima.reserva_horas').
     */
    public function reservar(?int $minutos = null): void
    {
        $this->estado_stock    = EstadoStock::Reservado;
        $this->reservado_hasta = $minutos
            ? Carbon::now()->addMinutes($minutos)
            : Carbon::now()->addHours(config('mrclima.reserva_horas'));
        $this->save();
    }

    /**
     * Devuelve la unidad al catálogo (reserva caducada o cancelada por el admin).
     */
    public function liberar(): void
    {
        $this->estado_stock    = EstadoStock::Disponible;
        $this->reservado_hasta = null;
        $this->save();
    }

    /**
     * Cierra la venta: pasa a 'vendido' y limpia la caducidad de la reserva.
     *
     * Se hace con asignación directa y NO con update([...]) porque
     * 'reservado_hasta' queda fuera de $fillable a propósito: solo la lógica
     * de dominio puede tocarlo. Un update() lo ignoraría en silencio.
     */
    public function marcarComoVendido(): void
    {
        $this->estado_stock    = EstadoStock::Vendido;
        $this->reservado_hasta = null;
        $this->save();
    }
}
