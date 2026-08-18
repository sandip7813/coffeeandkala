/**
 * AdminLTE 4 + Bootstrap 5 entry point.
 *
 * Published by `php artisan adminlte:install`. Add this file to your
 * vite.config.js input array, then `npm run dev` / `npm run build`.
 */

// Bootstrap (provides dropdowns, modals, tooltips, offcanvas, etc.)
import 'bootstrap'
import { Collapse, Modal } from 'bootstrap'

// OverlayScrollbars — AdminLTE uses it for the sidebar scroller (optional)
import { OverlayScrollbars } from 'overlayscrollbars'

// AdminLTE plugins (PushMenu, Treeview, CardWidget, FullScreen, DirectChat,
// Layout, accessibility). The data-lte-* API is wired on DOMContentLoaded.
import 'admin-lte'

import Swal from 'sweetalert2'

// jQuery + Select2 — used for AJAX autocomplete dropdowns (e.g. the users search field)
import jQuery from 'jquery'
import initSelect2 from 'select2/dist/js/select2.full.js'
import 'select2/dist/css/select2.min.css'
import 'select2-bootstrap-5-theme/dist/select2-bootstrap-5-theme.min.css'

// moment — required by daterangepicker below
import moment from 'moment'

window.$ = window.jQuery = jQuery
window.moment = moment

// daterangepicker — used for the "assigned date" range field on the quotes search panel.
// Loaded with a *dynamic* import (see loadDateRangePicker below) rather than a static one:
// ALL static imports in a module are evaluated before any of that module's own top-level
// code runs, no matter where the `import` line sits in the file — so a static import here
// would still run before `window.jQuery`/`window.moment` above are set, and its UMD wrapper
// would silently fall back to a mismatched jQuery instance (symptom: "e is not a function").
import 'daterangepicker/daterangepicker.css'

// Select2's UMD wrapper exports an uninvoked factory under bundlers — call it
// explicitly so it attaches itself to our jQuery instance.
initSelect2(window, jQuery)

// Select2 4.0.x's AJAX data adapter re-normalizes each result via an
// *unbound* `SelectAdapter.prototype._normalizeItem` reference
// (`results.results.map(AjaxAdapter.prototype._normalizeItem)`). Under a
// sloppy-mode <script> tag `this` falls back to `window` there, so the
// `this.container` read is harmless (`window.container` is just
// undefined). Under an ES module bundler every chunk is strict mode, so
// `this` is `undefined` instead and the same read throws
// "Cannot read properties of undefined (reading 'container')" the moment
// AJAX results come back. Patch the method to tolerate a missing `this`,
// matching the (already effectively no-op) behaviour it has when unbound.
jQuery.fn.select2.amd.require(['select2/data/select'], (SelectAdapter) => {
  const normalizeItem = function (item) {
    const self = this || {}

    if (item !== Object(item)) {
      item = { id: item, text: item }
    }

    item = jQuery.extend({}, { text: '' }, item)

    const defaults = { selected: false, disabled: false }

    if (item.id != null) {
      item.id = item.id.toString()
    }

    if (item.text != null) {
      item.text = item.text.toString()
    }

    if (item._resultId == null && item.id && self.container != null) {
      item._resultId = self.generateResultId(self.container, item)
    }

    if (item.children) {
      item.children = item.children.map(normalizeItem)
    }

    return jQuery.extend({}, defaults, item)
  }

  SelectAdapter.prototype._normalizeItem = normalizeItem
})
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

function initCollapsibleSearchPanels() {
  document.querySelectorAll('[data-search-toggle]').forEach((toggle) => {
    const targetSelector = toggle.dataset.searchToggle || toggle.getAttribute('data-bs-target')
    const target = targetSelector ? document.querySelector(targetSelector) : null

    if (!target) {
      return
    }

    const collapse = Collapse.getOrCreateInstance(target, { toggle: false })

    toggle.addEventListener('click', () => collapse.toggle())
    target.addEventListener('show.bs.collapse', () => toggle.setAttribute('aria-expanded', 'true'))
    target.addEventListener('hide.bs.collapse', () => toggle.setAttribute('aria-expanded', 'false'))
  })
}

