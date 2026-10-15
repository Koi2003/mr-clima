/* ================================================================
   MR. CLIMA — SPA Client Logic
   ================================================================ */

const API = window.API_BASE || (window.location.pathname.replace(/\/$/, '') + '/api');
let adminToken = null;
let catalogProductsMap = {};
let selectedReserveProduct = null;

/* ------ Utility Functions ------ */

async function apiCall(method, path, body = null, auth = false) {
  const headers = { 'Accept': 'application/json' };
  let reqBody = undefined;

  if (body) {
    if (body instanceof FormData) {
      reqBody = body;
    } else {
      headers['Content-Type'] = 'application/json';
      reqBody = JSON.stringify(body);
    }
  }

  if (auth && adminToken) headers['Authorization'] = 'Bearer ' + adminToken;

  const res = await fetch(API + path, {
    method,
    headers,
    body: reqBody,
  });

  const json = await res.json().catch(() => null);
  return { status: res.status, ok: res.ok, json };
}

function showToast(message, type = 'success') {
  let toast = document.getElementById('mc-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'mc-toast';
    toast.className = 'mc-toast';
    document.body.appendChild(toast);
  }
  toast.textContent = message;
  toast.className = 'mc-toast mc-toast-' + type + ' show';
  clearTimeout(toast._timeout);
  toast._timeout = setTimeout(() => toast.classList.remove('show'), 4000);
}

function debounce(fn, ms = 400) {
  let timer;
  return (...args) => {
    clearTimeout(timer);
    timer = setTimeout(() => fn(...args), ms);
  };
}

function formatPrice(price) {
  return parseFloat(price).toFixed(2).replace('.', ',') + ' €';
}

function openModal(id) {
  document.getElementById(id)?.classList.add('active');
}
function closeModal(id) {
  document.getElementById(id)?.classList.remove('active');
}

/* ------ Theme Toggle ------ */
function initTheme() {
  const saved = localStorage.getItem('mc-theme');
  if (saved) document.documentElement.setAttribute('data-theme', saved);
  const btn = document.getElementById('theme-toggle');
  if (btn) {
    updateThemeIcon(btn);
    btn.addEventListener('click', () => {
      const current = document.documentElement.getAttribute('data-theme');
      const next = current === 'dark' ? 'light' : 'dark';
      document.documentElement.setAttribute('data-theme', next);
      localStorage.setItem('mc-theme', next);
      updateThemeIcon(btn);
    });
  }
}
function updateThemeIcon(btn) {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  btn.innerHTML = isDark ? '<i class="fa-solid fa-sun"></i>' : '<i class="fa-solid fa-moon"></i>';
}

/* ------ Mobile Menu ------ */
function initMobileMenu() {
  const toggle = document.getElementById('menu-toggle');
  const nav = document.getElementById('main-nav');
  if (toggle && nav) {
    toggle.addEventListener('click', () => nav.classList.toggle('open'));
    nav.querySelectorAll('.mc-nav-link').forEach(link =>
      link.addEventListener('click', () => nav.classList.remove('open'))
    );
  }
}

