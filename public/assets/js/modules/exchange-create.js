document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const liveSearch = document.getElementById('liveSearch');
    const liveSearchResults = document.getElementById('liveSearchResults');
    const exchangeFormCard = document.getElementById('exchangeFormCard');
    const selectedUserInfo = document.getElementById('selectedUserInfo');
    const selectedUserDisplay = document.getElementById('selectedUserDisplay');
    const toUserId = document.getElementById('to_user_id');
    const fromAmount = document.getElementById('from_amount');
    const fromCurrency = document.getElementById('from_currency');
    const toAmount = document.getElementById('to_amount');
    const toCurrencyDisplay = document.getElementById('to_currency_display');
    const exchangeRateInfo = document.getElementById('exchange_rate_info');
    const exchangeInfo = document.getElementById('exchange_info');
    const exchangeSummary = document.getElementById('exchange_summary');
    const exchangeDetails = document.getElementById('exchange_details');
    const calculateBtn = document.getElementById('calculateBtn');
    const submitBtn = document.getElementById('submitBtn');
    const deselectUserBtn = document.getElementById('deselectUser');

    // Seleccionar usuario desde resultados de búsqueda
    document.querySelectorAll('.select-user').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const userName = this.getAttribute('data-user-name');
            const userCurrency = this.getAttribute('data-user-currency');
            const userDisplay = this.getAttribute('data-user-display');

            toUserId.value = userId;
            selectedUserDisplay.textContent = userDisplay;
            selectedUserInfo.style.display = 'block';
            exchangeFormCard.style.display = 'block';
            submitBtn.disabled = false;

            // Establecer moneda destino
            toCurrencyDisplay.textContent = userCurrency;

            // Scroll al formulario
            exchangeFormCard.scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Deseleccionar usuario
    if (deselectUserBtn) {
        deselectUserBtn.addEventListener('click', function() {
            selectedUserInfo.style.display = 'none';
            exchangeFormCard.style.display = 'none';
            toUserId.value = '';
            submitBtn.disabled = true;
        });
    }

    // Búsqueda en tiempo real
    let liveSearchTimeout;
    if (liveSearch) {
        liveSearch.addEventListener('input', function() {
            clearTimeout(liveSearchTimeout);
            const query = this.value.trim();

            if (query.length < 2) {
                liveSearchResults.style.display = 'none';
                return;
            }

            liveSearchTimeout = setTimeout(() => {
                fetch(`/intercambios/buscar?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(users => {
                        if (users.length > 0) {
                            liveSearchResults.innerHTML = users.map(user => `
                                <div class="card mb-2">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <strong>${user.name}</strong>
                                                <br>
                                                <small class="text-muted">ID: ${user.user_id} | ${user.email} | ${user.currency}</small>
                                            </div>
                                            <button type="button"
                                                    class="btn btn-primary btn-sm"
                                                    onclick="selectUserFromLiveSearch(${user.id}, '${user.name.replace(/'/g, "\\'")}', '${user.currency}', '${user.display_text.replace(/'/g, "\\'")}')">
                                                <i class="bi bi-arrow-right-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            `).join('');
                            liveSearchResults.style.display = 'block';
                        } else {
                            liveSearchResults.innerHTML = '<div class="text-muted text-center">No se encontraron usuarios</div>';
                            liveSearchResults.style.display = 'block';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }, 500);
        });
    }

    // Calcular intercambio
    if (calculateBtn) {
        calculateBtn.addEventListener('click', calculateExchange);
    }

    function calculateExchange() {
        if (!toUserId.value || !fromAmount.value) {
            alert('Por favor selecciona un usuario y ingresa un monto');
            return;
        }

        const formData = new FormData();
        formData.append('from_amount', fromAmount.value);
        formData.append('from_currency', fromCurrency.value);
        formData.append('to_currency', toCurrencyDisplay.textContent);

        fetch('/intercambios/calcular', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }

            toAmount.value = data.to_amount;
            exchangeRateInfo.textContent = `Tasa de cambio: 1 ${data.from_currency} = ${data.exchange_rate} ${data.to_currency}`;

            // Mostrar información del intercambio
            exchangeSummary.textContent = `Enviarás ${fromAmount.value} ${fromCurrency.value} y recibirás ${data.to_amount} ${data.to_currency}`;
            exchangeDetails.textContent = `Tasa aplicada: 1 ${data.from_currency} = ${data.exchange_rate} ${data.to_currency}`;
            exchangeInfo.style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al calcular el intercambio');
        });
    }
});

// Función global para seleccionar usuario desde búsqueda en tiempo real
function selectUserFromLiveSearch(userId, userName, userCurrency, userDisplay) {
    document.getElementById('to_user_id').value = userId;
    document.getElementById('selectedUserDisplay').textContent = userDisplay;
    document.getElementById('selectedUserInfo').style.display = 'block';
    document.getElementById('exchangeFormCard').style.display = 'block';
    document.getElementById('submitBtn').disabled = false;
    document.getElementById('to_currency_display').textContent = userCurrency;
    document.getElementById('liveSearchResults').style.display = 'none';
    document.getElementById('liveSearch').value = '';

    // Scroll al formulario
    document.getElementById('exchangeFormCard').scrollIntoView({ behavior: 'smooth' });
}
