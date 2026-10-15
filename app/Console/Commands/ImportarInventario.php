<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Enums\EstadoStock;
use App\Models\Producto;
use Illuminate\Console\Command;

/**
 * Importa el inventario de lavadoras desde un CSV exportado de Access/Excel.
 *
 * Uso:
 *   php artisan inventario:importar inventario.csv --dry-run   (solo simula)
 *   php artisan inventario:importar inventario.csv             (importa)
 *   php artisan inventario:importar inventario.csv --actualizar (actualiza existentes)
 *
 * El CSV puede venir con separador de coma, punto y coma o tabulador, y en
 * UTF-8 o Windows-1252 (lo tipico de Access). Las cabeceras se detectan por
 * nombre, con varios alias habituales, y los valores se limpian (precios con
 * coma decimal, "12 meses", "8 kg", "1.400 rpm"...).
 */
class ImportarInventario extends Command
{
    protected $signature = 'inventario:importar
                            {archivo : Ruta del CSV exportado desde Access/Excel}
                            {--dry-run : Simula la importacion sin guardar nada}
                            {--separador=auto : auto | coma | punto-y-coma | tabulador}
                            {--actualizar : Actualiza los productos que ya existan (misma marca y modelo)}';

    protected $description = 'Importa el inventario de lavadoras desde un CSV (exportado de Access/Excel)';

    /**
     * Alias de cabeceras. La clave es el campo del producto y el valor son los
     * nombres (ya normalizados) que se aceptan para esa columna.
     */
    private const MAPA = [
        'marca'                 => ['marca', 'brand', 'fabricante'],
        'modelo'                => ['modelo', 'model', 'referencia', 'ref', 'modelo_ref'],
        'precio'                => ['precio', 'pvp', 'price', 'precio_venta', 'importe', 'precio_euros', 'precio_eur', 'precio_euro'],
        'meses_garantia'        => ['meses_garantia', 'garantia', 'garantia_meses', 'garantia_mes', 'meses', 'warranty'],
        'capacidad_kg'          => ['capacidad_kg', 'capacidad', 'kg', 'carga', 'capacidad_carga'],
        'rpm'                   => ['rpm', 'centrifugado', 'centrifugado_rpm', 'revoluciones', 'velocidad'],
        'eficiencia_energetica' => ['eficiencia_energetica', 'eficiencia', 'clase', 'clase_energetica', 'etiqueta'],
        'descripcion'           => ['descripcion', 'description', 'observaciones', 'comentario', 'detalle'],
        'notas_tecnicas'        => ['notas_tecnicas', 'informe', 'informe_tecnico', 'reparaciones'],
        'condicion'             => ['condicion', 'tipo', 'estado_producto'],
        'estado_stock'          => ['estado_stock', 'estado', 'stock', 'situacion', 'vendida'],
        'garantia_tipo'         => ['garantia_tipo', 'tipo_garantia'],
    ];

