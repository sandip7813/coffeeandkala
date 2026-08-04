/**
 * AdminLTE 4 + Bootstrap 5 entry point.
 *
 * Published by `php artisan adminlte:install`. Add this file to your
 * vite.config.js input array, then `npm run dev` / `npm run build`.
 */

// Bootstrap (provides dropdowns, modals, tooltips, offcanvas, etc.)
import 'bootstrap'

// OverlayScrollbars — AdminLTE uses it for the sidebar scroller (optional)
import { OverlayScrollbars } from 'overlayscrollbars'

// AdminLTE plugins (PushMenu, Treeview, CardWidget, FullScreen, DirectChat,
// Layout, accessibility). The data-lte-* API is wired on DOMContentLoaded.
import 'admin-lte'

import Swal from 'sweetalert2'
/**
 * Initialise an optional plugin only when its global is present.
 * Plugin libraries (ApexCharts, jsVectorMap, FullCalendar, Sortable) are
 * loaded lazily via the @pluginScripts directive as global <script> tags,
 * so we feature-detect before touching them.
 */
function whenReady(fn) {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', fn)
  } else {
    fn()
  }
}

function parseConfig(el, attr) {
  const raw = el.getAttribute(attr)
  if (!raw) return {}
  try {
    return JSON.parse(raw)
  } catch (e) {
    console.warn('AdminLTE: invalid JSON in', attr, e)
    return {}
  }
}

// --- ApexCharts ------------------------------------------------------------
function initCharts() {
  if (typeof window.ApexCharts === 'undefined') return
  document.querySelectorAll('[data-apexchart]').forEach((el) => {
    if (el.dataset.apexchartReady) return
    const config = parseConfig(el, 'data-apexchart-config')
    try {
      new window.ApexCharts(el, config).render()
      el.dataset.apexchartReady = 'true'
    } catch (e) {
      console.warn('AdminLTE: ApexCharts init failed (check the chart config)', e)
    }
  })
}

// --- jsVectorMap -----------------------------------------------------------
function initVectorMaps() {
  if (typeof window.jsVectorMap === 'undefined') return
  document.querySelectorAll('[data-jsvectormap]').forEach((el) => {
    if (el.dataset.jsvectormapReady || !el.id) return
    const config = parseConfig(el, 'data-jsvectormap-config')
    try {
      new window.jsVectorMap({ selector: '#' + el.id, ...config })
      el.dataset.jsvectormapReady = 'true'
    } catch (e) {
      console.warn('AdminLTE: jsVectorMap init failed (is the map data file loaded?)', e)
    }
  })
}

// --- FullCalendar ----------------------------------------------------------
function initCalendars() {
  if (typeof window.FullCalendar === 'undefined') return
  document.querySelectorAll('[data-fullcalendar]').forEach((el) => {
    if (el.dataset.fullcalendarReady) return
    const config = parseConfig(el, 'data-fullcalendar-config')
    new window.FullCalendar.Calendar(el, config).render()
    el.dataset.fullcalendarReady = 'true'
  })
}

// --- SortableJS (generic lists + kanban boards) ----------------------------
function initSortables() {
  if (typeof window.Sortable === 'undefined') return

  // Generic sortable lists — items in the same group can be dragged between lists.
  document.querySelectorAll('[data-sortable]').forEach((el) => {
    if (el.dataset.sortableReady) return
    const options = parseConfig(el, 'data-sortable-options')
    window.Sortable.create(el, { animation: 150, ...options })
    el.dataset.sortableReady = 'true'
  })

  // Kanban boards — every lane shares one group so cards move between lanes.
  document.querySelectorAll('[data-sortable-kanban]').forEach((board) => {
    board.querySelectorAll('[data-sortable-group]').forEach((lane) => {
      if (lane.dataset.sortableReady) return
      window.Sortable.create(lane, {
        group: 'kanban-' + (board.id || 'board'),
        animation: 150,
      })
      lane.dataset.sortableReady = 'true'
    })
  })
}

