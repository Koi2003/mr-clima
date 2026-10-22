<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | Mr. Clima</title>
    <meta name="robots" content="noindex, nofollow">

    <!-- Favicon: isotipo de Mr. Clima -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-mrclima-isotipo.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- API Base URL -->
    <script>window.API_BASE = "{{ url('/api') }}";</script>

    <!-- Styles / Scripts -->
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="/resources/css/app.css">
        <script src="/resources/js/app.js" defer></script>
    @endif
</head>
<body>

<!-- HEADER ADMIN -->
<header class="mc-header">
    <div class="mc-container mc-header-inner">
        <a href="{{ url('/') }}" class="mc-logo">
            <img src="{{ asset('images/logo-mrclima-isotipo.png') }}" alt="Mr. Clima" style="height: 38px; width: auto;" onerror="this.style.display='none'">
            Mr.<span>Clima</span>
            <span style="font-size:.75rem;padding:.15rem .5rem;background:var(--mc-primary-light);color:var(--mc-primary);border-radius:var(--mc-radius-full);margin-left:.5rem;font-weight:600">Admin</span>
        </a>

        <div style="display:flex;align-items:center;gap:1rem">
            <a href="{{ url('/') }}" class="mc-nav-link" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Ver Web Comercial</a>
            <button class="mc-theme-toggle" id="theme-toggle" aria-label="Cambiar tema"><i class="fa-solid fa-moon"></i></button>
        </div>
    </div>
</header>

