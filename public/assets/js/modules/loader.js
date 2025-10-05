(function () {
    // Configuración global
    const LOADER_ID = 'custom-loader';
    const NO_SCROLL_CLASS = 'no-scroll';
    const TRANSITION_DURATION = 500;

    // Crear loader si no existe
    function createLoader() {
        if (document.getElementById(LOADER_ID)) return;

        const loader = document.createElement('div');
        loader.id = LOADER_ID;
        loader.innerHTML = `
            <div class="loader">
                <div class="orbit-container">
                    <div class="heart"></div>
                    <div class="coin coin1"><i class="bi bi-coin"></i></div>
                    <div class="coin coin2"><i class="bi bi-coin"></i></div>
                    <div class="coin coin3"><i class="bi bi-coin"></i></div>
                    <div class="coin coin4"><i class="bi bi-coin"></i></div>
                </div>
                <span>Loading</span>
            </div>
        `;
        document.body.appendChild(loader);
    }

    // Mostrar loader con animación
    function showLoader() {
        const loader = document.getElementById(LOADER_ID);
        if (!loader) return;

        loader.style.display = 'flex';
        loader.offsetHeight; // Forzar reflow para animación
        loader.style.opacity = '1';
        document.body.classList.add(NO_SCROLL_CLASS);
    }

    // Ocultar loader con animación
    function hideLoader() {
        const loader = document.getElementById(LOADER_ID);
        if (!loader) return;

        loader.style.opacity = '0';
        setTimeout(() => {
            loader.style.display = 'none';
        }, TRANSITION_DURATION);

        document.body.classList.remove(NO_SCROLL_CLASS);
    }

    // Inicializar loader al cargar DOM
    function initLoaderOnLoad() {
        if (document.readyState === 'complete') {
            hideLoader();
        } else {
            window.addEventListener('load', () => {
                hideLoader();
            });
        }
    }

    // Manejar navegaciones internas
    function handleNavigationClicks() {
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

                setTimeout(() => {
                    window.location.href = link.href;
                }, 50);
            }
        });
    }

    // Manejar navegación mediante botones de navegador
    function handlePageShowEvents() {
        window.addEventListener('pageshow', (event) => {
            if (event.persisted) {
                hideLoader();
            }
        });
    }

    // Interceptar fetch requests para mostrar loader en AJAX
    function interceptFetchRequests() {
        const originalFetch = window.fetch;

        window.fetch = function(...args) {
            // Mostrar loader para requests AJAX
            showLoader();

            return originalFetch.apply(this, args)
                .then(response => {
                    // Ocultar loader cuando la respuesta llegue
                    hideLoader();
                    return response;
                })
                .catch(error => {
                    // Ocultar loader en caso de error
                    hideLoader();
                    throw error;
                });
        };
    }

    // Interceptar XMLHttpRequest para compatibilidad
    function interceptXMLHttpRequests() {
        const originalOpen = XMLHttpRequest.prototype.open;

        XMLHttpRequest.prototype.open = function(method, url, ...args) {
            this.addEventListener('loadstart', () => {
                // Solo mostrar loader para requests que no sean navegación
                if (!url.includes(window.location.origin + window.location.pathname)) {
                    showLoader();
                }
            });

            this.addEventListener('loadend', () => {
                hideLoader();
            });

            return originalOpen.call(this, method, url, ...args);
        };
    }

    // Manejar envío de formularios
    function handleFormSubmissions() {
        document.addEventListener('submit', (e) => {
            const form = e.target;
            // Solo mostrar loader si no tiene data-no-loader
            if (!form.hasAttribute('data-no-loader')) {
                showLoader();
            }
        });
    }

    // Exponer funciones globalmente para uso manual
    window.FinanceLoader = {
        show: showLoader,
        hide: hideLoader
    };

    // Ejecución principal
    createLoader();
    showLoader(); // Mostrar inmediatamente
    initLoaderOnLoad();
    handleNavigationClicks();
    handlePageShowEvents();
    interceptFetchRequests();
    interceptXMLHttpRequests();
    handleFormSubmissions();
})();