// --- Sidebar treeview a11y --------------------------------------------------
// AdminLTE's Treeview toggles .menu-open on the <li>; mirror that state onto
// the toggle link's aria-expanded so screen readers track open/closed submenus.
function initTreeviewA11y() {
  const sidebar = document.querySelector('.app-sidebar')
  if (!sidebar || typeof MutationObserver === 'undefined') return
  const observer = new MutationObserver((mutations) => {
    mutations.forEach((m) => {
      const link = m.target.querySelector(':scope > a.nav-link[aria-expanded]')
      if (link) link.setAttribute('aria-expanded', m.target.classList.contains('menu-open') ? 'true' : 'false')
    })
  })
  sidebar.querySelectorAll('li.nav-item').forEach((li) => {
    if (li.querySelector(':scope > ul.nav-treeview')) {
      observer.observe(li, { attributes: true, attributeFilter: ['class'] })
    }
  })
}

// --- Delete confirmations (SweetAlert2) ------------------------------------
function ensurePageLoader() {
  let loader = document.getElementById('admin-page-loader')

  if (loader) {
    return loader
  }

  const spinnerSrc = document.body.dataset.pageLoaderSrc || '/logo-spinner.gif'

  loader = document.createElement('div')
  loader.id = 'admin-page-loader'
  loader.className = 'admin-page-loader'
  loader.setAttribute('role', 'status')
  loader.setAttribute('aria-live', 'polite')
  loader.setAttribute('aria-busy', 'true')
  loader.innerHTML = `
    <div class="admin-page-loader__card">
      <img class="admin-page-loader__spinner" src="${spinnerSrc}" alt="" width="64" height="64" aria-hidden="true">
      <p class="admin-page-loader__text">Deleting…</p>
    </div>
  `
  document.body.appendChild(loader)

  return loader
}

function showPageLoader(message = 'Deleting…') {
  const loader = ensurePageLoader()
  const text = loader.querySelector('.admin-page-loader__text')

  if (text) {
    text.textContent = message
  }

  loader.classList.add('is-visible')
  document.body.style.overflow = 'hidden'
}

function initPageLoadingForms() {
  document.addEventListener('submit', (event) => {
    const form = event.target

    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-page-loading')) {
      return
    }

    if (form.hasAttribute('data-confirm-delete') || form.hasAttribute('data-confirm-artisan')) {
      return
    }

    showPageLoader(form.dataset.pageLoading || 'Saving…')
  })
}

function initConfirmDeletes() {
  document.addEventListener('submit', async (event) => {
    const form = event.target

    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm-delete')) {
      return
    }

    if (form.dataset.confirmDeleteReady === '1') {
      return
    }

    event.preventDefault()
    event.stopPropagation()

    const title = form.dataset.confirmTitle || 'Delete this item?'
    const text = form.dataset.confirmText || 'This action cannot be undone.'
    const confirmButtonText = form.dataset.confirmButton || 'Yes, delete it'
    const cancelButtonText = form.dataset.cancelButton || 'Cancel'
    const loadingText = form.dataset.loadingText || 'Deleting…'

    const result = await Swal.fire({
      title,
      text,
      icon: 'warning',
      showCancelButton: true,
      focusCancel: true,
      reverseButtons: true,
      confirmButtonText,
      cancelButtonText,
      buttonsStyling: false,
      customClass: {
        popup: 'swal2-adminlte',
        actions: 'gap-2',
        confirmButton: 'btn btn-danger px-3',
        cancelButton: 'btn btn-outline-secondary px-3',
      },
    })

    if (!result.isConfirmed) {
      return
    }

    showPageLoader(loadingText)
    form.dataset.confirmDeleteReady = '1'
    form.requestSubmit()
  })
}

function initConfirmArtisanRuns() {
  document.addEventListener('submit', async (event) => {
    const form = event.target

    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm-artisan')) {
      return
    }

    if (form.dataset.confirmArtisanReady === '1') {
      return
    }

    event.preventDefault()
    event.stopPropagation()

    const isDanger = form.dataset.confirmDanger === '1'
    const title = form.dataset.confirmTitle || 'Run this Artisan command?'
    const text = form.dataset.confirmText || 'This will run on the server.'
    const confirmButtonText = form.dataset.confirmButton || 'Yes, run it'
    const cancelButtonText = form.dataset.cancelButton || 'Cancel'
    const loadingText = form.dataset.loadingText || 'Running Artisan command…'

    const result = await Swal.fire({
      title,
      text,
      icon: 'warning',
      showCancelButton: true,
      focusCancel: true,
      reverseButtons: true,
      confirmButtonText,
      cancelButtonText,
      buttonsStyling: false,
      customClass: {
        popup: 'swal2-adminlte',
        actions: 'gap-2',
        confirmButton: isDanger ? 'btn btn-danger px-3' : 'btn btn-primary px-3',
        cancelButton: 'btn btn-outline-secondary px-3',
      },
    })

    if (!result.isConfirmed) {
      return
    }

    const confirmInput = form.querySelector('[data-artisan-confirm]')

    if (confirmInput instanceof HTMLInputElement) {
      confirmInput.value = '1'
    }

    showPageLoader(loadingText)
    form.dataset.confirmArtisanReady = '1'
    form.requestSubmit()
  })
}

