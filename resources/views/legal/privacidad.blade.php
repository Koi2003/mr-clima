<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidad | Mister Clima</title>
    <meta name="robots" content="index, follow">
    <link rel="icon" type="image/png" href="{{ asset('images/logo-mrclima-isotipo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="/resources/css/app.css">
    @endif
</head>
<body>
<header class="mc-header">
    <div class="mc-container mc-header-inner">
        <a href="{{ url('/') }}" class="mc-logo">
            <img src="{{ asset('images/logo-mrclima-isotipo.png') }}" alt="Mr. Clima" style="height: 38px; width: auto; object-fit: contain;" onerror="this.style.display='none'">
            Mr.<span>Clima</span>
        </a>
        <a href="{{ url('/') }}" class="mc-nav-link"><i class="fa-solid fa-arrow-left"></i> Volver a la web</a>
    </div>
</header>

<main class="mc-section" style="padding:3rem 0">
    <div class="mc-container" style="max-width:840px">
        <h1>Política de Privacidad</h1>
        <p style="font-size:.85rem;color:var(--mc-text-muted);margin-bottom:2rem">Última actualización: {{ date('d/m/Y') }}</p>

        <h2 style="margin-top:2rem">1. Responsable del tratamiento</h2>
        <ul style="margin:1rem 0 1rem 1.25rem;list-style:disc;display:flex;flex-direction:column;gap:.5rem">
            <li><strong>Responsable:</strong> Mister Clima S.L. (CIF B53870739)</li>
            <li><strong>Domicilio:</strong> Calle Rafael Alberti, 8, 03160 Almoradí (Alicante)</li>
            <li><strong>Correo:</strong> <a href="mailto:info@misterclima.es">info@misterclima.es</a></li>
        </ul>

        <h2 style="margin-top:2rem">2. Datos que recogemos y finalidad</h2>
        <p>A través de los formularios de esta web recogemos los siguientes datos, siempre que tú nos los facilitas de forma voluntaria:</p>
        <ul style="margin:1rem 0 1rem 1.25rem;list-style:disc;display:flex;flex-direction:column;gap:.5rem">
            <li><strong>Solicitud de servicio técnico:</strong> nombre, teléfono, tipo de electrodoméstico y descripción de la avería. Finalidad: contactarte para atender tu solicitud.</li>
            <li><strong>Reserva de lavadora:</strong> se registra la reserva a tu nombre durante 48 horas. Finalidad: guardarte el producto.</li>
            <li><strong>Compra / envío:</strong> nombre, teléfono, dirección de entrega y localidad. Finalidad: tramitar el pedido y coordinar la entrega.</li>
        </ul>

        <h2 style="margin-top:2rem">3. Legitimación</h2>
        <p>La base legal del tratamiento es tu consentimiento (al marcar la casilla y enviar el formulario) y, en el caso de una compra, la ejecución del contrato de venta.</p>

        <h2 style="margin-top:2rem">4. Conservación de los datos</h2>
        <p>Los datos se conservan el tiempo necesario para atender tu solicitud o pedido y, posteriormente, durante los plazos legales de conservación aplicables (obligaciones fiscales y contables).</p>

        <h2 style="margin-top:2rem">5. Destinatarios</h2>
        <p>No cedemos tus datos a terceros, salvo obligación legal. Los datos se almacenan en los servidores del proveedor de hosting (Banahosting), dentro de la Unión Europea.</p>

        <h2 style="margin-top:2rem">6. Tus derechos</h2>
        <p>Puedes ejercer tus derechos de acceso, rectificación, supresión, oposición, limitación y portabilidad escribiendo a <a href="mailto:info@misterclima.es">info@misterclima.es</a>. También tienes derecho a reclamar ante la Agencia Española de Protección de Datos (AEPD) si consideras que no se han atendido correctamente tus derechos.</p>
    </div>
</main>

<footer class="mc-footer" style="padding:1.5rem 0">
    <div class="mc-container" style="text-align:center;font-size:.8rem;color:var(--mc-text-muted)">
        <p>© {{ date('Y') }} Mister Clima S.L. — Todos los derechos reservados. | <a href="{{ url('/aviso-legal') }}" style="color:var(--mc-text-muted)">Aviso Legal</a> · <a href="{{ url('/privacidad') }}" style="color:var(--mc-text-muted)">Privacidad</a> · <a href="{{ url('/cookies') }}" style="color:var(--mc-text-muted)">Cookies</a></p>
    </div>
</footer>
</body>
</html>