/* ------ Smooth Scroll Navigation ------ */
function initSmoothScroll() {
  document.querySelectorAll('a[href^="#"]').forEach(a => {
    a.addEventListener('click', e => {
      e.preventDefault();
      const target = document.querySelector(a.getAttribute('href'));
      if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
  });
}

/* ================================================================
   CATALOG MODULE
   ================================================================ */

let currentPage = 1;
let catalogMeta = null;

async function loadCatalog(page = 1) {
  const grid = document.getElementById('products-grid');
  const paginationEl = document.getElementById('catalog-pagination');
  if (!grid) return;

  grid.innerHTML = '<div class="mc-empty"><div class="mc-spinner"></div><p style="margin-top:1rem">Cargando catálogo...</p></div>';

  const params = new URLSearchParams();
  const marca = document.getElementById('filter-marca')?.value;
  const precioMax = document.getElementById('filter-precio')?.value;
  if (marca) params.set('marca', marca);
  if (precioMax) params.set('precio_max', precioMax);
  params.set('page', page);

  const qs = params.toString();
  const { ok, json } = await apiCall('GET', '/lavadoras' + (qs ? '?' + qs : ''));

  if (!ok || !json?.data) {
    grid.innerHTML = '<div class="mc-empty"><div class="mc-empty-icon"><i class="fa-solid fa-triangle-exclamation" style="font-size:2.5rem;color:var(--mc-warning)"></i></div><p style="margin-top:1rem">Error al cargar el catálogo</p></div>';
    return;
  }

  currentPage = json.meta?.current_page || 1;
  catalogMeta = json.meta;
  catalogProductsMap = {};

  if (json.data.length === 0) {
    grid.innerHTML = '<div class="mc-empty"><div class="mc-empty-icon"><i class="fa-solid fa-magnifying-glass" style="font-size:2.5rem;color:var(--mc-text-muted)"></i></div><p style="margin-top:1rem">No se encontraron lavadoras con esos filtros</p></div>';
    if (paginationEl) paginationEl.innerHTML = '';
    return;
  }

  grid.innerHTML = json.data.map(p => {
    catalogProductsMap[p.id] = p;
    const mainImg = (p.imagenes && p.imagenes.length > 0) ? p.imagenes[0] : (p.imagen_url || null);

    let specsBadges = `<span class="mc-spec-pill"><i class="fa-solid fa-shield-halved"></i> ${p.meses_garantia}m garantía</span>`;
    if (p.capacidad_kg) specsBadges += `<span class="mc-spec-pill"><i class="fa-solid fa-weight-hanging"></i> ${p.capacidad_kg}kg</span>`;
    if (p.rpm) specsBadges += `<span class="mc-spec-pill"><i class="fa-solid fa-rotate-right"></i> ${p.rpm}rpm</span>`;
    if (p.eficiencia_energetica) specsBadges += `<span class="mc-spec-pill"><i class="fa-solid fa-bolt"></i> Clase ${esc(p.eficiencia_energetica)}</span>`;

    return `
      <article class="mc-product-card mc-animate-in" onclick="openReserveModal(${p.id})" style="cursor:pointer">
        <div class="mc-product-img">
          ${mainImg ? `<img src="${mainImg}" alt="${esc(p.marca)} ${esc(p.modelo)}">` : '<i class="fa-solid fa-soap" style="font-size:3rem;color:var(--mc-primary)"></i>'}
          <span class="mc-badge" style="position:absolute;top:.75rem;left:.75rem;background:var(--mc-surface);font-weight:700;font-size:.75rem;box-shadow:var(--mc-shadow)">
            ${p.condicion === 'nuevo' ? '<i class="fa-solid fa-sparkles" style="color:var(--mc-accent)"></i> Nuevo a Estrenar' : '<i class="fa-solid fa-recycle" style="color:var(--mc-success)"></i> Reacondicionado'}
          </span>
        </div>
        <div class="mc-product-body">
          <div class="mc-product-brand">${esc(p.marca)}</div>
          <h3 class="mc-product-name">${esc(p.modelo)}</h3>
          <p class="mc-product-desc">${esc(p.descripcion || 'Electrodoméstico revisado y probado en taller.')}</p>

          <div class="mc-specs-row">
            ${specsBadges}
          </div>

          <div class="mc-product-footer" style="margin-top:.75rem">
            <span class="mc-product-price">${formatPrice(p.precio)}</span>
            <button class="mc-btn mc-btn-accent mc-btn-sm" onclick="event.stopPropagation(); openReserveModal(${p.id})">
              <i class="fa-solid fa-cart-shopping"></i> Comprar / Reservar
            </button>
          </div>
        </div>
      </article>
    `;
  }).join('');

  if (paginationEl && catalogMeta) {
    renderPagination(paginationEl, catalogMeta);
  }
}

function renderPagination(el, meta) {
  if (meta.last_page <= 1) { el.innerHTML = ''; return; }
  let html = '';
  if (meta.current_page > 1) {
    html += `<button class="mc-page-btn" onclick="loadCatalog(${meta.current_page - 1})">‹</button>`;
  }
  for (let i = 1; i <= meta.last_page; i++) {
    html += `<button class="mc-page-btn ${i === meta.current_page ? 'active' : ''}" onclick="loadCatalog(${i})">${i}</button>`;
  }
  if (meta.current_page < meta.last_page) {
    html += `<button class="mc-page-btn" onclick="loadCatalog(${meta.current_page + 1})">›</button>`;
  }
  el.innerHTML = html;
}

function esc(str) {
  if (!str) return '';
  const d = document.createElement('div');
  d.textContent = str;
  return d.innerHTML;
}

const debouncedLoadCatalog = debounce(() => loadCatalog(1));

function initCatalogFilters() {
  document.getElementById('filter-marca')?.addEventListener('change', () => loadCatalog(1));
  document.getElementById('filter-precio')?.addEventListener('input', debouncedLoadCatalog);
  document.getElementById('btn-clear-filters')?.addEventListener('click', () => {
    const marca = document.getElementById('filter-marca');
    const precio = document.getElementById('filter-precio');
    if (marca) marca.value = '';
    if (precio) precio.value = '';
    loadCatalog(1);
  });
}

/* ================================================================
   RESERVE & DETAIL MODAL
   ================================================================ */

function openReserveModal(id) {
  const product = catalogProductsMap[id];
  if (!product) return;

  selectedReserveProduct = product;
  const galleryArea = document.getElementById('reserve-gallery-area');
  const info = document.getElementById('reserve-product-info');
  const whatsappContainer = document.getElementById('reserve-whatsapp-container');

  if (whatsappContainer) {
    whatsappContainer.style.display = 'none';
    whatsappContainer.innerHTML = '';
  }

  const imgs = (product.imagenes && product.imagenes.length > 0)
    ? product.imagenes
    : (product.imagen_url ? [product.imagen_url] : []);

  if (galleryArea) {
    if (imgs.length > 0) {
      galleryArea.innerHTML = `
        <div class="mc-gallery-container">
          <div class="mc-gallery-main" id="modal-main-img-box">
            <img src="${imgs[0]}" id="modal-main-img" alt="${esc(product.marca)} ${esc(product.modelo)}">
          </div>
          ${imgs.length > 1 ? `
            <div class="mc-gallery-thumbs">
              ${imgs.map((url, idx) => `
                <div class="mc-gallery-thumb ${idx === 0 ? 'active' : ''}" onclick="switchModalGalleryImage('${url}', this)">
                  <img src="${url}" alt="Foto ${idx+1}">
                </div>
              `).join('')}
            </div>
          ` : ''}
        </div>
      `;
    } else {
      galleryArea.innerHTML = `
        <div class="mc-gallery-main">
          <i class="fa-solid fa-soap" style="font-size:4rem;color:var(--mc-primary)"></i>
        </div>
      `;
    }
  }

  if (info) {
    let specPills = `<span class="mc-spec-pill"><i class="fa-solid fa-shield-halved"></i> ${product.meses_garantia} meses garantía</span>`;
    if (product.capacidad_kg) specPills += `<span class="mc-spec-pill"><i class="fa-solid fa-weight-hanging"></i> ${product.capacidad_kg} kg de carga</span>`;
    if (product.rpm) specPills += `<span class="mc-spec-pill"><i class="fa-solid fa-rotate-right"></i> ${product.rpm} RPM</span>`;
    if (product.eficiencia_energetica) specPills += `<span class="mc-spec-pill"><i class="fa-solid fa-bolt"></i> Eficiencia Clase ${esc(product.eficiencia_energetica)}</span>`;

    info.innerHTML = `
      <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem">
        <div>
          <span style="font-size:.75rem;font-weight:700;color:var(--mc-primary);text-transform:uppercase;letter-spacing:.05em">${esc(product.marca)}</span>
          <h3 style="font-size:1.3rem;font-weight:700;margin-bottom:.25rem">${esc(product.modelo)}</h3>
        </div>
        <span style="font-family:'Outfit',sans-serif;font-size:1.6rem;font-weight:800;color:var(--mc-text)">${formatPrice(product.precio)}</span>
      </div>

      <div class="mc-specs-row" style="margin:.75rem 0">
        ${specPills}
      </div>

      <p style="font-size:.9rem;color:var(--mc-text-secondary);margin-bottom:.75rem">
        ${esc(product.descripcion || 'Lavadora reacondicionada y testada en taller por nuestros técnicos.')}
      </p>

      ${product.notas_tecnicas ? `
        <div style="padding:.6rem .8rem;background:var(--mc-bg-alt);border-radius:var(--mc-radius-sm);font-size:.8rem;color:var(--mc-text-secondary);margin-bottom:.75rem">
          <strong style="color:var(--mc-primary)"><i class="fa-solid fa-screwdriver-wrench"></i> Informe técnico:</strong> ${esc(product.notas_tecnicas)}
        </div>
      ` : ''}

      <p style="font-size:.8rem;color:var(--mc-text-muted)">
        <i class="fa-solid fa-circle-info"></i> La reserva es 100% gratuita y guarda el equipo a tu nombre durante 48 horas.
      </p>
    `;
  }

  document.getElementById('reserve-error').textContent = '';
  const btn = document.getElementById('btn-confirm-reserve');
  if (btn) {
    btn.disabled = false;
    btn.style.display = 'block';
    btn.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Confirmar Reserva 48h Gratis';
  }

  openModal('reserve-modal');
}

function switchModalGalleryImage(url, thumbEl) {
  const mainImg = document.getElementById('modal-main-img');
  if (mainImg) mainImg.src = url;
  document.querySelectorAll('.mc-gallery-thumb').forEach(t => t.classList.remove('active'));
  thumbEl?.classList.add('active');
}

async function confirmReserve() {
  if (!selectedReserveProduct) return;

  const btn = document.getElementById('btn-confirm-reserve');
  const errorEl = document.getElementById('reserve-error');
  const whatsappContainer = document.getElementById('reserve-whatsapp-container');

  btn.disabled = true;
  btn.innerHTML = '<span class="mc-spinner"></span> Confirmando reserva...';
  errorEl.textContent = '';

  const { ok, json } = await apiCall('POST', `/lavadoras/${selectedReserveProduct.id}/reservar`);

  if (ok) {
    btn.style.display = 'none';
    showToast('¡Lavadora reservada! Tienes 48 horas para confirmar.', 'success');

    const msg = encodeURIComponent(`Hola Mr. Clima, acabo de reservar la lavadora ${selectedReserveProduct.marca} ${selectedReserveProduct.modelo} (${formatPrice(selectedReserveProduct.precio)}) en la web. Me gustaría confirmar los detalles.`);
    const waUrl = `https://wa.me/34685949486?text=${msg}`;

    if (whatsappContainer) {
      whatsappContainer.style.display = 'block';
      whatsappContainer.innerHTML = `
        <div style="padding:1rem;background:rgba(37,211,102,.1);border:1px solid #25D366;border-radius:var(--mc-radius);text-align:center">
          <p style="font-weight:600;color:#1DA851;margin-bottom:.5rem">¡Reserva registrada a tu nombre!</p>
          <a href="${waUrl}" target="_blank" rel="noopener" class="mc-btn mc-btn-whatsapp mc-btn-block">
            <i class="fa-brands fa-whatsapp" style="font-size:1.2rem"></i> Confirmar por WhatsApp en 1-Clic
          </a>
        </div>
      `;
    }
    loadCatalog(currentPage);
  } else {
    const msg = json?.error === 'stock_no_disponible'
      ? 'Esta lavadora ya no está disponible. Alguien la ha reservado antes.'
      : (json?.message || 'Error al reservar.');
    errorEl.textContent = msg;
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-calendar-check"></i> Reintentar Reserva';
  }
}

/* ================================================================
   CONTACT MODULE (HomeServe-style form)
   ================================================================ */

async function submitContact(e) {
  e.preventDefault();
  const form = e.target;
  const btn = form.querySelector('button[type="submit"]');
  const errorEl = document.getElementById('contact-errors');
  const successEl = document.getElementById('contact-success');

  errorEl.innerHTML = '';
  successEl.style.display = 'none';
  form.querySelectorAll('.mc-form-error').forEach(el => el.textContent = '');
  btn.disabled = true;
  btn.innerHTML = '<span class="mc-spinner"></span> Enviando solicitud...';

  const body = {
    cliente_nombre: form.cliente_nombre.value.trim(),
    cliente_telefono: form.cliente_telefono.value.trim(),
    tipo_aparato: form.tipo_aparato.value,
    descripcion_averia: form.descripcion_averia.value.trim(),
  };

  const { ok, status, json } = await apiCall('POST', '/contacto', body);

  if (ok) {
    const nombre = body.cliente_nombre;
    const aparato = body.tipo_aparato.replace('_', ' ');
    const msg = encodeURIComponent(`Hola Mr. Clima, soy ${nombre}. He solicitado asistencia técnica desde la web para mi ${aparato}: "${body.descripcion_averia}". Mi teléfono es ${body.cliente_telefono}.`);
    const waUrl = `https://wa.me/34685949486?text=${msg}`;

    form.reset();
    successEl.style.display = 'block';
    successEl.innerHTML = `
      <div style="margin-bottom:.75rem">${esc(json?.message || 'Solicitud recibida. Te contactaremos en breve.')}</div>
      <a href="${waUrl}" target="_blank" rel="noopener" class="mc-btn mc-btn-whatsapp mc-btn-block">
        <i class="fa-brands fa-whatsapp" style="font-size:1.2rem"></i> Hablar con un Técnico por WhatsApp
      </a>
    `;
    showToast('Solicitud enviada correctamente', 'success');
  } else if (status === 422 && json?.errors) {
    Object.entries(json.errors).forEach(([field, msgs]) => {
      const errEl = document.getElementById('err-' + field);
      if (errEl) errEl.textContent = msgs[0];
    });
  } else {
    errorEl.innerHTML = '<p class="mc-form-error">' + (json?.message || 'Error al enviar la solicitud.') + '</p>';
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-headset"></i> Te contactamos gratis';
}

/* ================================================================
   ADMIN MODULE
   ================================================================ */

function initAdmin() {
  const savedToken = localStorage.getItem('mc-admin-token');
  if (savedToken && document.getElementById('admin-dashboard')) {
    adminToken = savedToken;
    showAdminPanel();
    loadAdminSolicitudes();
    loadAdminProductos();
  }

  document.getElementById('admin-login-form')?.addEventListener('submit', async (e) => {
    e.preventDefault();
    const form = e.target;
    const btn = form.querySelector('button[type="submit"]');
    const errEl = document.getElementById('login-error');
    errEl.textContent = '';
    btn.disabled = true;
    btn.innerHTML = '<span class="mc-spinner"></span> Accediendo...';

    const { ok, json } = await apiCall('POST', '/admin/login', {
      email: form.email.value,
      password: form.password.value,
    });

    if (ok && json?.token) {
      adminToken = json.token;
      localStorage.setItem('mc-admin-token', adminToken);
      showAdminPanel();
      loadAdminSolicitudes();
      loadAdminProductos();
    } else {
      errEl.textContent = json?.message || 'Credenciales incorrectas.';
    }
    btn.disabled = false;
    btn.innerHTML = 'Iniciar sesión';
  });

  document.getElementById('btn-admin-logout')?.addEventListener('click', async () => {
    await apiCall('POST', '/admin/logout', null, true);
    adminToken = null;
    localStorage.removeItem('mc-admin-token');
    hideAdminPanel();
    showToast('Sesión cerrada', 'success');
  });

  document.querySelectorAll('.mc-tab[data-tab]').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.mc-tab').forEach(t => t.classList.remove('active'));
      document.querySelectorAll('.mc-tab-content').forEach(c => c.style.display = 'none');
      tab.classList.add('active');
      const target = document.getElementById('tab-' + tab.dataset.tab);
      if (target) target.style.display = 'block';

      if (tab.dataset.tab === 'solicitudes') loadAdminSolicitudes();
      if (tab.dataset.tab === 'inventario') loadAdminProductos();
    });
  });

  // Admin image file preview listener
  const imgInput = document.getElementById('admin-images-input');
  if (imgInput) {
    imgInput.addEventListener('change', (e) => {
      const container = document.getElementById('admin-image-previews');
      if (!container) return;
      container.innerHTML = '';
      const files = Array.from(e.target.files).slice(0, 5);

      files.forEach((file) => {
        const reader = new FileReader();
        reader.onload = (ev) => {
          const div = document.createElement('div');
          div.className = 'mc-preview-thumb';
          div.innerHTML = `<img src="${ev.target.result}" alt="Preview">`;
          container.appendChild(div);
        };
        reader.readAsDataURL(file);
      });
    });
  }
}