async function initArtisanGate() {
  const gate = document.querySelector('[data-artisan-gate]')

  if (!(gate instanceof HTMLElement)) {
    return
  }

  const form = gate.querySelector('form')
  const passwordInput = gate.querySelector('input[name="password"]')
  const cancelUrl = gate.dataset.cancelUrl || '/'

  if (!(form instanceof HTMLFormElement) || !(passwordInput instanceof HTMLInputElement)) {
    return
  }

  // If the server already rendered validation errors, keep the fallback form visible.
  if (gate.querySelector('.alert-danger')) {
    return
  }

  const result = await Swal.fire({
    title: gate.dataset.title || 'Restricted page',
    text: gate.dataset.text || 'Enter your account password to continue.',
    icon: 'warning',
    input: 'password',
    inputPlaceholder: 'Account password',
    inputAttributes: {
      autocapitalize: 'off',
      autocomplete: 'current-password',
      autocorrect: 'off',
    },
    inputValidator: (value) => {
      if (!value) {
        return 'Password is required to continue.'
      }

      return null
    },
    showCancelButton: true,
    allowOutsideClick: false,
    allowEscapeKey: true,
    focusConfirm: false,
    reverseButtons: true,
    confirmButtonText: gate.dataset.confirmButton || 'Unlock',
    cancelButtonText: gate.dataset.cancelButton || 'Back to dashboard',
    buttonsStyling: false,
    customClass: {
      popup: 'swal2-adminlte',
      actions: 'gap-2',
      confirmButton: 'btn btn-warning px-3',
      cancelButton: 'btn btn-outline-secondary px-3',
    },
  })

  if (!result.isConfirmed) {
    window.location.href = cancelUrl
    return
  }

  passwordInput.value = String(result.value || '')
  showPageLoader('Unlocking Artisan Runner…')
  form.submit()
}

// --- Flash success toasts (SweetAlert2) ------------------------------------
function initFlashToasts() {
  const el = document.getElementById('admin-flash')

  if (!el) {
    return
  }

  let payload

  try {
    payload = JSON.parse(el.textContent)
  } catch (e) {
    console.warn('AdminLTE: invalid flash JSON', e)
    return
  }

  const message = typeof payload?.message === 'string' ? payload.message.trim() : ''

  if (!message) {
    return
  }

  const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    showCloseButton: false,
    timer: 4000,
    timerProgressBar: true,
    customClass: {
      popup: 'admin-flash-toast',
      htmlContainer: 'admin-flash-toast__container',
      timerProgressBar: 'admin-flash-toast__timer',
    },
    didOpen: (toast) => {
      toast.onmouseenter = Swal.stopTimer
      toast.onmouseleave = Swal.resumeTimer
    },
  })

  Toast.fire({
    html: `
      <div class="admin-flash-toast__content">
        <span class="admin-flash-toast__icon" aria-hidden="true">
          <i class="bi bi-check2-circle"></i>
        </span>
        <div class="admin-flash-toast__copy">
          <span class="admin-flash-toast__label">Success</span>
          <span class="admin-flash-toast__message"></span>
        </div>
      </div>
    `,
    didRender: (toast) => {
      const messageEl = toast.querySelector('.admin-flash-toast__message')

      if (messageEl) {
        messageEl.textContent = message
      }
    },
  })
}

whenReady(() => {
  // Wire OverlayScrollbars to the sidebar (matches the AdminLTE demo behaviour)
  const sidebar = document.querySelector('.sidebar-wrapper')
  if (sidebar && window.innerWidth > 992) {
    OverlayScrollbars(sidebar, {
      scrollbars: { theme: 'os-theme-light', autoHide: 'leave', clickScroll: true },
    })
  }

  initCharts()
  initVectorMaps()
  initCalendars()
  initSortables()
  initTreeviewA11y()
  initConfirmDeletes()
  initConfirmArtisanRuns()
  initArtisanGate()
  initPageLoadingForms()
  initFlashToasts()
})
