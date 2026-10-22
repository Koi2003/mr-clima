{{-- =====================================================================
     Email de aviso: nueva solicitud de servicio técnico.
     HTML con estilos inline (los clientes de correo ignoran los <style>
     externos) y sin imágenes, para que no caiga en spam y cargue rápido.
     Los colores son los de marca (naranja del logo), no la paleta por
     defecto de Tailwind: el correo también es imagen de la empresa.
     ===================================================================== --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
</head>
<body style="margin:0; padding:0; background:#FAF7F3; font-family: Arial, Helvetica, sans-serif;">

    <div style="max-width:560px; margin:0 auto; padding:24px 16px;">

        {{-- Cabecera --}}
        <div style="background:#BC4A1F; color:#FFFFFF; border-radius:8px 8px 0 0; padding:16px 24px;">
            <h1 style="margin:0; font-size:18px;">Mr. Clima — Nueva solicitud de servicio</h1>
        </div>

        {{-- Datos de la solicitud --}}
        <div style="background:#FFFFFF; border:1px solid #E4DAD0; border-top:0; padding:24px;">

            <p style="margin:0 0 16px; font-size:14px; color:#57504A;">
                Ha entrado una solicitud nueva desde la web
                el {{ $solicitud->created_at->format('d/m/Y') }}
                a las {{ $solicitud->created_at->format('H:i') }}.
            </p>

            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                <tr>
                    <td style="padding:8px 0; color:#8A8079; width:120px;">Cliente</td>
                    <td style="padding:8px 0; color:#1F1B18;"><strong>{{ $solicitud->cliente_nombre }}</strong></td>
                </tr>
                <tr style="border-top:1px solid #E4DAD0;">
                    <td style="padding:8px 0; color:#8A8079;">Teléfono</td>
                    <td style="padding:8px 0;">
                        {{-- Enlace tel: para llamar con un toque desde el móvil --}}
                        <a href="tel:{{ $solicitud->cliente_telefono }}" style="color:#BC4A1F; font-weight:bold; text-decoration:none;">
                            {{ $solicitud->cliente_telefono }}
                        </a>
                    </td>
                </tr>
                <tr style="border-top:1px solid #E4DAD0;">
                    <td style="padding:8px 0; color:#8A8079;">Aparato</td>
                    <td style="padding:8px 0; color:#1F1B18;">{{ str_replace('_', ' ', $solicitud->tipo_aparato) }}</td>
                </tr>
                <tr style="border-top:1px solid #E4DAD0;">
                    <td style="padding:8px 0; color:#8A8079; vertical-align:top;">Avería</td>
                    <td style="padding:8px 0; color:#1F1B18;">{{ $solicitud->descripcion_averia }}</td>
                </tr>
            </table>

        </div>

        {{-- Pie --}}
        <div style="background:#F3EDE6; border:1px solid #E4DAD0; border-top:0; border-radius:0 0 8px 8px; padding:12px 24px;">
            <p style="margin:0; font-size:12px; color:#8A8079;">
                Solicitud nº {{ $solicitud->id }} · Aviso automático de la web de Mr. Clima — no respondas a este correo.
            </p>
        </div>

    </div>

</body>
</html>