<main class="mc-section" style="min-height:80vh;padding:3rem 0">
    <div class="mc-container">

        <!-- pantalla de login (si no hay sesión) -->
        <div id="admin-login-section" style="max-width:420px;margin:2rem auto">
            <form id="admin-login-form" class="mc-contact-card" style="box-shadow:var(--mc-shadow-xl)">
                <div style="text-align:center;margin-bottom:1.5rem">
                    <div class="mc-feature-icon" style="margin:0 auto 1rem;width:4rem;height:4rem;font-size:1.8rem">
                        <i class="fa-solid fa-user-shield" style="color:var(--mc-primary)"></i>
                    </div>
                    <h2 style="font-size:1.4rem">Acceso Administrador</h2>
                    <p style="font-size:.85rem;color:var(--mc-text-secondary);margin-top:.25rem">Inicia sesión para gestionar solicitudes e inventario</p>
                </div>

                <div class="mc-form-group">
                    <label class="mc-label" for="admin-email">Email</label>
                    <input class="mc-input" type="email" name="email" id="admin-email" placeholder="admin@misterclima.es" required>
                </div>

                <div class="mc-form-group" style="margin-top:.85rem">
                    <label class="mc-label" for="admin-pass">Contraseña</label>
                    <input class="mc-input" type="password" name="password" id="admin-pass" required>
                </div>

                <p class="mc-form-error" id="login-error" style="margin-top:.5rem"></p>

                <button type="submit" class="mc-btn mc-btn-primary mc-btn-block" style="margin-top:1.25rem">
                    <i class="fa-solid fa-right-to-bracket"></i> Iniciar sesión
                </button>
            </form>
        </div>

        <!-- DASHBOARD (visible al estar autenticado) -->
        <div class="mc-admin-panel" id="admin-dashboard">

            <!-- Barra Superior del Panel -->
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem">
                <div>
                    <h1 style="font-size:1.6rem">Panel de Control</h1>
                    <p style="font-size:.9rem;color:var(--mc-text-secondary)">Gestión centralizada de Mr. Clima</p>
                </div>
                <div style="display:flex;align-items:center;gap:1rem">
                    <span style="font-size:.85rem;font-weight:600;color:var(--mc-success)"><i class="fa-solid fa-circle-check"></i> Conectado como Admin</span>
                    <button class="mc-btn mc-btn-danger mc-btn-sm" id="btn-admin-logout">
                        <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
                    </button>
                </div>
            </div>

            <!-- TARJETAS KPI (ESTADÍSTICAS RÁPIDAS) -->
            <div class="mc-features-grid" style="margin-bottom:2rem;grid-template-columns:repeat(auto-fit, minmax(220px, 1fr))">
                <div class="mc-feature-card" style="text-align:left;padding:1.25rem;display:flex;align-items:center;gap:1rem">
                    <div class="mc-feature-icon" style="margin:0;width:3rem;height:3rem;font-size:1.3rem;background:rgba(234,179,8,.15)">
                        <i class="fa-solid fa-clipboard-list" style="color:var(--mc-warning)"></i>
                    </div>
                    <div>
                        <span style="font-size:.75rem;font-weight:600;text-transform:uppercase;color:var(--mc-text-muted)">Solicitudes Pendientes</span>
                        <div style="font-size:1.5rem;font-weight:800" id="kpi-solicitudes-pendientes">0</div>
                    </div>
                </div>

                <div class="mc-feature-card" style="text-align:left;padding:1.25rem;display:flex;align-items:center;gap:1rem">
                    <div class="mc-feature-icon" style="margin:0;width:3rem;height:3rem;font-size:1.3rem;background:rgba(22,163,74,.15)">
                        <i class="fa-solid fa-boxes-stacked" style="color:var(--mc-success)"></i>
                    </div>
                    <div>
                        <span style="font-size:.75rem;font-weight:600;text-transform:uppercase;color:var(--mc-text-muted)">Lavadoras Disponibles</span>
                        <div style="font-size:1.5rem;font-weight:800" id="kpi-productos-disponibles">0</div>
                    </div>
                </div>

                <div class="mc-feature-card" style="text-align:left;padding:1.25rem;display:flex;align-items:center;gap:1rem">
                    <div class="mc-feature-icon" style="margin:0;width:3rem;height:3rem;font-size:1.3rem;background:rgba(2,132,199,.15)">
                        <i class="fa-solid fa-clock-history" style="color:var(--mc-primary)"></i>
                    </div>
                    <div>
                        <span style="font-size:.75rem;font-weight:600;text-transform:uppercase;color:var(--mc-text-muted)">Reservas 48h Activas</span>
                        <div style="font-size:1.5rem;font-weight:800" id="kpi-reservas-activas">0</div>
                    </div>
                </div>
            </div>

            <!-- TABS -->
            <div class="mc-tabs">
                <button class="mc-tab active" data-tab="solicitudes"><i class="fa-solid fa-clipboard-list"></i> Solicitudes Técnicas</button>
                <button class="mc-tab" data-tab="pedidos" onclick="loadAdminPedidos()"><i class="fa-solid fa-cart-check"></i> Pedidos Cobrados / Envíos</button>
                <button class="mc-tab" data-tab="inventario"><i class="fa-solid fa-boxes-stacked"></i> Inventario de Productos</button>
            </div>

            <!-- TAB: SOLICITUDES -->
            <div class="mc-tab-content" id="tab-solicitudes">
                <div style="margin-bottom:1rem;display:flex;gap:.75rem;align-items:center">
                    <label class="mc-label" style="margin:0">Filtrar estado:</label>
                    <select class="mc-select" id="admin-filtro-estado" style="width:auto;padding:.4rem .6rem;font-size:.85rem">
                        <option value="">Todas las solicitudes</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="en_proceso">En proceso</option>
                        <option value="finalizada">Finalizada</option>
                    </select>
                </div>
                <div class="mc-table-wrap">
                    <table class="mc-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Teléfono</th>
                                <th>Servicio</th>
                                <th>Estado</th>
                                <th>Cambiar Estado</th>
                            </tr>
                        </thead>
                        <tbody id="solicitudes-tbody">
                            <tr><td colspan="6" style="text-align:center;color:var(--mc-text-muted)">Cargando solicitudes...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="mc-pagination" id="solicitudes-paginacion"></div>
            </div>

            <!-- TAB: PEDIDOS COBRADOS / ENVIOS -->
            <div class="mc-tab-content" id="tab-pedidos" style="display:none">
                <div class="mc-table-wrap">
                    <table class="mc-table">
                        <thead>
                            <tr>
                                <th>Pedido</th>
                                <th>Cliente / Teléfono</th>
                                <th>Dirección de Entrega</th>
                                <th>Lavadora</th>
                                <th>Total / Método</th>
                                <th>Estado Pago</th>
                                <th>Estado Envío</th>
                            </tr>
                        </thead>
                        <tbody id="pedidos-tbody">
                            <tr><td colspan="7" style="text-align:center;color:var(--mc-text-muted)">Cargando pedidos...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="mc-pagination" id="pedidos-paginacion"></div>
            </div>

            <!-- TAB: INVENTARIO -->
            <div class="mc-tab-content" id="tab-inventario" style="display:none">
                <!-- Formulario Alta Lavadora -->
                <div class="mc-contact-card" style="margin-bottom:2rem;box-shadow:none;border:1px solid var(--mc-border)">
                    <h3 style="margin-bottom:1rem;font-size:1rem"><i class="fa-solid fa-plus"></i> Alta de producto (Reacondicionado o Nuevo)</h3>
                    <form id="admin-create-product-form" enctype="multipart/form-data">
                        <div class="mc-form-row" style="margin-bottom:.75rem">
                            <div class="mc-form-group">
                                <label class="mc-label">Estado del Producto *</label>
                                <select class="mc-select" name="condicion" required>
                                    <option value="reacondicionado">Reacondicionado en taller</option>
                                    <option value="nuevo">Nuevo a estrenar</option>
                                </select>
                            </div>
                            <div class="mc-form-group">
                                <label class="mc-label">Marca *</label>
                                <input class="mc-input" type="text" name="marca" placeholder="Ej: Bosch" required>
                            </div>
                            <div class="mc-form-group">
                                <label class="mc-label">Modelo *</label>
                                <input class="mc-input" type="text" name="modelo" placeholder="Ej: Serie 4 WAN28281ES" required>
                            </div>
                        </div>

                        <div class="mc-form-row" style="margin-bottom:.75rem">
                            <div class="mc-form-group">
                                <label class="mc-label">Precio (€) *</label>
                                <input class="mc-input" type="number" name="precio" placeholder="249.99" step="0.01" required>
                            </div>
                            <div class="mc-form-group">
                                <label class="mc-label">Garantía (meses) *</label>
                                <input class="mc-input" type="number" name="meses_garantia" placeholder="12" min="1" max="36" required>
                            </div>
                        </div>

                        <div class="mc-form-row" style="margin-bottom:.75rem">
                            <div class="mc-form-group">
                                <label class="mc-label">Capacidad (kg)</label>
                                <input class="mc-input" type="number" name="capacidad_kg" placeholder="Ej: 8" step="0.5" min="1" max="25">
                            </div>
                            <div class="mc-form-group">
                                <label class="mc-label">Centrifugado (RPM)</label>
                                <input class="mc-input" type="number" name="rpm" placeholder="Ej: 1400" step="100" min="400" max="2000">
                            </div>
                            <div class="mc-form-group">
                                <label class="mc-label">Eficiencia Energética</label>
                                <select class="mc-select" name="eficiencia_energetica">
                                    <option value="">Sin especificar</option>
                                    <option value="A">Clase A</option>
                                    <option value="B">Clase B</option>
                                    <option value="C">Clase C</option>
                                    <option value="D">Clase D</option>
                                    <option value="E">Clase E</option>
                                    <option value="F">Clase F</option>
                                </select>
                            </div>
                        </div>

                        <div class="mc-form-group" style="margin-bottom:.75rem">
                            <label class="mc-label">Descripción pública</label>
                            <input class="mc-input" type="text" name="descripcion" placeholder="Ej: Lavadora reacondicionada y revisada con pequeña marca en el frontal.">
                        </div>

                        <div class="mc-form-group" style="margin-bottom:.75rem">
                            <label class="mc-label">Notas técnicas internas</label>
                            <input class="mc-input" type="text" name="notas_tecnicas" placeholder="Ej: Bomba de desagüe nueva instalada en revisión.">
                        </div>

                        <!-- Subida de fotos reales -->
                        <div class="mc-form-group" style="margin-bottom:1rem">
                            <label class="mc-label">Fotos reales del producto (hasta 5 imágenes)</label>
                            <div class="mc-upload-box" onclick="document.getElementById('admin-images-input').click()">
                                <i class="fa-solid fa-cloud-arrow-up" style="font-size:1.8rem;color:var(--mc-primary);margin-bottom:.5rem"></i>
                                <p style="font-size:.85rem;font-weight:500">Haz clic o arrastra aquí para seleccionar fotos reales</p>
                                <span style="font-size:.75rem;color:var(--mc-text-muted)">JPG, PNG o WebP. Máximo 5MB por foto.</span>
                            </div>
                            <input type="file" id="admin-images-input" name="imagenes[]" multiple accept="image/*" style="display:none">
                            <div class="mc-image-previews" id="admin-image-previews"></div>
                        </div>

                        <button type="submit" class="mc-btn mc-btn-primary mc-btn-sm">
                            <i class="fa-solid fa-plus"></i> Añadir lavadora al catálogo
                        </button>
                    </form>
                </div>

                <!-- Tabla de Productos -->
                <div style="margin-bottom:1rem;display:flex;gap:.75rem;align-items:center">
                    <label class="mc-label" style="margin:0">Filtrar estado:</label>
                    <select class="mc-select" id="admin-filtro-producto-estado" style="width:auto;padding:.4rem .6rem;font-size:.85rem">
                        <option value="">Todos los productos</option>
                        <option value="disponible">Disponible</option>
                        <option value="reservado">Reservado</option>
                        <option value="vendido">Vendido</option>
                    </select>
                </div>
                <div class="mc-table-wrap">
                    <table class="mc-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Marca</th>
                                <th>Modelo</th>
                                <th>Precio</th>
                                <th>Garantía</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="productos-tbody">
                            <tr><td colspan="7" style="text-align:center;color:var(--mc-text-muted)">Cargando inventario...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="mc-pagination" id="productos-paginacion"></div>
            </div>

        </div>

    </div>
</main>

<footer class="mc-footer" style="padding:1.5rem 0">
    <div class="mc-container" style="text-align:center;font-size:.8rem;color:var(--mc-text-muted)">
        <p>© {{ date('Y') }} Mr. Clima — Panel de Gestión Interna</p>
    </div>
</footer>

</body>
</html>