function showAdminPanel() {
  document.getElementById('admin-login-section')?.style.setProperty('display', 'none');
  document.getElementById('admin-dashboard')?.classList.add('active');
}
function hideAdminPanel() {
  document.getElementById('admin-login-section')?.style.removeProperty('display');
  document.getElementById('admin-dashboard')?.classList.remove('active');
}

/* --- Admin: Solicitudes --- */
async function loadAdminSolicitudes() {
  const tbody = document.getElementById('solicitudes-tbody');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="6" style="text-align:center"><span class="mc-spinner"></span></td></tr>';

  const filtro = document.getElementById('admin-filtro-estado')?.value || '';
  const qs = filtro ? '?estado=' + filtro : '';
  const { ok, json } = await apiCall('GET', '/admin/solicitudes' + qs, null, true);

  if (!ok || !json?.data) {
    tbody.innerHTML = '<tr><td colspan="6">Error al cargar</td></tr>';
    return;
  }

  // Update KPI
  const pendientesCount = json.data.filter(s => s.estado === 'pendiente').length;
  const kpiEl = document.getElementById('kpi-solicitudes-pendientes');
  if (kpiEl) kpiEl.textContent = pendientesCount;

  if (json.data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="6" style="text-align:center;color:var(--mc-text-muted)">Sin solicitudes</td></tr>';
    return;
  }

  tbody.innerHTML = json.data.map(s => `
    <tr>
      <td>${s.id}</td>
      <td>${esc(s.cliente_nombre)}</td>
      <td><a href="tel:${s.cliente_telefono}">${s.cliente_telefono}</a></td>
      <td>${esc(s.tipo_aparato)}</td>
      <td><span class="mc-status mc-status-${s.estado}">${s.estado}</span></td>
      <td>
        <select onchange="updateSolicitudEstado(${s.id}, this.value)" class="mc-select" style="padding:.35rem .5rem;font-size:.8rem;width:auto">
          <option value="pendiente" ${s.estado === 'pendiente' ? 'selected' : ''}>Pendiente</option>
          <option value="en_proceso" ${s.estado === 'en_proceso' ? 'selected' : ''}>En proceso</option>
          <option value="finalizada" ${s.estado === 'finalizada' ? 'selected' : ''}>Finalizada</option>
        </select>
      </td>
    </tr>
  `).join('');
}

