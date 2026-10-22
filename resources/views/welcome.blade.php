<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mr. Clima | Servicio Técnico y Lavadoras de Ocasión en Alicante</title>
    <meta name="description" content="Servicio técnico cercano y profesional de electrodomésticos y aire acondicionado en Alicante y Vega Baja. Venta de lavadoras reacondicionadas con 1 año de garantía.">

    <!-- Favicon: isotipo de Mr. Clima -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-mrclima-isotipo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-mrclima-isotipo.png') }}">

    <!-- SEO: Open Graph (compartir en WhatsApp/Facebook) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Mr. Clima">
    <meta property="og:title" content="Mr. Clima | Servicio Técnico y Lavadoras de Ocasión en Alicante">
    <meta property="og:description" content="Servicio técnico de electrodomésticos y venta de lavadoras reacondicionadas con 1 año de garantía en Almoradí y Vega Baja.">
    <meta property="og:url" content="https://misterclima.es">
    <meta property="og:image" content="{{ url('images/logo-mrclima.png') }}">
    <meta property="og:locale" content="es_ES">

    <!-- SEO: Twitter Card -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Mr. Clima | Servicio Técnico y Lavadoras de Ocasión en Alicante">
    <meta name="twitter:description" content="Servicio técnico de electrodomésticos y venta de lavadoras reacondicionadas con 1 año de garantía en Almoradí y Vega Baja.">
    <meta name="twitter:image" content="{{ url('images/logo-mrclima.png') }}">

    <!-- Datos estructurados: negocio local (SEO local en Google) -->
    @verbatim
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "LocalBusiness",
        "name": "Mr. Clima",
        "legalName": "Mister Clima S.L.",
        "vatID": "B53870739",
        "description": "Servicio técnico de electrodomésticos y climatización en Alicante. Venta de lavadoras reacondicionadas con 1 año de garantía.",
        "url": "https://misterclima.es",
        "telephone": "+34965700947",
        "email": "info@misterclima.es",
        "image": "https://misterclima.es/images/logo-mrclima.png",
        "priceRange": "€€",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Calle Rafael Alberti, 8",
            "addressLocality": "Almoradí",
            "addressRegion": "Alicante",
            "postalCode": "03160",
            "addressCountry": "ES"
        },
        "openingHoursSpecification": {
            "@type": "OpeningHoursSpecification",
            "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"],
            "opens": "09:00",
            "closes": "18:00"
        }
    }
    </script>
    @endverbatim

    <!-- Fonts: Outfit (headings) + Inter (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome 6.5.1 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- API Base URL -->
    <script>window.API_BASE = "{{ url('/api') }}";</script>

    <!-- Vite Assets -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="/resources/css/app.css">
        <script src="/resources/js/app.js" defer></script>
    @endif
</head>
<body>

<!-- ================================================================
     HEADER FAMILIAR
     ================================================================ -->
<header class="mc-header">
    <div class="mc-container mc-header-inner">
        <a href="#" class="mc-logo">
            <img src="{{ asset('images/logo-mrclima-isotipo.png') }}" alt="Mr. Clima" style="height: 38px; width: auto; object-fit: contain;" onerror="this.style.display='none'">
            Mr.<span>Clima</span>
        </a>

        <nav class="mc-nav" id="main-nav">
            <a href="#hero" class="mc-nav-link">Inicio</a>
            <a href="#servicios" class="mc-nav-link"><i class="fa-solid fa-list-check"></i> Servicios</a>
            <a href="#catalogo" class="mc-nav-link"><i class="fa-solid fa-soap"></i> Lavadoras</a>
            <a href="#faq" class="mc-nav-link"><i class="fa-solid fa-circle-question"></i> Preguntas</a>
            <a href="#contacto-directo" class="mc-nav-link"><i class="fa-solid fa-location-dot"></i> Dónde Estamos</a>
        </nav>

        <div style="display:flex;align-items:center;gap:.75rem">
            <a href="tel:+34965700947" class="mc-phone-btn">
                <i class="fa-solid fa-phone" style="font-size:1.1rem"></i> 965 70 09 47
            </a>
            <a href="https://wa.me/34685949486?text=Hola%20Mr.%20Clima,%20necesito%20asistencia%20t%C3%A9cnica." target="_blank" rel="noopener" class="mc-phone-btn">
                <i class="fa-brands fa-whatsapp" style="font-size:1.3rem"></i> 685 94 94 86
            </a>
            <button class="mc-theme-toggle" id="theme-toggle" aria-label="Cambiar tema">
                <i class="fa-solid fa-moon"></i>
            </button>
            <button class="mc-menu-toggle" id="menu-toggle" aria-label="Menú móvil">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>
</header>

<!-- ================================================================
     HERO CERCANO Y TRADICIONAL
     ================================================================ -->
<section class="mc-hero" id="hero">
    <div class="mc-container mc-hero-inner">
        <div>
            <div style="display:inline-flex;align-items:center;gap:.5rem;padding:.4rem .9rem;background:var(--mc-primary-light);border-radius:var(--mc-radius-full);color:var(--mc-primary);font-size:.85rem;font-weight:700;margin-bottom:1.25rem">
                <i class="fa-solid fa-wrench"></i> Taller propio en Almoradí · Servicio a domicilio en la Vega Baja
            </div>

            <h1 class="mc-hero-title">
                Reparación de lavadoras<br>
                <span style="color:var(--mc-primary)">y electrodomésticos a domicilio</span>
            </h1>

            <p class="mc-hero-subtitle">
                <strong>Que la lavadora te deje la ropa empapada un domingo no es plan.</strong>
                Vamos a tu casa, la revisamos y te decimos qué tiene — con el precio antes de tocar nada. Y si no compensa arreglarla, te llevamos una reacondicionada revisada en nuestro taller, con <strong>1 año de garantía por escrito</strong>.
            </p>

            <div class="mc-hero-badges">
                <span class="mc-badge"><i class="fa-solid fa-shield-halved" style="color:var(--mc-success)"></i> 1 Año de Garantía por Escrito</span>
                <span class="mc-badge"><i class="fa-solid fa-location-dot" style="color:var(--mc-accent)"></i> Taller Físico en Almoradí</span>
                <span class="mc-badge"><i class="fa-solid fa-user-check" style="color:var(--mc-primary)"></i> Atención Personal por la Dirección</span>
            </div>
        </div>

        <!-- FORMULARIO CERCANO (Te llamamos gratis) -->
        <div class="mc-contact-card" id="contacto">
            <h2 class="mc-contact-card-title"><i class="fa-solid fa-phone-volume" style="color:var(--mc-primary)"></i> ¿Te llamamos gratis?</h2>
            <p class="mc-contact-card-sub">Dinos qué se te ha roto y te llamamos <strong>sin compromiso</strong>:</p>

            <!-- Selector visual de servicio -->
            <div class="mc-service-selector">
                <button type="button" class="mc-service-btn active" data-aparato="lavadora">
                    <i class="fa-solid fa-soap" style="color:var(--mc-primary);font-size:1.1rem"></i> Lavadora
                </button>
                <button type="button" class="mc-service-btn" data-aparato="aire_acondicionado">
                    <i class="fa-solid fa-snowflake" style="color:var(--mc-accent);font-size:1.1rem"></i> Aire Acondicionado
                </button>
                <button type="button" class="mc-service-btn" data-aparato="frigorifico">
                    <i class="fa-solid fa-temperature-arrow-down" style="color:#5FA8AC;font-size:1.1rem"></i> Frigorífico
                </button>
                <button type="button" class="mc-service-btn" data-aparato="caldera">
                    <i class="fa-solid fa-fire-flame-curved" style="color:var(--mc-warning);font-size:1.1rem"></i> Caldera / Clima
                </button>
            </div>

            <form id="contact-form">
                <input type="hidden" name="tipo_aparato" id="form-tipo-aparato" value="lavadora">

                <div class="mc-form-group" style="margin-bottom:.75rem">
                    <label class="mc-label" for="cliente_nombre">Tu nombre *</label>
                    <input class="mc-input" type="text" id="cliente_nombre" name="cliente_nombre" placeholder="Ej: Maria José" required>
                    <span class="mc-form-error" id="err-cliente_nombre"></span>
                </div>

                <div class="mc-form-group" style="margin-bottom:.75rem">
                    <label class="mc-label" for="cliente_telefono">Teléfono de contacto *</label>
                    <input class="mc-input" type="tel" id="cliente_telefono" name="cliente_telefono" placeholder="Ej: 600 123 456" required>
                    <span class="mc-form-error" id="err-cliente_telefono"></span>
                </div>

                <div class="mc-form-group" style="margin-bottom:1rem">
                    <label class="mc-label" for="descripcion_averia">¿Qué le ocurre a tu electrodoméstico? *</label>
                    <textarea class="mc-textarea" id="descripcion_averia" name="descripcion_averia" placeholder="Ej: No centrifuga bien, tira agua o no enfría..." required></textarea>
                    <span class="mc-form-error" id="err-descripcion_averia"></span>
                </div>

                <div class="mc-checkbox-row" style="margin-bottom:1.25rem">
                    <input type="checkbox" id="politica_privacidad" required checked>
                    <label for="politica_privacidad">Acepto que me contacte Mister Clima S.L. para resolver mi consulta, conforme a la <a href="{{ url('/privacidad') }}" target="_blank">Política de Privacidad</a>.</label>
                </div>

                <div id="contact-errors"></div>
                <div id="contact-success" style="display:none;margin-bottom:1rem" class="mc-form-success"></div>

                <div style="display:flex;flex-direction:column;gap:.6rem">
                    <button type="submit" class="mc-btn mc-btn-primary mc-btn-lg mc-btn-block">
                        <i class="fa-solid fa-phone-volume"></i> Enviar y te llamamos gratis
                    </button>
                    <a href="https://wa.me/34685949486?text=Hola%20Mr.%20Clima,%20necesito%20asistencia%20t%C3%A9cnica%20para%20un%20electrodom%C3%A9stico." target="_blank" rel="noopener" class="mc-btn mc-btn-whatsapp mc-btn-block">
                        <i class="fa-brands fa-whatsapp" style="font-size:1.2rem"></i> Consulta directa por WhatsApp
                    </a>
                </div>
            </form>
        </div>
    </div>
</section>

<!-- ================================================================
     FRANJA DE CONFIANZA Y EXPERIENCIA
     ================================================================ -->
<section class="mc-stats-strip">
    <div class="mc-container">
        <div class="mc-stats-grid">
            <div class="mc-stat-box">
                <span class="mc-stat-num" style="color:var(--mc-primary)">+25 años</span>
                <span class="mc-stat-label">reparando electrodomésticos</span>
            </div>
            <div class="mc-stat-box">
                <span class="mc-stat-num" style="color:var(--mc-success)">1 año</span>
                <span class="mc-stat-label">de garantía por escrito</span>
            </div>
            <div class="mc-stat-box">
                <span class="mc-stat-num" style="color:var(--mc-accent)">Almoradí</span>
                <span class="mc-stat-label">y toda la Vega Baja</span>
            </div>
            <div class="mc-stat-box">
                <span class="mc-stat-num" style="color:var(--mc-primary)">0 €</span>
                <span class="mc-stat-label">por reservar una lavadora</span>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================
     QUÉ PODEMOS HACER POR TI (estructura tipo HomeServe)
     ================================================================ -->
<section class="mc-section" id="servicios">
    <div class="mc-container">
        <div class="mc-section-header">
            <h2><i class="fa-solid fa-list-check" style="color:var(--mc-primary)"></i> ¿Qué podemos hacer por ti?</h2>
            <p>Tres formas de ayudarte. Elige la tuya y te informamos <strong>sin compromiso</strong>.</p>
            <span class="mc-accent-line"></span>
        </div>

        <div class="mc-features-grid" style="grid-template-columns:repeat(auto-fit,minmax(290px,1fr))">
            <div class="mc-feature-card" style="text-align:left;align-items:flex-start">
                <div class="mc-feature-icon" style="margin:0 0 .75rem">
                    <i class="fa-solid fa-wrench" style="color:var(--mc-primary)"></i>
                </div>
                <h3>Reparación a domicilio</h3>
                <p><strong>Vamos a tu casa</strong> y te decimos qué tiene antes de tocar nada.</p>
                <p>Lavadoras, frigoríficos, lavavajillas y aire acondicionado. Y si la avería no compensa, <strong>te lo decimos claro</strong>.</p>
                <p style="color:var(--mc-primary);font-weight:700">Presupuesto sin compromiso</p>
                <a href="#contacto" class="mc-btn mc-btn-primary mc-btn-sm" style="margin-top:.75rem">
                    <i class="fa-solid fa-phone"></i> Pedir presupuesto
                </a>
            </div>

            <div class="mc-feature-card" style="text-align:left;align-items:flex-start">
                <div class="mc-feature-icon" style="margin:0 0 .75rem">
                    <i class="fa-solid fa-soap" style="color:var(--mc-accent)"></i>
                </div>
                <h3>Lavadoras reacondicionadas</h3>
                <p>Equipos <strong>revisados y probados en nuestro taller</strong>, higienizados y listos para usar.</p>
                <p>Con <strong>factura y 1 año de garantía por escrito</strong>, y te las llevamos e instalamos en casa.</p>
                <p style="color:var(--mc-primary);font-weight:700">Mira precios y modelos disponibles</p>
                <a href="#catalogo" class="mc-btn mc-btn-accent mc-btn-sm" style="margin-top:.75rem">
                    <i class="fa-solid fa-soap"></i> Ver lavadoras
                </a>
            </div>

            <div class="mc-feature-card" style="text-align:left;align-items:flex-start">
                <div class="mc-feature-icon" style="margin:0 0 .75rem">
                    <i class="fa-solid fa-snowflake" style="color:#5FA8AC"></i>
                </div>
                <h3>Aire acondicionado y clima</h3>
                <p>Reparación, puesta a punto y mantenimiento de <strong>aire acondicionado y calderas</strong>.</p>
                <p>Te lo dejamos funcionando y probado antes de irnos, con garantía por escrito.</p>
                <p style="color:var(--mc-primary);font-weight:700">Cuéntanos tu caso</p>
                <a href="#contacto" class="mc-btn mc-btn-outline mc-btn-sm" style="margin-top:.75rem">
                    <i class="fa-solid fa-snowflake"></i> Consultar
                </a>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================
     CATÁLOGO DE LAVADORAS DE OCASIÓN
     ================================================================ -->
<section class="mc-section" id="catalogo">
    <div class="mc-container">
        <div class="mc-section-header">
            <h2><i class="fa-solid fa-soap" style="color:var(--mc-primary)"></i> Lavadoras de Ocasión Reacondicionadas</h2>
            <p>Equipos seminuevos totalmente probados, higienizados y listos para llevar a tu casa con 1 año de garantía.</p>
            <span class="mc-accent-line"></span>
        </div>

        <!-- FILTROS SENCILLOS -->
        <div class="mc-filters">
            <div class="mc-form-group">
                <label class="mc-label" for="filter-marca">Filtrar por marca</label>
                <select class="mc-select" id="filter-marca">
                    <option value="">Todas las marcas</option>
                    <option value="Bosch">Bosch</option>
                    <option value="Balay">Balay</option>
                    <option value="Siemens">Siemens</option>
                    <option value="Whirlpool">Whirlpool</option>
                    <option value="LG">LG</option>
                    <option value="Samsung">Samsung</option>
                    <option value="Indesit">Indesit</option>
                </select>
            </div>
            <div class="mc-form-group">
                <label class="mc-label" for="filter-precio">Precio máximo (€)</label>
                <input class="mc-input" type="number" id="filter-precio" placeholder="Ej: 250" min="0" step="10">
            </div>
            <button class="mc-btn mc-btn-outline mc-btn-sm" id="btn-clear-filters" style="align-self:flex-end">
                <i class="fa-solid fa-rotate-left"></i> Limpiar filtros
            </button>
        </div>

        <!-- GRID DE PRODUCTOS -->
        <div class="mc-products-grid" id="products-grid">
            <div class="mc-empty">
                <div class="mc-spinner"></div>
                <p style="margin-top:1rem">Cargando lavadoras disponibles...</p>
            </div>
        </div>

        <!-- PAGINACIÓN -->
        <div class="mc-pagination" id="catalog-pagination"></div>
    </div>
</section>

<!-- ================================================================
     POR QUÉ ELEGIRNOS
     ================================================================ -->
<section class="mc-section" id="ventajas" style="background:var(--mc-bg-alt)">
    <div class="mc-container">
        <div class="mc-section-header">
            <h2><i class="fa-solid fa-clipboard-check" style="color:var(--mc-accent)"></i> Cómo trabajamos</h2>
            <p>Sin intermediarios ni call centers: te atiende directamente la dirección de Mister Clima.</p>
            <span class="mc-accent-line"></span>
        </div>

        <div class="mc-features-grid">
            <div class="mc-feature-card">
                <div class="mc-feature-icon">
                    <i class="fa-solid fa-wrench" style="color:var(--mc-primary)"></i>
                </div>
                <h3>Reparaciones con Garantía</h3>
                <p>Arreglamos lavadoras, frigoríficos y aire acondicionado utilizando repuestos originales.</p>
            </div>

            <div class="mc-feature-card">
                <div class="mc-feature-icon">
                    <i class="fa-solid fa-file-signature" style="color:var(--mc-success)"></i>
                </div>
                <h3>1 Año de Garantía por Escrito</h3>
                <p>Todas nuestras lavadoras reacondicionadas incluyen factura y garantía sellada por nuestro taller.</p>
            </div>

            <div class="mc-feature-card">
                <div class="mc-feature-icon">
                    <i class="fa-solid fa-truck" style="color:var(--mc-accent)"></i>
                </div>
                <h3>Transporte e Instalación</h3>
                <p>Te la llevamos a casa en Almoradí, Torrevieja, Orihuela y alrededores, dejándola probada e instalada.</p>
            </div>

            <div class="mc-feature-card">
                <div class="mc-feature-icon">
                    <i class="fa-solid fa-user-shield" style="color:var(--mc-primary)"></i>
                </div>
                <h3>Atención Personal por la Dirección</h3>
                <p>Al llamar o escribir hablas directamente con la gerencia de Mr. Clima, asegurándote máxima honestidad, trato cercano y compromiso en cada servicio.</p>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================
     SECCIÓN CONTACTO DIRECTO / DÓNDE ESTAMOS
     ================================================================ -->
<section class="mc-section" id="contacto-directo">
    <div class="mc-container">
        <div class="mc-contact-card" style="max-width:900px;margin:0 auto">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:2rem;align-items:center">
                <div>
                    <h2 style="font-size:1.6rem;margin-bottom:.5rem"><i class="fa-solid fa-location-dot" style="color:var(--mc-primary)"></i> Ven a visitarnos o llámanos</h2>
                    <p style="color:var(--mc-text-secondary);margin-bottom:1.5rem">Estamos a tu disposición para cualquier consulta sobre reparaciones o venta de electrodomésticos.</p>

                    <div style="display:flex;flex-direction:column;gap:1rem">
                        <div style="display:flex;align-items:center;gap:.75rem">
                            <div style="width:2.5rem;height:2.5rem;background:var(--mc-primary-light);border-radius:var(--mc-radius-full);display:flex;align-items:center;justify-content:center;color:var(--mc-primary)">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <div>
                                <strong style="display:block;font-size:.85rem;color:var(--mc-text-muted)">Teléfono de atención</strong>
                                <a href="tel:+34965700947" style="font-size:1.2rem;font-weight:700;color:var(--mc-text)">965 70 09 47</a>
                            </div>
                        </div>

                        <div style="display:flex;align-items:center;gap:.75rem">
                            <div style="width:2.5rem;height:2.5rem;background:var(--mc-primary-light);border-radius:var(--mc-radius-full);display:flex;align-items:center;justify-content:center;color:var(--mc-primary)">
                                <i class="fa-brands fa-whatsapp"></i>
                            </div>
                            <div>
                                <strong style="display:block;font-size:.85rem;color:var(--mc-text-muted)">WhatsApp directo</strong>
                                <a href="https://wa.me/34685949486" target="_blank" rel="noopener" style="font-size:1.1rem;font-weight:700;color:var(--mc-text)">685 94 94 86</a>
                            </div>
                        </div>

                        <div style="display:flex;align-items:center;gap:.75rem">
                            <div style="width:2.5rem;height:2.5rem;background:var(--mc-accent-light);border-radius:var(--mc-radius-full);display:flex;align-items:center;justify-content:center;color:var(--mc-accent)">
                                <i class="fa-solid fa-map-pin"></i>
                            </div>
                            <div>
                                <strong style="display:block;font-size:.85rem;color:var(--mc-text-muted)">Ubicación</strong>
                                <span style="font-weight:600;color:var(--mc-text)">C/ Rafael Alberti 8, Almoradí (Alicante)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div style="padding:1.5rem;background:var(--mc-bg-alt);border-radius:var(--mc-radius);border:1px solid var(--mc-border);text-align:center">
                    <i class="fa-solid fa-store" style="font-size:3rem;color:var(--mc-primary);margin-bottom:1rem"></i>
                    <h3 style="font-size:1.2rem;margin-bottom:.5rem">Atención Presencial y Telefónica</h3>
                    <p style="font-size:.9rem;color:var(--mc-text-secondary);margin-bottom:1rem">Horario: Lunes a Viernes de 9:00 a 18:00h</p>
                    <a href="tel:+34965700947" class="mc-btn mc-btn-primary mc-btn-block">
                        <i class="fa-solid fa-phone"></i> Llamar ahora a tienda
                    </a>
                </div>
            </div>

            <!-- Mapa de Google -->
            <div style="margin-top:1.5rem;border-radius:var(--mc-radius);overflow:hidden;border:1px solid var(--mc-border)">
                <iframe src="https://www.google.com/maps?q=Calle%20Rafael%20Alberti%208%2C%2003160%20Almorad%C3%AD%2C%20Alicante&output=embed" width="100%" height="300" style="border:0;display:block" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Ubicación de Mister Clima en Almoradí"></iframe>
            </div>
        </div>
    </div>
</section>

<!-- ================================================================
     PREGUNTAS FRECUENTES
     ================================================================ -->
<section class="mc-section" id="faq" style="background:var(--mc-bg-alt)">
    <div class="mc-container" style="max-width:860px">
        <div class="mc-section-header">
            <h2><i class="fa-solid fa-circle-question" style="color:var(--mc-primary)"></i> Preguntas frecuentes</h2>
            <p>Las dudas que más nos preguntáis por teléfono.</p>
            <span class="mc-accent-line"></span>
        </div>

        <div style="display:flex;flex-direction:column;gap:.6rem">
            <details style="background:var(--mc-surface);border:1px solid var(--mc-border);border-radius:var(--mc-radius);padding:1rem 1.15rem">
                <summary style="cursor:pointer;font-weight:700">¿Qué garantía tienen las lavadoras reacondicionadas?</summary>
                <p style="margin-top:.7rem;color:var(--mc-text-secondary)">Llevan <strong>1 año de garantía por escrito</strong> y su factura. Antes de entregártela la hemos revisado, probado e higienizado en nuestro taller.</p>
            </details>

            <details style="background:var(--mc-surface);border:1px solid var(--mc-border);border-radius:var(--mc-radius);padding:1rem 1.15rem">
                <summary style="cursor:pointer;font-weight:700">¿Me la lleváis a casa?</summary>
                <p style="margin-top:.7rem;color:var(--mc-text-secondary)">Sí. Te la llevamos e <strong>instalamos en Almoradí, Torrevieja, Orihuela y alrededores</strong>, y te la dejamos probada antes de irnos.</p>
            </details>

            <details style="background:var(--mc-surface);border:1px solid var(--mc-border);border-radius:var(--mc-radius);padding:1rem 1.15rem">
                <summary style="cursor:pointer;font-weight:700">¿Qué marcas reparáis?</summary>
                <p style="margin-top:.7rem;color:var(--mc-text-secondary)">Trabajamos con todas las marcas habituales del hogar: Bosch, Balay, Siemens, LG, Samsung, Whirlpool, Indesit… Si tienes dudas con la tuya, llámanos y te lo decimos.</p>
            </details>

            <details style="background:var(--mc-surface);border:1px solid var(--mc-border);border-radius:var(--mc-radius);padding:1rem 1.15rem">
                <summary style="cursor:pointer;font-weight:700">¿Y si no merece la pena arreglarla?</summary>
                <p style="margin-top:.7rem;color:var(--mc-text-secondary)"><strong>Te lo decimos claro y sin marearte.</strong> Nosotros no ganamos nada teniéndote esperando: si no compensa, te lo decimos y puedes quedarte con una de nuestras lavadoras reacondicionadas, con garantía.</p>
            </details>

            <details style="background:var(--mc-surface);border:1px solid var(--mc-border);border-radius:var(--mc-radius);padding:1rem 1.15rem">
                <summary style="cursor:pointer;font-weight:700">¿Cómo puedo pagar?</summary>
                <p style="margin-top:.7rem;color:var(--mc-text-secondary)">Como te sea más cómodo: <strong>con tarjeta desde la web</strong> o <strong>en mano al recibirla</strong> en tu casa.</p>
            </details>

            <details style="background:var(--mc-surface);border:1px solid var(--mc-border);border-radius:var(--mc-radius);padding:1rem 1.15rem">
                <summary style="cursor:pointer;font-weight:700">¿Atendéis urgencias?</summary>
                <p style="margin-top:.7rem;color:var(--mc-text-secondary)">Llámanos al <a href="tel:+34965700947">965 70 09 47</a> y te decimos cuándo podemos ir. Normalmente intentamos acudir lo antes posible.</p>
            </details>
        </div>

        <div style="text-align:center;margin-top:2rem">
            <p style="margin-bottom:.85rem;color:var(--mc-text-secondary)">¿Te queda alguna duda? Te la resolvemos por teléfono, sin compromiso.</p>
            <a href="tel:+34965700947" class="mc-btn mc-btn-primary mc-btn-lg">
                <i class="fa-solid fa-phone"></i> Llámanos: 965 70 09 47
            </a>
        </div>
    </div>
</section>

<!-- ================================================================
     FOOTER
     ================================================================ -->
<footer class="mc-footer">
    <div class="mc-container">
        <div class="mc-footer-grid">
            <div>
                <a href="#" class="mc-logo" style="margin-bottom:1rem">
                    <img src="{{ asset('images/logo-mrclima-isotipo.png') }}" alt="Mr. Clima" style="height: 34px; width: auto; object-fit: contain;" onerror="this.style.display='none'">
                    Mr.<span>Clima</span>
                </a>
                <p>Servicio técnico profesional de electrodomésticos y climatización en Alicante. Más de 25 años de experiencia al servicio de tu hogar.</p>
            </div>
            <div>
                <h4>Servicios</h4>
                <ul class="mc-footer-links">
                    <li><a href="#catalogo">Lavadoras reacondicionadas</a></li>
                    <li><a href="#contacto">Reparación de averías</a></li>
                    <li><a href="#ventajas">Servicio técnico</a></li>
                    <li><a href="#contacto">Placas electrónicas</a></li>
                </ul>
            </div>
            <div>
                <h4>Contacto</h4>
                <ul class="mc-footer-links">
                    <li><a href="tel:+34965700947"><i class="fa-solid fa-phone" style="margin-right:.4rem"></i> 965 70 09 47</a></li>
                    <li><a href="mailto:info@misterclima.es"><i class="fa-solid fa-envelope" style="margin-right:.4rem"></i> info@misterclima.es</a></li>
                    <li><i class="fa-solid fa-location-dot" style="margin-right:.4rem"></i> C/ Rafael Alberti 8, Almoradí (Alicante)</li>
                    <li><i class="fa-solid fa-clock" style="margin-right:.4rem"></i> Lun-Vie 9:00 - 18:00</li>
                </ul>
            </div>
        </div>
        <div class="mc-footer-bottom">
            <p>© {{ date('Y') }} Mister Clima S.L. Todos los derechos reservados. | <a href="{{ url('/aviso-legal') }}" style="color:var(--mc-text-muted);text-decoration:underline">Aviso Legal</a> | <a href="{{ url('/privacidad') }}" style="color:var(--mc-text-muted);text-decoration:underline">Privacidad</a> | <a href="{{ url('/cookies') }}" style="color:var(--mc-text-muted);text-decoration:underline">Cookies</a> | <a href="{{ url('/admin') }}" style="color:var(--mc-text-muted);text-decoration:underline">Acceso Administración</a></p>
        </div>
    </div>
</footer>

<!-- ================================================================
     RESERVE & HYBRID CHECKOUT MODAL
     ================================================================ -->
<div class="mc-modal-overlay" id="reserve-modal">
    <div class="mc-modal" style="max-width:640px">
        <div class="mc-modal-header">
            <h3><i class="fa-solid fa-cart-shopping"></i> Ficha y Opciones de Compra</h3>
            <button class="mc-modal-close" onclick="closeModal('reserve-modal')">✕</button>
        </div>
        <div class="mc-modal-body">
            <!-- Galería de fotos reales -->
            <div id="reserve-gallery-area" style="margin-bottom:1.25rem"></div>

            <!-- Detalles y Especificaciones -->
            <div id="reserve-product-info" style="margin-bottom:1.25rem"></div>

            <p class="mc-form-error" id="reserve-error"></p>

            <!-- OPCIÓN 1: RESERVA 48H -->
            <div style="margin-bottom:1.25rem;padding:1rem;background:var(--mc-bg-alt);border-radius:var(--mc-radius);border:1px solid var(--mc-border)">
                <h4 style="font-size:1rem;margin-bottom:.35rem;color:var(--mc-primary)"><i class="fa-solid fa-calendar-check"></i> Opción 1: Reserva Gratis 48 Horas</h4>
                <p style="font-size:.85rem;color:var(--mc-text-secondary);margin-bottom:.75rem">Guarda el producto a tu nombre sin poner tarjeta. Te llamamos o nos escribes por WhatsApp para confirmar.</p>
                <button class="mc-btn mc-btn-accent mc-btn-block" id="btn-confirm-reserve">
                    <i class="fa-solid fa-calendar-check"></i> Confirmar Reserva 48h Gratis
                </button>
                <div id="reserve-whatsapp-container" style="display:none;margin-top:1rem"></div>
            </div>

            <!-- OPCIÓN 2: COMPRA DIRECTA ONLINE -->
            <div style="padding:1rem;background:var(--mc-surface);border-radius:var(--mc-radius);border:1.5px solid var(--mc-primary)">
                <h4 style="font-size:1rem;margin-bottom:.35rem;color:var(--mc-text)"><i class="fa-solid fa-truck-ramp-box"></i> Opción 2: Compra Directa con Envío a Domicilio</h4>
                <p style="font-size:.85rem;color:var(--mc-text-secondary);margin-bottom:1rem">Compra online con tarjeta o paga al recibir tu lavadora en casa. Te la entregamos e instalamos en Alicante o Vega Baja.</p>

                <form id="buy-order-form">
                    <div class="mc-form-row" style="margin-bottom:.75rem">
                        <div class="mc-form-group">
                            <label class="mc-label">Tu Nombre *</label>
                            <input class="mc-input" type="text" name="cliente_nombre" placeholder="Ej: María García" required>
                        </div>
                        <div class="mc-form-group">
                            <label class="mc-label">Teléfono *</label>
                            <input class="mc-input" type="tel" name="cliente_telefono" placeholder="Ej: 600 123 456" required>
                        </div>
                    </div>
                    <div class="mc-form-group" style="margin-bottom:.75rem">
                        <label class="mc-label">Dirección Completa de Entrega *</label>
                        <input class="mc-input" type="text" name="cliente_direccion" placeholder="Calle, número, piso y puerta" required>
                    </div>
                    <div class="mc-form-row" style="margin-bottom:.75rem">
                        <div class="mc-form-group">
                            <label class="mc-label">Localidad</label>
                            <input class="mc-input" type="text" name="cliente_localidad" placeholder="Ej: Almoradí / Torrevieja">
                        </div>
                        <div class="mc-form-group">
                            <label class="mc-label">Método de Pago *</label>
                            <select class="mc-select" name="metodo_pago" required>
                                <option value="stripe">Tarjeta (pago online)</option>
                                <option value="efectivo">Pago al repartidor en mano</option>
                            </select>
                        </div>
                    </div>
                    <p class="mc-form-error" id="buy-order-error"></p>
                    <button type="submit" class="mc-btn mc-btn-primary mc-btn-block" style="margin-top:.5rem">
                        <i class="fa-solid fa-credit-card"></i> Confirmar Compra y Envío
                    </button>
                </form>
                <div id="buy-order-result" style="display:none;margin-top:1rem"></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