    public function handle(): int
    {
        $ruta = (string) $this->argument('archivo');

        if (!is_file($ruta)) {
            $this->error("No existe el archivo: {$ruta}");

            return self::FAILURE;
        }

        $filas = $this->leerCsv($ruta, (string) $this->option('separador'));

        if ($filas === []) {
            $this->error('El archivo esta vacio o no se ha podido leer.');

            return self::FAILURE;
        }

        $cabeceras = array_shift($filas);
        $columnas = $this->mapearColumnas($cabeceras);

        $this->info("Archivo: {$ruta}");
        $this->line('Filas de datos: ' . count($filas));
        $this->line('Columnas detectadas:');

        $tabla = [];
        foreach ($columnas as $indice => $campo) {
            $tabla[] = [($cabeceras[$indice] ?? "#{$indice}"), $campo];
        }
        $this->table(['Columna del CSV', 'Campo en la web'], $tabla);

        $faltan = array_diff(['marca', 'modelo', 'precio'], array_values($columnas));
        if ($faltan !== []) {
            $this->error('Faltan columnas obligatorias: ' . implode(', ', $faltan));
            $this->line('Revisa que el CSV tenga al menos Marca, Modelo y Precio.');

            return self::FAILURE;
        }

        $dryRun = (bool) $this->option('dry-run');
        $actualizar = (bool) $this->option('actualizar');

        $creados = 0;
        $actualizados = 0;
        $saltados = 0;
        $previsualizacion = [];

        foreach ($filas as $numero => $fila) {
            if ($this->filaVacia($fila)) {
                continue;
            }

            $datos = $this->construirProducto($fila, $columnas);

            if ($datos === null) {
                $saltados++;
                $this->warn('  Fila ' . ($numero + 2) . ': sin marca o modelo, se salta.');

                continue;
            }

            if (count($previsualizacion) < 10) {
                $previsualizacion[] = [
                    $datos['marca'],
                    $datos['modelo'],
                    number_format((float) $datos['precio'], 2, ',', '.') . ' EUR',
                    $datos['meses_garantia'] . 'm',
                    $datos['capacidad_kg'] ?? '-',
                    $datos['rpm'] ?? '-',
                    $datos['condicion'] ?? 'reacondicionado',
                    $datos['estado_stock'],
                ];
            }

            if ($dryRun) {
                $creados++;

                continue;
            }

            $existente = Producto::where('marca', $datos['marca'])
                ->where('modelo', $datos['modelo'])
                ->first();

            if ($existente) {
                if ($actualizar) {
                    $existente->update($datos);
                    $actualizados++;
                } else {
                    $saltados++;
                }

                continue;
            }

            Producto::create($datos);
            $creados++;
        }

        $this->newLine();
        $this->line('Primeras filas leidas:');
        $this->table(
            ['Marca', 'Modelo', 'Precio', 'Garantia', 'Kg', 'RPM', 'Condicion', 'Estado'],
            $previsualizacion
        );

        $this->newLine();
        if ($dryRun) {
            $this->comment('*** SIMULACION (--dry-run): no se ha guardado nada ***');
            $this->line("Se crearian: {$creados} | Se saltarian: {$saltados}");
        } else {
            $this->info("Creados: {$creados} | Actualizados: {$actualizados} | Saltados: {$saltados}");
        }

        return self::SUCCESS;
    }

    /** Lee el CSV detectando codificacion y separador. */
    private function leerCsv(string $ruta, string $separador): array
    {
        $contenido = (string) file_get_contents($ruta);

        // Quita el BOM si lo hay.
        if (str_starts_with($contenido, "\xEF\xBB\xBF")) {
            $contenido = substr($contenido, 3);
        }

        // Access exporta a menudo en Windows-1252: si no es UTF-8 valido, se convierte.
        if (!mb_check_encoding($contenido, 'UTF-8')) {
            $contenido = mb_convert_encoding($contenido, 'UTF-8', 'Windows-1252');
        }

        $contenido = str_replace(["\r\n", "\r"], "\n", $contenido);
        $lineas = array_values(array_filter(explode("\n", $contenido), fn ($l) => trim($l) !== ''));

        if ($lineas === []) {
            return [];
        }

        $delimitador = match ($separador) {
            'coma'          => ',',
            'punto-y-coma'  => ';',
            'tabulador'     => "\t",
            default         => $this->detectarSeparador($lineas[0]),
        };

        $this->line('Separador detectado: ' . ($delimitador === "\t" ? 'tabulador' : $delimitador));

        return array_map(fn ($linea) => str_getcsv($linea, $delimitador, '"', '\\'), $lineas);
    }

    /** El separador mas frecuente en la primera linea. */
    private function detectarSeparador(string $linea): string
    {
        $cuenta = [
            ';'  => substr_count($linea, ';'),
            ','  => substr_count($linea, ','),
            "\t" => substr_count($linea, "\t"),
        ];

        arsort($cuenta);

        return (string) array_key_first($cuenta);
    }