async function updateSolicitudEstado(id, estado) {
  const { ok } = await apiCall('PATCH', `/admin/solicitudes/${id}`, { estado }, true);
  if (ok) {
    showToast('Estado actualizado', 'success');
    loadAdminSolicitudes();
  } else {
    showToast('Error al actualizar', 'error');
  }
}

/* --- Admin: Productos / Inventario --- */
async function loadAdminProductos() {
  const tbody = document.getElementById('productos-tbody');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="7" style="text-align:center"><span class="mc-spinner"></span></td></tr>';

  const filtro = document.getElementById('admin-filtro-producto-estado')?.value || '';
  const qs = filtro ? '?estado=' + filtro : '';
  const { ok, json } = await apiCall('GET', '/admin/productos' + qs, null, true);

  if (!ok || !json?.data) {
    tbody.innerHTML = '<tr><td colspan="7">Error al cargar</td></tr>';
    return;
  }

  // Update KPIs
  const dispCount = json.data.filter(p => p.estado_stock === 'disponible').length;
  const resCount = json.data.filter(p => p.estado_stock === 'reservado').length;
  const elDisp = document.getElementById('kpi-productos-disponibles');
  const elRes = document.getElementById('kpi-reservas-activas');
  if (elDisp) elDisp.textContent = dispCount;
  if (elRes) elRes.textContent = resCount;

  if (json.data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--mc-text-muted)">Sin productos</td></tr>';
    return;
  }

  tbody.innerHTML = json.data.map(p => `
    <tr>
      <td>${p.id}</td>
      <td>${esc(p.marca)}</td>
      <td>${esc(p.modelo)}</td>
      <td>${formatPrice(p.precio)}</td>
      <td>${p.meses_garantia}m</td>
      <td><span class="mc-status mc-status-${p.estado_stock}">${p.estado_stock}</span></td>
      <td>
        <div style="display:flex;gap:.35rem">
          <button class="mc-btn mc-btn-sm mc-btn-outline" onclick="openEditProductModal(${p.id})"><i class="fa-solid fa-pen"></i></button>
          <button class="mc-btn mc-btn-sm mc-btn-danger" onclick="deleteProduct(${p.id})"><i class="fa-solid fa-trash"></i></button>
        </div>
      </td>
    </tr>
  `).join('');
}

