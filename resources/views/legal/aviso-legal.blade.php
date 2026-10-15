<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso Legal | Mister Clima</title>
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
        <h1>Aviso Legal</h1>
        <p style="font-size:.85rem;color:var(--mc-text-muted);margin-bottom:2rem">Última actualización: {{ date('d/m/Y') }}</p>

        <h2 style="margin-top:2rem">1. Titular del sitio web</h2>
        <p>En cumplimiento del artículo 10 de la Ley 34/2002, de 11 de julio, de Servicios de la Sociedad de la Información y de Comercio Electrónico (LSSI-CE), se informa de los siguientes datos:</p>
        <ul style="margin:1rem 0 1rem 1.25rem;list-style:disc;display:flex;flex-direction:column;gap:.5rem">
            <li><strong>Titular:</strong> Mister Clima S.L.</li>
            <li><strong>CIF/NIF:</strong> B53870739</li>
            <li><strong>Domicilio:</strong> Calle Rafael Alberti, 8, 03160 Almoradí (Alicante), España</li>
            <li><strong>Teléfono:</strong> <a href="tel:+34965700947">965 70 09 47</a></li>
            <li><strong>Correo electrónico:</strong> <a href="mailto:info@misterclima.es">info@misterclima.es</a></li>
        </ul>

        <h2 style="margin-top:2rem">2. Objeto</h2>
        <p>Este sitio web tiene por objeto informar sobre los servicios de reparación de electrodomésticos y climatización, así como la venta de lavadoras reacondicionadas con garantía ofrecidas por Mister Clima S.L.</p>

        <h2 style="margin-top:2rem">3. Propiedad intelectual e industrial</h2>
        <p>Todos los contenidos del sitio (textos, imágenes, logotipos, diseño y código) son titularidad de Mister Clima S.L. o de terceros que han autorizado su uso. Queda prohibida su reproducción, distribución o modificación sin autorización expresa.</p>

        <h2 style="margin-top:2rem">4. Responsabilidad</h2>
        <p>Mister Clima S.L. no se hace responsable del mal uso de los contenidos de este sitio web ni de los daños derivados de información de terceros. Los precios y la disponibilidad de los productos mostrados pueden variar sin previo aviso.</p>

        <h2 style="margin-top:2rem">5. Legislación aplicable y jurisdicción</h2>
        <p>La relación entre Mister Clima S.L. y el usuario se regirá por la normativa española vigente. Para cualquier controversia, las partes se someterán a los Juzgados y Tribunales que correspondan según la legislación aplicable.</p>
    </div>
</main>

<footer class="mc-footer" style="padding:1.5rem 0">
    <div class="mc-container" style="text-align:center;font-size:.8rem;color:var(--mc-text-muted)">
        <p>© {{ date('Y') }} Mister Clima S.L. — Todos los derechos reservados. | <a href="{{ url('/aviso-legal') }}" style="color:var(--mc-text-muted)">Aviso Legal</a> · <a href="{{ url('/privacidad') }}" style="color:var(--mc-text-muted)">Privacidad</a> · <a href="{{ url('/cookies') }}" style="color:var(--mc-text-muted)">Cookies</a></p>
    </div>
</footer>
</body>
</html>
