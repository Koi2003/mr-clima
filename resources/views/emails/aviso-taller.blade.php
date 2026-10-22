{{-- =====================================================================
     Aviso genérico al taller (nuevo pedido, nueva reserva…).
     Estilos inline: los clientes de correo ignoran los <style> externos.
     Colores tomados de los tokens de marca de resources/css/app.css.
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
            <h1 style="margin:0; font-size:18px;">{{ $titulo }}</h1>
        </div>

        {{-- Datos --}}
        <div style="background:#FFFFFF; border:1px solid #E4DAD0; border-top:0; padding:24px;">

            @if ($nota)
                <p style="margin:0 0 16px; font-size:14px; color:#57504A;">{{ $nota }}</p>
            @endif

            <table style="width:100%; border-collapse:collapse; font-size:14px;">
                @foreach ($filas as $etiqueta => $valor)
                    <tr style="border-top:1px solid #E4DAD0;">
                        <td style="padding:8px 0; color:#8A8079; width:150px; vertical-align:top;">{{ $etiqueta }}</td>
                        <td style="padding:8px 0; color:#1F1B18;">{{ $valor }}</td>
                    </tr>
                @endforeach
            </table>

        </div>

        {{-- Pie --}}
        <div style="background:#F3EDE6; border:1px solid #E4DAD0; border-top:0; border-radius:0 0 8px 8px; padding:12px 24px;">
            <p style="margin:0; font-size:12px; color:#8A8079;">
                Aviso automático de la web de Mr. Clima — no respondas a este correo.
            </p>
        </div>

    </div>

</body>
</html>
