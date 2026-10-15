<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Cookies | Mister Clima</title>
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
        <h1>Política de Cookies</h1>
        <p style="font-size:.85rem;color:var(--mc-text-muted);margin-bottom:2rem">Última actualización: {{ date('d/m/Y') }}</p>

        <h2 style="margin-top:2rem">1. Qué son las cookies</h2>
        <p>Las cookies son pequeños archivos que se almacenan en tu dispositivo al visitar una web y que permiten recordar cierta información (por ejemplo, tu preferencia de tema claro u oscuro).</p>

        <h2 style="margin-top:2rem">2. Cookies que utiliza esta web</h2>
        <p>Este sitio web utiliza únicamente cookies técnicas necesarias para su funcionamiento:</p>
        <ul style="margin:1rem 0 1rem 1.25rem;list-style:disc;display:flex;flex-direction:column;gap:.5rem">
            <li><strong>Cookie de sesión de Laravel</strong>: necesaria para el envío seguro de formularios y el panel de administración.</li>
            <li><strong>Preferencia de tema</strong>: se guarda en tu navegador para recordar si prefieres modo claro u oscuro (no es una cookie de seguimiento).</li>
        </ul>
        <p>No utilizamos cookies de publicidad ni de seguimiento de terceros.</p>

        <h2 style="margin-top:2rem">3. Cómo desactivar las cookies</h2>
        <p>Puedes bloquear o eliminar las cookies desde la configuración de tu navegador (Chrome, Firefox, Safari, Edge…). Ten en cuenta que, si desactivas las cookies técnicas, algunas funciones de la web (como el panel de administración) podrían no funcionar correctamente.</p>
    </div>
</main>

<footer class="mc-footer" style="padding:1.5rem 0">
    <div class="mc-container" style="text-align:center;font-size:.8rem;color:var(--mc-text-muted)">
        <p>© {{ date('Y') }} Mister Clima S.L. — Todos los derechos reservados. | <a href="{{ url('/aviso-legal') }}" style="color:var(--mc-text-muted)">Aviso Legal</a> · <a href="{{ url('/privacidad') }}" style="color:var(--mc-text-muted)">Privacidad</a> · <a href="{{ url('/cookies') }}" style="color:var(--mc-text-muted)">Cookies</a></p>
    </div>
</footer>
</body>
</html>
