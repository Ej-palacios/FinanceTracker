(function () {
  const LOADER_ID = 'custom-loader';
  const NO_SCROLL_CLASS = 'no-scroll';
  const LOADING_BODY_CLASS = 'loading';
  const CONTENT_WRAPPER_SELECTOR = '.content-wrapper';
  const SHOW_DELAY_ON_LOAD_MS = 120; // "unas milésimas" antes de cerrar
  const FADE_DURATION_MS = 300; // debe coincidir con CSS

  function ensureLoaderExists() {
    let loader = document.getElementById(LOADER_ID);
    if (!loader) {
      // El layout incluye el blade del loader; si no se renderizó por algún motivo, crearlo minimalmente
      loader = document.createElement('div');
      loader.id = LOADER_ID;
      loader.className = 'app-loader';
      loader.innerHTML = `
        <div class="coins-scene">
          <div class="center-core"><div class="core-ring"></div><div class="core-glow"></div></div>
          <div class="orbit orbit-1"><div class="coin" data-currency="USD">$</div></div>
          <div class="orbit orbit-2"><div class="coin" data-currency="EUR">€</div></div>
          <div class="orbit orbit-3"><div class="coin" data-currency="GBP">£</div></div>
          <div class="orbit orbit-4"><div class="coin" data-currency="BTC">₿</div></div>
        </div>
        <div class="loader-text">Cargando...</div>`;
      document.body.appendChild(loader);
    }
    return loader;
  }

  function hideMainContent() {
    const mainContent = document.querySelector(CONTENT_WRAPPER_SELECTOR);
    if (mainContent) {
      mainContent.style.visibility = 'hidden';
    }
  }

  function showMainContent() {
    const mainContent = document.querySelector(CONTENT_WRAPPER_SELECTOR);
    if (mainContent) {
      mainContent.style.visibility = '';
    }
  }

  function showLoader() {
    const loader = ensureLoaderExists();
    loader.style.display = 'flex';
    requestAnimationFrame(() => {
      loader.style.opacity = '1';
    });
    document.body.classList.add(NO_SCROLL_CLASS);
    document.body.classList.add(LOADING_BODY_CLASS);
    hideMainContent();
  }

  function hideLoader() {
    const loader = ensureLoaderExists();
    loader.style.opacity = '0';
    setTimeout(() => {
      loader.style.display = 'none';
      showMainContent();
      document.body.classList.remove(LOADING_BODY_CLASS);
    }, FADE_DURATION_MS);
    document.body.classList.remove(NO_SCROLL_CLASS);
  }

  // Al iniciar: mostrar loader inmediatamente y ocultar contenido
  function boot() {
    showLoader();

    // Cerrar ligeramente después de load
    if (document.readyState === 'complete') {
      setTimeout(hideLoader, SHOW_DELAY_ON_LOAD_MS);
    } else {
      window.addEventListener('load', () => {
        setTimeout(hideLoader, SHOW_DELAY_ON_LOAD_MS);
      });
    }

    // Navegación interna: mostrar loader
    document.addEventListener('click', (e) => {
      const link = e.target.closest('a');
      if (
        link &&
        !link.target &&
        !link.href.startsWith('javascript:') &&
        !link.href.startsWith('mailto:') &&
        !link.href.startsWith('tel:') &&
        !link.dataset.noload &&
        link.hostname === window.location.hostname &&
        !link.href.includes('#')
      ) {
        e.preventDefault();
        showLoader();
        // Pequeña espera para feedback visual
        setTimeout(() => { window.location.href = link.href; }, 80);
      }
    });

    // Manejar volver desde cache (bfcache)
    window.addEventListener('pageshow', (event) => {
      if (event.persisted) {
        setTimeout(hideLoader, SHOW_DELAY_ON_LOAD_MS);
      }
    });

    // Interceptar fetch
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
      showLoader();
      return originalFetch.apply(this, args)
        .then((res) => { setTimeout(hideLoader, SHOW_DELAY_ON_LOAD_MS); return res; })
        .catch((err) => { setTimeout(hideLoader, SHOW_DELAY_ON_LOAD_MS); throw err; });
    };

    // Interceptar XHR
    const originalOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function(method, url, ...rest) {
      this.addEventListener('loadstart', () => { showLoader(); });
      this.addEventListener('loadend', () => { setTimeout(hideLoader, SHOW_DELAY_ON_LOAD_MS); });
      return originalOpen.call(this, method, url, ...rest);
    };

    // Formularios
    document.addEventListener('submit', (e) => {
      const form = e.target;
      if (!form.hasAttribute('data-no-loader')) {
        showLoader();
      }
    });
  }

  // Exponer API
  window.FinanceLoader = { show: showLoader, hide: hideLoader };

  // Lanzar
  boot();
})();