    /** Devuelve [indiceDeColumna => campo] segun los alias conocidos. */
    private function mapearColumnas(array $cabeceras): array
    {
        $columnas = [];

        foreach ($cabeceras as $indice => $cabecera) {
            $campo = $this->buscarCampo($this->normalizar((string) $cabecera));

            if ($campo !== null && !in_array($campo, $columnas, true)) {
                $columnas[$indice] = $campo;
            }
        }

        return $columnas;
    }

    /**
     * Busca el campo de la web para una cabecera ya normalizada.
     *
     * Primero prueba la coincidencia exacta y, si falla, quita el sufijo de
     * unidad: asi "centrifugado_rpm" se reconoce como "centrifugado" y
     * "capacidad_maxima_kg" no se pierde.
     */
    private function buscarCampo(string $normalizada): ?string
    {
        if ($normalizada === '') {
            return null;
        }

        $candidatas = [$normalizada];

        $sinUnidad = preg_replace('/_(meses|mes|kg|rpm|eur|euros|euro|clase)$/', '', $normalizada) ?? '';

        if ($sinUnidad !== '' && $sinUnidad !== $normalizada) {
            $candidatas[] = $sinUnidad;
        }

        foreach ($candidatas as $candidata) {
            foreach (self::MAPA as $campo => $alias) {
                if (in_array($candidata, $alias, true)) {
                    return $campo;
                }
            }
        }

        return null;
    }

    /** Convierte una fila del CSV en los datos de un producto. */
    private function construirProducto(array $fila, array $columnas): ?array
    {
        $brutos = [];
        foreach ($columnas as $indice => $campo) {
            $brutos[$campo] = isset($fila[$indice]) ? trim((string) $fila[$indice]) : null;
        }

        $marca = $this->limpiarTexto($brutos['marca'] ?? null);
        $modelo = $this->limpiarTexto($brutos['modelo'] ?? null);
        $precio = $this->limpiarPrecio($brutos['precio'] ?? null);

        if ($marca === null || $modelo === null || $precio === null) {
            return null;
        }

        // Meses de garantia: obligatorio en la web (por defecto 12).
        $meses = $this->limpiarMeses($brutos['meses_garantia'] ?? null) ?? 12;
        $meses = max(1, min(36, $meses));

        $capacidad = $this->limpiarDecimal($brutos['capacidad_kg'] ?? null);
        $capacidad = $capacidad !== null ? max(1.0, min(25.0, $capacidad)) : null;

        $rpm = $this->limpiarEntero($brutos['rpm'] ?? null);
        $rpm = $rpm !== null ? max(400, min(2000, $rpm)) : null;

        $condicion = $this->normalizarCondicion($brutos['condicion'] ?? null);
        $estado = $this->normalizarEstado($brutos['estado_stock'] ?? null);

        // Si la columna "Estado" traia en realidad la condicion
        // (Nueva / Reacondicionada), se aprovecha para ese campo.
        if ($estado === null && $condicion === null) {
            $condicion = $this->normalizarCondicion($brutos['estado_stock'] ?? null);
        }

        return array_filter([
            'marca'                 => mb_substr($marca, 0, 60),
            'modelo'                => mb_substr($modelo, 0, 100),
            'precio'                => $precio,
            'meses_garantia'        => $meses,
            'capacidad_kg'          => $capacidad,
            'rpm'                   => $rpm,
            'eficiencia_energetica' => $this->normalizarEficiencia($brutos['eficiencia_energetica'] ?? null),
            'descripcion'           => $this->limpiarTexto($brutos['descripcion'] ?? null, 2000),
            'notas_tecnicas'        => $this->limpiarTexto($brutos['notas_tecnicas'] ?? null, 2000),
            'condicion'             => $condicion ?? 'reacondicionado',
            'estado_stock'          => $estado ?? EstadoStock::Disponible->value,
            'garantia_tipo'         => $this->limpiarTexto($brutos['garantia_tipo'] ?? null, 100),
        ], fn ($valor) => $valor !== null);
    }