async function deleteProduct(id) {
  if (!confirm('¿Eliminar esta lavadora del inventario?')) return;
  const { ok, json } = await apiCall('DELETE', `/admin/productos/${id}`, null, true);
  if (ok) {
    showToast('Lavadora eliminada', 'success');
    loadAdminProductos();
  } else {
    showToast(json?.message || 'No se puede eliminar', 'error');
  }
}

function openEditProductModal(id) {
  const options = ['disponible', 'reservado', 'vendido'];
  const nuevo = prompt('Nuevo estado (disponible / reservado / vendido):');
  if (nuevo && options.includes(nuevo)) {
    apiCall('PATCH', `/admin/productos/${id}`, { estado_stock: nuevo }, true).then(({ ok }) => {
      if (ok) {
        showToast('Producto actualizado', 'success');
        loadAdminProductos();
        loadCatalog(currentPage);
      } else {
        showToast('Error al actualizar', 'error');
      }
    });
  }
}

async function createProduct(e) {
  e.preventDefault();
  const form = e.target;
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true;
  btn.innerHTML = '<span class="mc-spinner"></span> Guardando lavadora...';

  const formData = new FormData();
  formData.append('marca', form.marca.value.trim());
  formData.append('modelo', form.modelo.value.trim());
  formData.append('precio', form.precio.value);
  formData.append('meses_garantia', form.meses_garantia.value);

  if (form.capacidad_kg?.value) formData.append('capacidad_kg', form.capacidad_kg.value);
  if (form.rpm?.value) formData.append('rpm', form.rpm.value);
  if (form.eficiencia_energetica?.value) formData.append('eficiencia_energetica', form.eficiencia_energetica.value);
  if (form.descripcion?.value?.trim()) formData.append('descripcion', form.descripcion.value.trim());
  if (form.notas_tecnicas?.value?.trim()) formData.append('notas_tecnicas', form.notas_tecnicas.value.trim());

  const fileInput = document.getElementById('admin-images-input');
  if (fileInput && fileInput.files.length > 0) {
    Array.from(fileInput.files).slice(0, 5).forEach((file) => {
      formData.append('imagenes[]', file);
    });
  }

  const { ok, json } = await apiCall('POST', '/admin/productos', formData, true);

  if (ok) {
    showToast('Lavadora añadida al inventario', 'success');
    form.reset();
    const previewContainer = document.getElementById('admin-image-previews');
    if (previewContainer) previewContainer.innerHTML = '';
    loadAdminProductos();
    loadCatalog(1);
  } else {
    const errors = json?.errors ? Object.values(json.errors).flat().join(', ') : (json?.message || 'Error al guardar');
    showToast(errors, 'error');
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="fa-solid fa-plus"></i> Añadir lavadora al catálogo';
}

async function submitBuyOrder(e) {
  e.preventDefault();
  if (!selectedReserveProduct) return;

  const form = e.target;
  const btn = form.querySelector('button[type="submit"]');
  const errorEl = document.getElementById('buy-order-error');
  const resultEl = document.getElementById('buy-order-result');

  btn.disabled = true;
  btn.innerHTML = '<span class="mc-spinner"></span> Procesando pedido...';
  if (errorEl) errorEl.textContent = '';

  const body = {
    producto_id: selectedReserveProduct.id,
    cliente_nombre: form.cliente_nombre.value.trim(),
    cliente_telefono: form.cliente_telefono.value.trim(),
    cliente_email: form.cliente_email?.value?.trim() || null,
    cliente_direccion: form.cliente_direccion.value.trim(),
    cliente_localidad: form.cliente_localidad?.value?.trim() || 'Alicante / Vega Baja',
    metodo_pago: form.metodo_pago.value,
  };

  const { ok, json } = await apiCall('POST', '/checkout/comprar', body);

  if (ok && json?.data) {
    if (json.data.checkout_url) {
      // Pago con tarjeta: redirigir a la pasarela segura de Stripe.
      window.location.href = json.data.checkout_url;
      return;
    }
    showToast('¡Pedido realizado con éxito!', 'success');
    form.style.display = 'none';
    if (resultEl) {
      resultEl.style.display = 'block';
      resultEl.innerHTML = `
        <div style="padding:1.25rem;background:rgba(22,163,74,.1);border:1.5px solid var(--mc-success);border-radius:var(--mc-radius);text-align:center">
          <h4 style="color:var(--mc-success);margin-bottom:.5rem"><i class="fa-solid fa-circle-check"></i> ¡Pedido #${json.data.pedido_id} Confirmado!</h4>
          <p style="font-size:.9rem;color:var(--mc-text-secondary);margin-bottom:.75rem">
            Transacción: <strong>${json.data.transaction_id}</strong><br>
            Método de pago: <strong>${json.data.metodo_pago.toUpperCase()}</strong> (${json.data.estado_pago})
          </p>
          <p style="font-size:.85rem;color:var(--mc-text)">Te contactaremos por teléfono para coordinar la hora exacta de entrega e instalación en tu domicilio.</p>
        </div>
      `;
    }
    loadCatalog(currentPage);
  } else {
    const msg = json?.message || 'Error al procesar el pedido.';
    if (errorEl) errorEl.textContent = msg;
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-credit-card"></i> Confirmar Compra Directa';
  }
}

/* --- Admin: Pedidos --- */
async function loadAdminPedidos() {
  const tbody = document.getElementById('pedidos-tbody');
  if (!tbody) return;
  tbody.innerHTML = '<tr><td colspan="7" style="text-align:center"><span class="mc-spinner"></span></td></tr>';

  const { ok, json } = await apiCall('GET', '/admin/pedidos', null, true);

  if (!ok || !json?.data) {
    tbody.innerHTML = '<tr><td colspan="7">Error al cargar pedidos</td></tr>';
    return;
  }

  if (json.data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align:center;color:var(--mc-text-muted)">Sin pedidos registrados</td></tr>';
    return;
  }

  tbody.innerHTML = json.data.map(p => `
    <tr>
      <td>#${p.id}</td>
      <td>${esc(p.cliente_nombre)}<br><small><a href="tel:${p.cliente_telefono}">${p.cliente_telefono}</a></small></td>
      <td>${esc(p.cliente_direccion)} (${esc(p.cliente_localidad)})</td>
      <td>${formatPrice(p.total)} (${p.metodo_pago.toUpperCase()})</td>
      <td><span class="mc-status mc-status-${p.estado_pago === 'pagado' ? 'finalizada' : 'pendiente'}">${p.estado_pago}</span></td>
      <td>
        <select onchange="updatePedidoEstado(${p.id}, 'estado_envio', this.value)" class="mc-select" style="padding:.35rem .5rem;font-size:.8rem;width:auto">
          <option value="pendiente" ${p.estado_envio === 'pendiente' ? 'selected' : ''}>Pendiente</option>
          <option value="en_reparto" ${p.estado_envio === 'en_reparto' ? 'selected' : ''}>En reparto</option>
          <option value="entregado" ${p.estado_envio === 'entregado' ? 'selected' : ''}>Entregado</option>
        </select>
      </td>
    </tr>
  `).join('');
}

async function updatePedidoEstado(id, campo, valor) {
  const body = {};
  body[campo] = valor;
  const { ok } = await apiCall('PATCH', `/admin/pedidos/${id}`, body, true);
  if (ok) {
    showToast('Pedido actualizado', 'success');
    loadAdminPedidos();
  } else {
    showToast('Error al actualizar', 'error');
  }
}

function initServiceSelector() {
  document.querySelectorAll('.mc-service-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.mc-service-btn').forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      const aparato = btn.dataset.aparato;
      const input = document.getElementById('form-tipo-aparato');
      if (input && aparato) input.value = aparato;
    });
  });
}