function initPageLoadingForms() {
  document.addEventListener('submit', (event) => {
    const form = event.target

    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-page-loading')) {
      return
    }

    if (form.hasAttribute('data-confirm-delete') || form.hasAttribute('data-confirm-artisan') || form.hasAttribute('data-confirm-toggle')) {
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

function initConfirmToggles() {
  document.addEventListener('submit', async (event) => {
    const form = event.target

    if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm-toggle')) {
      return
    }

    if (form.dataset.confirmToggleReady === '1') {
      return
    }

    event.preventDefault()
    event.stopPropagation()

    const title = form.dataset.confirmTitle || 'Change status?'
    const text = form.dataset.confirmText || 'This will update the status.'
    const confirmButtonText = form.dataset.confirmButton || 'Yes, change it'
    const cancelButtonText = form.dataset.cancelButton || 'Cancel'
    const loadingText = form.dataset.loadingText || 'Processing…'

    const result = await Swal.fire({
      title,
      text,
      icon: 'question',
      showCancelButton: true,
      focusCancel: true,
      reverseButtons: true,
      confirmButtonText,
      cancelButtonText,
      buttonsStyling: false,
      customClass: {
        popup: 'swal2-adminlte',
        actions: 'gap-2',
        confirmButton: 'btn btn-primary px-3',
        cancelButton: 'btn btn-outline-secondary px-3',
      },
    })

    if (!result.isConfirmed) {
      return
    }

    showPageLoader(loadingText)
    form.dataset.confirmToggleReady = '1'
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

async function promptArtisanUnlock(config, options = {}) {
  const cancelGoesToDashboard = options.cancelGoesToDashboard === true
  const cancelButtonText = options.cancelButtonText
    || (cancelGoesToDashboard ? 'Back to dashboard' : 'Cancel')

  const result = await Swal.fire({
    title: 'Restricted page',
    text: 'Artisan Runner can change this application. Enter your account password to continue.',
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
    showCloseButton: true,
    showLoaderOnConfirm: true,
    allowOutsideClick: () => !Swal.isLoading(),
    allowEscapeKey: () => !Swal.isLoading(),
    focusConfirm: false,
    reverseButtons: true,
    confirmButtonText: 'Unlock',
    cancelButtonText,
    buttonsStyling: false,
    customClass: {
      popup: 'swal2-adminlte',
      actions: 'gap-2',
      confirmButton: 'btn btn-warning px-3',
      cancelButton: 'btn btn-outline-secondary px-3',
    },
    preConfirm: async (password) => {
      try {
        const response = await fetch(config.unlockUrl, {
          method: 'POST',
          headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': config.csrf,
            'X-Requested-With': 'XMLHttpRequest',
          },
          credentials: 'same-origin',
          body: JSON.stringify({ password }),
        })

        const data = await response.json().catch(() => ({}))

        if (!response.ok) {
          const message = data?.errors?.password?.[0]
            || data?.message
            || 'Unable to unlock Artisan Runner.'

          Swal.showValidationMessage(message)

          return false
        }

        return data
      } catch (error) {
        Swal.showValidationMessage('Unable to unlock Artisan Runner. Please try again.')

        return false
      }
    },
  })

  if (result.isConfirmed) {
    config.unlocked = true
    showPageLoader('Opening Artisan Runner…')
    window.location.href = result.value?.redirect || config.targetUrl

    return true
  }

  if (cancelGoesToDashboard && result.dismiss === Swal.DismissReason.cancel) {
    window.location.href = config.dashboardUrl
  }

  return false
}

function readArtisanGateConfig() {
  const el = document.getElementById('artisan-runner-gate')

  if (!el) {
    return null
  }

  try {
    return JSON.parse(el.textContent)
  } catch (error) {
    console.warn('AdminLTE: invalid artisan runner gate JSON', error)

    return null
  }
}

function isArtisanIndexUrl(url, targetUrl) {
  try {
    const href = new URL(url, window.location.origin)
    const target = new URL(targetUrl, window.location.origin)

    return href.pathname.replace(/\/$/, '') === target.pathname.replace(/\/$/, '')
  } catch (error) {
    return false
  }
}

function initArtisanSidebarUnlock() {
  const config = readArtisanGateConfig()

  if (!config) {
    return
  }

  document.addEventListener('click', async (event) => {
    const link = event.target instanceof Element ? event.target.closest('a') : null

    if (!(link instanceof HTMLAnchorElement)) {
      return
    }

    if (!isArtisanIndexUrl(link.href, config.targetUrl)) {
      return
    }

    if (config.unlocked) {
      return
    }

    if (Swal.isVisible()) {
      event.preventDefault()
      event.stopPropagation()

      return
    }

    event.preventDefault()
    event.stopPropagation()

    await promptArtisanUnlock(config, {
      cancelGoesToDashboard: false,
      cancelButtonText: 'Cancel',
    })
  }, true)
}

async function initArtisanGate() {
  const gate = document.querySelector('[data-artisan-gate]')
  const config = readArtisanGateConfig()

  if (!(gate instanceof HTMLElement) || !config || config.unlocked) {
    return
  }

  // If the server already rendered validation errors, keep the fallback form visible.
  if (gate.querySelector('.alert-danger')) {
    return
  }

  if (Swal.isVisible()) {
    return
  }

  await promptArtisanUnlock(config, {
    cancelGoesToDashboard: true,
    cancelButtonText: gate.dataset.cancelButton || 'Back to dashboard',
  })
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

function initValidationErrorAlert() {
  const el = document.getElementById('admin-validation-errors')

  if (!el) {
    return
  }

  let messages

  try {
    messages = JSON.parse(el.textContent)
  } catch (e) {
    console.warn('AdminLTE: invalid validation errors JSON', e)
    return
  }

  if (!Array.isArray(messages) || messages.length === 0) {
    return
  }

  const escapeHtml = (value) => value.replace(/[&<>"']/g, (char) => ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    '"': '&quot;',
    "'": '&#39;',
  })[char])

  const list = messages.map((message) => `<li>${escapeHtml(String(message))}</li>`).join('')

  Swal.fire({
    icon: 'error',
    title: messages.length === 1 ? 'Please fix the following' : `Please fix the following ${messages.length} issues`,
    html: `<ul class="text-start mb-0 ps-3">${list}</ul>`,
    confirmButtonText: 'OK',
    buttonsStyling: false,
    customClass: {
      popup: 'swal2-adminlte',
      confirmButton: 'btn btn-primary px-3',
    },
  })
}

// --- Password visibility toggles --------------------------------------------
function initPasswordToggles() {
  document.querySelectorAll('.js-password-toggle').forEach((btn) => {
    btn.addEventListener('click', () => {
      const wrapper = btn.closest('.input-group') || btn.parentElement
      const input = wrapper?.querySelector('input[type="password"], input[type="text"].js-password-input')

      if (!input) {
        return
      }

      const willShow = input.type === 'password'
      input.type = willShow ? 'text' : 'password'
      input.classList.toggle('js-password-input', willShow)

      const icon = btn.querySelector('i')
      if (icon) {
        icon.classList.toggle('bi-eye', !willShow)
        icon.classList.toggle('bi-eye-slash', willShow)
      }

      btn.setAttribute('aria-label', willShow ? 'Hide password' : 'Show password')
    })
  })
}

// --- Role permission group "select all" toggles -----------------------------
function initPermissionGroupToggles() {
  document.querySelectorAll('.permission-group').forEach((card) => {
    const toggle = card.querySelector('.js-permission-group-toggle')
    const checkboxes = Array.from(card.querySelectorAll('.js-permission-checkbox'))

    if (!toggle || checkboxes.length === 0) {
      return
    }

    const syncToggle = () => {
      const checkedCount = checkboxes.filter((checkbox) => checkbox.checked).length
      toggle.checked = checkedCount === checkboxes.length
      toggle.indeterminate = checkedCount > 0 && checkedCount < checkboxes.length
    }

    toggle.addEventListener('change', () => {
      checkboxes.forEach((checkbox) => {
        checkbox.checked = toggle.checked
      })
      toggle.indeterminate = false
    })

    checkboxes.forEach((checkbox) => checkbox.addEventListener('change', syncToggle))

    syncToggle()
  })
}

// --- Reopen a modal after a validation redirect -----------------------------
function initReopenModal() {
  const el = document.getElementById('admin-reopen-modal')

  if (!el) {
    return
  }

  let modalId

  try {
    modalId = JSON.parse(el.textContent)
  } catch (e) {
    console.warn('AdminLTE: invalid reopen-modal JSON', e)
    return
  }

  if (typeof modalId !== 'string' || !modalId) {
    return
  }

  const modalEl = document.getElementById(modalId)

  if (!modalEl) {
    return
  }

  Modal.getOrCreateInstance(modalEl).show()
}

// --- Date range picker (Quotes search panel) --------------------------------
// Loaded on demand via a *dynamic* import — by the time this promise resolves,
// window.jQuery/window.moment (set above) are already in place, unlike with a
// static import (see the comment near those assignments for why that matters).
function loadDateRangePicker() {
  return import('daterangepicker').then((mod) => mod.default || mod)
}

// --- Fancybox (Gallery/Studio thumbnail lightbox) ---------------------------
// Loaded on demand via a *dynamic* import, for the same reason as
// daterangepicker above: it's a jQuery UMD plugin that needs window.jQuery
// already in place when it runs.
//
// Fancybox 3 calls several $.xxx helpers (isArray, isFunction, isNumeric,
// isPlainObject, trim, type) that jQuery removed in v4 — this project is on
// jQuery ^4, so without these shims opening the lightbox throws
// "n.isArray is not a function".
function shimJqueryForFancybox() {
  jQuery.isArray ??= Array.isArray
  jQuery.isFunction ??= (value) => typeof value === 'function'
  jQuery.isNumeric ??= (value) => !isNaN(parseFloat(value)) && isFinite(value)
  jQuery.isPlainObject ??= (value) =>
    typeof value === 'object' && value !== null && value.constructor === Object
  jQuery.trim ??= (value) => (value == null ? '' : String(value).trim())
  jQuery.type ??= (value) => (value === null ? 'null' : typeof value)
}

function loadFancybox() {
  shimJqueryForFancybox()

  return import('@fancyapps/fancybox/dist/jquery.fancybox.js').then(() => jQuery.fancybox)
}

function initFancybox() {
  const elements = document.querySelectorAll('[data-fancybox]')

  if (elements.length === 0) {
    return
  }

  loadFancybox()
}

function initDateRangePicker() {
  const elements = document.querySelectorAll('[data-daterangepicker]')

  if (elements.length === 0) {
    return
  }

  loadDateRangePicker().then((DateRangePicker) => {
    elements.forEach((el) => {
      const startInput = document.querySelector(el.dataset.startInput)
      const endInput = document.querySelector(el.dataset.endInput)

      if (!startInput || !endInput) {
        return
      }

      const hasRange = Boolean(startInput.value && endInput.value)

      const picker = new DateRangePicker(
        el,
        {
          autoUpdateInput: true,
          autoApply: false,
          opens: 'left',
          locale: { format: 'MMM D, YYYY', cancelLabel: 'Clear' },
          ...(hasRange ? { startDate: startInput.value, endDate: endInput.value } : {}),
        },
        (start, end) => {
          startInput.value = start.format('YYYY-MM-DD')
          endInput.value = end.format('YYYY-MM-DD')
        }
      )

      if (!hasRange) {
        el.value = ''
      }

      // "Clear" (the cancel button, relabelled above) empties the field instead of
      // just closing the picker on the last-shown (today's) range.
      picker.container?.[0]?.querySelector('.cancelBtn')?.addEventListener('click', () => {
        el.value = ''
        startInput.value = ''
        endInput.value = ''
      })
    })
  })
}

function initSelect2Search() {
  document.querySelectorAll('[data-select2-search]').forEach((el) => {
    const setup = () => {
      if (jQuery(el).data('select2')) {
        return
      }

      // Note: Select2's `tags: true` is not supported alongside a remote
      // `ajax` source (it corrupts the results/selection state and throws
      // "Cannot read properties of undefined (reading 'container')"), so
      // this stays a pick-a-suggestion autocomplete rather than free tagging.
      jQuery(el).select2({
        theme: 'bootstrap-5',
        width: '100%',
        allowClear: true,
        placeholder: el.dataset.placeholder || '',
        minimumInputLength: 3,
        ajax: {
          url: el.dataset.select2Url,
          dataType: 'json',
          delay: 300,
          data: (params) => ({ q: params.term }),
          processResults: (data) => ({ results: data.results || [] }),
        },
      })
    }

    // Select2 measures its container for positioning, which fails when the
    // field starts out inside a collapsed (display: none) panel. Defer
    // initialisation until the panel is actually visible.
    const collapseParent = el.closest('.collapse')

    if (collapseParent && !collapseParent.classList.contains('show')) {
      collapseParent.addEventListener('shown.bs.collapse', setup, { once: true })
    } else {
      setup()
    }
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

  // Each init runs in isolation: a page-specific error thrown by one (e.g. a
  // chart/calendar widget only present on some pages) would otherwise abort
  // this whole callback and silently skip every init still queued after it —
  // including, on pages with no charts/calendars at all, ones as unrelated as
  // the collapsible search panel toggle.
  const inits = [
    initCharts,
    initVectorMaps,
    initCalendars,
    initSortables,
    initTreeviewA11y,
    initCollapsibleSearchPanels,
    initConfirmDeletes,
    initConfirmToggles,
    initConfirmArtisanRuns,
    initArtisanSidebarUnlock,
    initArtisanGate,
    initPageLoadingForms,
    initFlashToasts,
    initValidationErrorAlert,
    initPasswordToggles,
    initPermissionGroupToggles,
    initReopenModal,
    initSelect2Search,
    initDateRangePicker,
    initFancybox,
  ]

  for (const init of inits) {
    try {
      init()
    } catch (error) {
      console.error(`adminlte.js: ${init.name} failed`, error)
    }
  }
})