    private function filaVacia(array $fila): bool
    {
        foreach ($fila as $celda) {
            if (trim((string) $celda) !== '') {
                return false;
            }
        }

        return true;
    }

    private function normalizar(string $texto): string
    {
        $texto = mb_strtolower(trim($texto));
        $texto = strtr($texto, ['á' => 'a', 'é' => 'e', 'í' => 'i', 'ó' => 'o', 'ú' => 'u', 'ñ' => 'n', 'ü' => 'u']);
        $texto = preg_replace('/[^a-z0-9]+/', '_', $texto) ?? '';

        return trim($texto, '_');
    }

    private function limpiarTexto(?string $valor, int $max = 255): ?string
    {
        if ($valor === null) {
            return null;
        }

        $valor = trim(strip_tags($valor));

        return $valor === '' ? null : mb_substr($valor, 0, $max);
    }

    private function limpiarPrecio(?string $valor): ?float
    {
        if ($valor === null || trim($valor) === '') {
            return null;
        }

        $v = preg_replace('/[^0-9,.\-]/', '', $valor) ?? '';

        if ($v === '' || $v === '-') {
            return null;
        }

        if (str_contains($v, ',') && str_contains($v, '.')) {
            // El ultimo separador que aparece es el decimal.
            if (strrpos($v, ',') > strrpos($v, '.')) {
                $v = str_replace('.', '', $v);
                $v = str_replace(',', '.', $v);
            } else {
                $v = str_replace(',', '', $v);
            }
        } elseif (str_contains($v, ',')) {
            $v = str_replace(',', '.', $v);
        }

        $numero = (float) $v;

        return $numero > 0 ? $numero : null;
    }

    private function limpiarDecimal(?string $valor): ?float
    {
        if ($valor === null || trim($valor) === '') {
            return null;
        }

        $v = preg_replace('/[^0-9,.]/', '', $valor) ?? '';
        $v = str_replace(',', '.', $v);
        $numero = (float) $v;

        return $numero > 0 ? $numero : null;
    }

    private function limpiarEntero(?string $valor): ?int
    {
        if ($valor === null || trim($valor) === '') {
            return null;
        }

        $v = preg_replace('/[^0-9]/', '', $valor) ?? '';
        $numero = (int) $v;

        return $numero > 0 ? $numero : null;
    }

    /** "12", "12 meses", "1 año" -> 12 */
    private function limpiarMeses(?string $valor): ?int
    {
        if ($valor === null || trim($valor) === '') {
            return null;
        }

        $numero = $this->limpiarEntero($valor);

        if ($numero === null) {
            return null;
        }

        $normalizado = $this->normalizar($valor);

        if (str_contains($normalizado, 'ano') || str_contains($normalizado, 'year')) {
            return $numero * 12;
        }

        return $numero;
    }

    private function normalizarCondicion(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $v = $this->normalizar($valor);

        if ($v === '') {
            return null;
        }

        if (str_contains($v, 'nuev') || str_contains($v, 'estrenar')) {
            return 'nuevo';
        }

        if (str_contains($v, 'reacond') || str_contains($v, 'seminuev') || str_contains($v, 'segunda')) {
            return 'reacondicionado';
        }

        return null;
    }

    private function normalizarEstado(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        $v = $this->normalizar($valor);

        return match (true) {
            str_contains($v, 'vendid')            => EstadoStock::Vendido->value,
            str_contains($v, 'reserv')            => EstadoStock::Reservado->value,
            str_contains($v, 'disponib'),
            str_contains($v, 'stock'),
            str_contains($v, 'libre'),
            $v === 'si'                           => EstadoStock::Disponible->value,
            default                               => null,
        };
    }

    private function normalizarEficiencia(?string $valor): ?string
    {
        if ($valor === null) {
            return null;
        }

        if (preg_match('/[A-G]/i', $valor, $coincidencias) === 1) {
            return strtoupper($coincidencias[0]);
        }

        return null;
    }
}