/* ================================================================
   INIT
   ================================================================ */

document.addEventListener('DOMContentLoaded', () => {
  initTheme();
  initMobileMenu();
  initSmoothScroll();
  initCatalogFilters();
  initServiceSelector();
  initAdmin();

  loadCatalog(1);

  document.getElementById('contact-form')?.addEventListener('submit', submitContact);
  document.getElementById('btn-confirm-reserve')?.addEventListener('click', confirmReserve);
  document.getElementById('buy-order-form')?.addEventListener('submit', submitBuyOrder);
  document.getElementById('admin-create-product-form')?.addEventListener('submit', createProduct);
  document.getElementById('admin-filtro-estado')?.addEventListener('change', loadAdminSolicitudes);
  document.getElementById('admin-filtro-producto-estado')?.addEventListener('change', loadAdminProductos);

  document.querySelectorAll('.mc-modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) overlay.classList.remove('active');
    });
  });
});

window.loadCatalog = loadCatalog;
window.openReserveModal = openReserveModal;
window.switchModalGalleryImage = switchModalGalleryImage;
window.confirmReserve = confirmReserve;
window.submitBuyOrder = submitBuyOrder;
window.loadAdminPedidos = loadAdminPedidos;
window.updatePedidoEstado = updatePedidoEstado;
window.updateSolicitudEstado = updateSolicitudEstado;
window.deleteProduct = deleteProduct;
window.openEditProductModal = openEditProductModal;
window.openModal = openModal;
window.closeModal = closeModal;
