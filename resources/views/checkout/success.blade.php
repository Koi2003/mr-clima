<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmación de compra | Mister Clima</title>
    <meta name="robots" content="noindex, nofollow">
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
    </div>
</header>

<main class="mc-section" style="padding:3rem 0">
    <div class="mc-container" style="max-width:620px;text-align:center">
        @if (!empty($confirmed))
            <div style="padding:2rem;background:rgba(22,163,74,.1);border:1.5px solid var(--mc-success);border-radius:var(--mc-radius)">
                <div style="font-size:3rem;color:var(--mc-success);margin-bottom:.75rem"><i class="fa-solid fa-circle-check"></i></div>
                <h1 style="font-size:1.4rem;margin-bottom:.5rem">¡Pago confirmado!</h1>
                <p style="color:var(--mc-text-secondary);margin-bottom:1rem">
                    Gracias por tu compra. Te contactaremos por teléfono para coordinar la entrega e instalación de tu lavadora.
                </p>
                @if (!empty($resumen))
                    <p style="font-size:.9rem;color:var(--mc-text);margin-bottom:1.25rem">
                        Pedido <strong>#{{ $resumen['pedido_id'] }}</strong> · Total <strong>{{ number_format($resumen['total'], 2, ',', '.') }} €</strong>
                    </p>
                @endif
                <a href="{{ url('/') }}" class="mc-btn mc-btn-primary mc-btn-block">
                    <i class="fa-solid fa-arrow-left"></i> Volver a la web
                </a>
            </div>
        @else
            <div style="padding:2rem;background:var(--mc-bg-alt);border:1px solid var(--mc-border);border-radius:var(--mc-radius)">
                <div style="font-size:3rem;color:var(--mc-warning);margin-bottom:.75rem"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <h1 style="font-size:1.4rem;margin-bottom:.5rem">Pago no completado</h1>
                <p style="color:var(--mc-text-secondary);margin-bottom:1.25rem">{{ $error ?? 'No se pudo confirmar el pago.' }}</p>
                <a href="{{ url('/') }}" class="mc-btn mc-btn-primary mc-btn-block">
                    <i class="fa-solid fa-arrow-left"></i> Volver a la web
                </a>
            </div>
        @endif
    </div>
</main>

<footer class="mc-footer" style="padding:1.5rem 0">
    <div class="mc-container" style="text-align:center;font-size:.8rem;color:var(--mc-text-muted)">
        <p>© {{ date('Y') }} Mister Clima S.L. — Todos los derechos reservados.</p>
    </div>
</footer>
</body>
</html>
