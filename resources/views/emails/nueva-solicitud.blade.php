{{-- =====================================================================
     Email de aviso: nueva solicitud de servicio técnico.
     HTML con estilos inline (los clientes de correo ignoran los <style>
     externos) y sin imágenes, para que no caiga en spam y cargue rápido.
     ===================================================================== --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background:0f4f4f5; font-family: Arial, Helvetica, sans-serif;">

    <div style="max-width:560px; margin:0 auto; padding:24px 16px;">

        {{-- Cabecera --}}
        <div style="background:00369a1; color:0ffffff; border-radius:8px 8px 0 0; padding:16px 24px;">
            <h1 style="margin:0; font-size:18px;">🔧 Mr. Clima — Nueva solicitud de servicio</h1>
        </div>

        {{-- Datos de la solicitud --}}
        <div style="background:0ffffff; border:1px solid 0e4e4e7; border-top:0; padding:24px;">

            <p style="margin:0 0 16px; font-size:14px; color:03f3f46;">
                Ha entrado una solicitud nueva desde la web
                el {{ $solicitud->created_at->format('d/m/Y') }}
                a las {{ $solicitud->created_at->format('H:i') }}.
            </p>

            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <tr>
                    <td style="padding:8px 0; color:071717a; width:120px;">Cliente</td>
                    <td style="padding:8px 0; color:018181b;"><strong>{{ $solicitud->cliente_nombre }}</strong></td>
                </tr>
                <tr style="border-top:1px solid 0f4f4f5;">
                    <td style="padding:8px 0; color:071717a;">Teléfono</td>
                    <td style="padding:8px 0;">
                        {{-- Enlace tel: para llamar con un toque desde el móvil --}}
                        <a href="tel:{{ $solicitud->cliente_telefono }}" style="color:00369a1; font-weight:bold; text-decoration:none;">
                            {{ $solicitud->cliente_telefono }}
                        </a>
                    </td>
                </tr>
                <tr style="border-top:1px solid 0f4f4f5;">
                    <td style="padding:8px 0; color:071717a;">Aparato</td>
                    <td style="padding:8px 0; color:018181b;">{{ str_replace('_', ' ', $solicitud->tipo_aparato) }}</td>
                </tr>
                <tr style="border-top:1px solid 0f4f4f5;">
                    <td style="padding:8px 0; color:071717a; vertical-align:top;">Avería</td>
                    <td style="padding:8px 0; color:018181b;">{{ $solicitud->descripcion_averia }}</td>
                </tr>
            </table>

        </div>

        {{-- Pie --}}
        <div style="background:0fafafa; border:1px solid 0e4e4e7; border-top:0; border-radius:0 0 8px 8px; padding:12px 24px;">
            <p style="margin:0; font-size:12px; color:0a1a1aa;">
                Solicitud nº {{ $solicitud->id }} · Aviso automático de la web de Mr. Clima — no respondas a este correo.
            </p>
        </div>

    </div>

</body>
</html>
