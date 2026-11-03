@php
    $userBot = auth()->user();
    $isMayoristaBot = $userBot?->tipo_cliente === 'mayorista';
    $whatsappNumber = config('services.hyk.whatsapp', '5493704000000');
    $whatsappText = urlencode('Hola HyK Mayorista, necesito realizar una consulta desde el portal web.');
    $whatsappUrl = "https://wa.me/{$whatsappNumber}?text={$whatsappText}";
@endphp

<!-- Widget Flotante del Asistente Virtual con IA (RF 5 / RF 5.1) -->
<div id="hyk-chatbot-root" class="fixed bottom-20 md:bottom-6 right-4 md:right-6 z-[9990] font-sans antialiased">

    <!-- Ventana de Conversación -->
    <div id="hyk-chatbot-panel"
         class="hidden mb-3 w-[calc(100vw-2rem)] sm:w-[390px] md:w-[410px] max-h-[75vh] sm:max-h-[580px] bg-white border border-slate-200 rounded-2xl shadow-2xl flex-col overflow-hidden transition-all duration-200 origin-bottom-right"
         role="dialog"
         aria-label="Asistente Virtual HyK Mayorista">

        <!-- Cabecera del Asistente -->
        <div class="bg-[#00317e] text-white px-4 py-3.5 flex items-center justify-between shrink-0 shadow-sm">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-9 h-9 rounded-xl bg-white/15 flex items-center justify-center shrink-0 border border-white/20">
                    <span class="material-symbols-outlined text-xl text-white">smart_toy</span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h3 class="font-bold text-sm tracking-tight truncate">Asistente HyK</h3>
                        <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse" title="En línea"></span>
                    </div>
                    <p class="text-[11px] text-blue-100 truncate">
                        @auth
                            Precios {{ $isMayoristaBot ? 'Mayoristas' : 'Minoristas' }} activos
                        @else
                            Catálogo, precios y pedidos
                        @endauth
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-1 shrink-0">
                <a href="{{ $whatsappUrl }}"
                   target="_blank"
                   rel="noopener noreferrer"
                   class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold px-2.5 py-1.5 rounded-lg transition-colors shadow-xs"
                   title="Hablar con un asesor por WhatsApp">
                    <span class="material-symbols-outlined text-sm">chat</span>
                    <span class="hidden sm:inline">WhatsApp</span>
                </a>
                <button type="button"
                        id="hyk-chatbot-clear"
                        class="p-1.5 rounded-lg text-blue-100 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                        title="Reiniciar conversación">
                    <span class="material-symbols-outlined text-lg">restart_alt</span>
                </button>
                <button type="button"
                        id="hyk-chatbot-close"
                        class="p-1.5 rounded-lg text-blue-100 hover:text-white hover:bg-white/10 transition-colors cursor-pointer"
                        aria-label="Cerrar asistente">
                    <span class="material-symbols-outlined text-lg">close</span>
                </button>
            </div>
        </div>

        <!-- Contenedor de Mensajes -->
        <div id="hyk-chatbot-messages"
             class="flex-1 overflow-y-auto p-3.5 space-y-3 bg-slate-50 min-h-[280px] max-h-[370px]">
            <!-- Cargando inicial -->
            <div id="hyk-chatbot-loading" class="flex items-center justify-center py-10 text-xs text-slate-500 gap-2">
                <span class="material-symbols-outlined animate-spin text-[#00317e]">progress_activity</span>
                <span>Conectando con el asistente...</span>
            </div>
        </div>

        <!-- Notificación rápida (Toast de Carrito) -->
        <div id="hyk-chatbot-toast"
             class="hidden px-3.5 py-2 bg-emerald-700 text-white text-xs font-semibold items-center justify-between gap-2 shrink-0">
            <span id="hyk-chatbot-toast-text" class="truncate">Producto agregado al carrito</span>
            <a href="{{ route('carrito.index') }}" class="underline font-bold shrink-0 hover:text-emerald-100">Ver carrito &rarr;</a>
        </div>

        <!-- Chips de Sugerencias Rápidas -->
        <div class="px-3 py-2 bg-white border-t border-slate-200/80 shrink-0">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 px-0.5">Consultas frecuentes:</p>
            <div id="hyk-chatbot-suggestions" class="flex gap-1.5 overflow-x-auto pb-1 scrollbar-none">
                <!-- Se puebla dinámicamente -->
            </div>
        </div>

        <!-- Formulario de Entrada -->
        <form id="hyk-chatbot-form" class="p-2.5 bg-white border-t border-slate-200 flex items-center gap-2 shrink-0">
            <input type="text"
                   id="hyk-chatbot-input"
                   maxlength="500"
                   autocomplete="off"
                   placeholder="Preguntá por productos, precios o tu pedido..."
                   class="flex-1 text-xs sm:text-sm bg-slate-100 border border-slate-300 rounded-xl px-3.5 py-2.5 text-slate-900 placeholder-slate-500 focus:outline-none focus:bg-white focus:border-[#00317e] focus:ring-1 focus:ring-[#00317e] transition-all"/>
            <button type="submit"
                    id="hyk-chatbot-send"
                    class="bg-[#00317e] hover:bg-[#0046ad] disabled:opacity-50 text-white h-10 px-3.5 rounded-xl font-bold text-xs flex items-center justify-center gap-1 shadow-sm transition-all cursor-pointer shrink-0">
                <span class="material-symbols-outlined text-lg">send</span>
            </button>
        </form>
    </div>

    <!-- Botón Flotante Principal (FAB) -->
    <div class="flex justify-end">
        <button type="button"
                id="hyk-chatbot-toggle"
                class="group bg-[#00317e] hover:bg-[#0046ad] active:scale-95 text-white rounded-full p-3.5 sm:px-5 sm:py-3.5 shadow-xl hover:shadow-2xl border-2 border-white flex items-center gap-2.5 transition-all cursor-pointer"
                aria-label="Abrir Asistente Virtual HyK">
            <span class="relative flex items-center justify-center">
                <span class="material-symbols-outlined text-2xl" id="hyk-chatbot-fab-icon">smart_toy</span>
                <span class="absolute -top-1 -right-1 w-2.5 h-2.5 bg-emerald-400 rounded-full border border-[#00317e]"></span>
            </span>
            <span class="hidden sm:inline font-bold text-sm tracking-tight">Asistente HyK</span>
        </button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const panel = document.getElementById('hyk-chatbot-panel');
    const toggleBtn = document.getElementById('hyk-chatbot-toggle');
    const closeBtn = document.getElementById('hyk-chatbot-close');
    const clearBtn = document.getElementById('hyk-chatbot-clear');
    const fabIcon = document.getElementById('hyk-chatbot-fab-icon');
    const messagesBox = document.getElementById('hyk-chatbot-messages');
    const suggestionsBox = document.getElementById('hyk-chatbot-suggestions');
    const chatForm = document.getElementById('hyk-chatbot-form');
    const chatInput = document.getElementById('hyk-chatbot-input');
    const sendBtn = document.getElementById('hyk-chatbot-send');
    const toastBox = document.getElementById('hyk-chatbot-toast');
    const toastText = document.getElementById('hyk-chatbot-toast-text');

    if (!panel || !toggleBtn) return;

    const csrfToken = '{{ csrf_token() }}';
    const historyUrl = '{{ route('chatbot.history') }}';
    const sendUrl = '{{ route('chatbot.send') }}';
    const clearUrl = '{{ route('chatbot.clear') }}';
    const addCartUrl = '{{ route('chatbot.add-cart') }}';

    let historyLoaded = false;
    let isSending = false;

    function scrollToBottom() {
        messagesBox.scrollTop = messagesBox.scrollHeight;
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str ?? '';
        return div.innerHTML;
    }

    function showToast(msg, isError = false) {
        if (!toastBox || !toastText) return;
        toastText.textContent = msg;
        toastBox.classList.remove('hidden', 'bg-emerald-700', 'bg-red-600');
        toastBox.classList.add('flex', isError ? 'bg-red-600' : 'bg-emerald-700');
        setTimeout(() => {
            toastBox.classList.add('hidden');
            toastBox.classList.remove('flex');
        }, 4000);
    }

    function renderSuggestions(list) {
        if (!suggestionsBox || !Array.isArray(list)) return;
        suggestionsBox.innerHTML = '';
        list.forEach(item => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'shrink-0 text-[11px] font-semibold bg-slate-100 hover:bg-[#00317e] hover:text-white text-slate-700 border border-slate-200 px-2.5 py-1.5 rounded-full transition-colors cursor-pointer whitespace-nowrap';
            btn.textContent = item;
            btn.addEventListener('click', () => {
                // Quitar emoji inicial al enviar para que la consulta sea aún más limpia
                const cleanQuery = item.replace(/^[^\w¿¡]+/u, '').trim();
                chatInput.value = cleanQuery || item;
                handleSendMessage();
            });
            suggestionsBox.appendChild(btn);
        });
    }

    function buildProductsHtml(products) {
        if (!Array.isArray(products) || products.length === 0) return '';

        let cards = '<div class="mt-2.5 pt-2.5 border-t border-slate-200/80 space-y-2">';
        cards += '<p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Productos sugeridos (' + escapeHtml(products[0].tipo_precio || 'Catálogo') + '):</p>';

        products.forEach(prod => {
            const presList = Array.isArray(prod.presentaciones) ? prod.presentaciones : [];
            const defaultPres = prod.presentacion_default || presList[0];
            if (!defaultPres) return;

            let optionsHtml = '';
            presList.forEach(pr => {
                const selected = pr.id === defaultPres.id ? 'selected' : '';
                optionsHtml += `<option value="${pr.id}" data-precio="${escapeHtml(pr.precio_formateado)}">${escapeHtml(pr.tipo)} (x${pr.cantidad_contenida}) - ${escapeHtml(pr.precio_formateado)}</option>`;
            });

            cards += `
                <div class="bg-white border border-slate-200 rounded-xl p-2.5 shadow-2xs flex flex-col gap-2" data-product-card>
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-slate-900 leading-snug">${escapeHtml(prod.nombre)}</p>
                            <p class="text-[10px] text-slate-500">${escapeHtml(prod.categoria)} • Stock: ${prod.stock_actual} u.</p>
                        </div>
                        <span class="text-xs font-extrabold text-[#00317e] shrink-0" data-card-price>${escapeHtml(defaultPres.precio_formateado)}</span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <select class="flex-1 text-[11px] font-semibold bg-slate-50 border border-slate-300 rounded-lg px-2 py-1.5 text-slate-800 focus:outline-none focus:border-[#00317e]" data-pres-select>
                            ${optionsHtml}
                        </select>
                        <button type="button"
                                data-add-cart-btn
                                data-pres-id="${defaultPres.id}"
                                class="bg-[#006e25] hover:bg-emerald-800 text-white text-[11px] font-bold px-2.5 py-1.5 rounded-lg flex items-center gap-1 shrink-0 transition-colors cursor-pointer">
                            <span class="material-symbols-outlined text-sm">add_shopping_cart</span>
                            <span>Agregar</span>
                        </button>
                    </div>
                </div>
            `;
        });

        cards += '</div>';
        return cards;
    }

    function buildOrdersHtml(orders) {
        if (!Array.isArray(orders) || orders.length === 0) return '';

        let html = '<div class="mt-2.5 pt-2.5 border-t border-slate-200/80 space-y-2">';
        html += '<p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Tus pedidos:</p>';

        orders.forEach(order => {
            const badgeColor = order.pagado
                ? 'bg-emerald-100 text-emerald-800 border-emerald-200'
                : 'bg-amber-100 text-amber-800 border-amber-200';

            const payBtn = order.url_pago
                ? `<a href="${escapeHtml(order.url_pago)}" class="bg-[#009ee3] hover:bg-[#0082ba] text-white text-[11px] font-bold px-2.5 py-1 rounded-lg transition-colors">Pagar MP</a>`
                : '';

            html += `
                <div class="bg-white border border-slate-200 rounded-xl p-2.5 shadow-2xs flex flex-col gap-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-[#00317e]">Pedido #${order.id}</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full border ${badgeColor}">${escapeHtml(order.estado)}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-slate-600">
                        <span>Fecha: ${escapeHtml(order.fecha)} (${order.items_count} art.)</span>
                        <strong class="text-slate-900">${escapeHtml(order.total_formateado)}</strong>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-100">
                        ${payBtn}
                        <a href="${escapeHtml(order.url_detalle)}" class="text-[11px] font-bold text-[#00317e] hover:underline">Ver detalle &rarr;</a>
                    </div>
                </div>
            `;
        });

        html += '</div>';
        return html;
    }

    function appendMessage(msg) {
        const isUser = msg.role === 'user';
        const wrapper = document.createElement('div');
        wrapper.className = `flex flex-col ${isUser ? 'items-end' : 'items-start'}`;

        const bubble = document.createElement('div');
        bubble.className = isUser
            ? 'max-w-[85%] bg-[#00317e] text-white rounded-2xl rounded-br-xs px-3.5 py-2.5 text-xs sm:text-[13px] shadow-xs'
            : 'max-w-[92%] bg-white text-slate-800 border border-slate-200 rounded-2xl rounded-bl-xs px-3.5 py-2.5 text-xs sm:text-[13px] shadow-xs';

        const textHtml = `<div class="leading-relaxed whitespace-pre-line">${escapeHtml(msg.text)}</div>`;
        const productsHtml = !isUser ? buildProductsHtml(msg.products) : '';
        const ordersHtml = !isUser ? buildOrdersHtml(msg.orders) : '';

        bubble.innerHTML = textHtml + productsHtml + ordersHtml;
        wrapper.appendChild(bubble);

        if (msg.time) {
            const timeEl = document.createElement('span');
            timeEl.className = 'text-[10px] text-slate-400 mt-1 px-1';
            timeEl.textContent = msg.time;
            wrapper.appendChild(timeEl);
        }

        // Eventos para selector de presentación y botón de agregar al carrito
        bubble.querySelectorAll('[data-product-card]').forEach(card => {
            const select = card.querySelector('[data-pres-select]');
            const priceSpan = card.querySelector('[data-card-price]');
            const addBtn = card.querySelector('[data-add-cart-btn]');

            if (select && priceSpan && addBtn) {
                select.addEventListener('change', function () {
                    const opt = this.options[this.selectedIndex];
                    priceSpan.textContent = opt.getAttribute('data-precio') || '';
                    addBtn.setAttribute('data-pres-id', this.value);
                });
            }

            if (addBtn) {
                addBtn.addEventListener('click', async function () {
                    const presId = parseInt(this.getAttribute('data-pres-id'), 10);
                    if (!presId) return;

                    const originalHtml = this.innerHTML;
                    this.disabled = true;
                    this.innerHTML = '<span class="material-symbols-outlined text-sm animate-spin">progress_activity</span>';

                    try {
                        const res = await fetch(addCartUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrfToken
                            },
                            body: JSON.stringify({
                                presentacion_id: presId,
                                cantidad: 1
                            })
                        });

                        const data = await res.json();
                        if (res.ok && data.success) {
                            showToast(data.message || 'Agregado al carrito');
                            updateHeaderCartBadges(data.cart_count);
                        } else {
                            showToast(data.message || 'No se pudo agregar al carrito', true);
                        }
                    } catch (e) {
                        showToast('Error de conexión al agregar al carrito', true);
                    } finally {
                        this.disabled = false;
                        this.innerHTML = originalHtml;
                    }
                });
            }
        });

        messagesBox.appendChild(wrapper);
        scrollToBottom();
    }

    function updateHeaderCartBadges(newCount) {
        if (typeof newCount !== 'number') return;
        document.querySelectorAll('a[href*="carrito"] span.rounded-full').forEach(badge => {
            badge.textContent = String(newCount);
            if (newCount > 0) {
                badge.classList.remove('bg-surface-variant', 'text-on-surface-variant');
                badge.classList.add('bg-red-600', 'text-white');
            }
        });
    }

    function showTypingIndicator() {
        const indicator = document.createElement('div');
        indicator.id = 'hyk-chatbot-typing';
        indicator.className = 'flex items-center gap-2 text-xs text-slate-500 bg-white border border-slate-200 rounded-2xl px-3.5 py-2.5 w-fit shadow-2xs';
        indicator.innerHTML = `
            <span class="material-symbols-outlined text-base text-[#00317e] animate-spin">progress_activity</span>
            <span>Consultando sistema HyK...</span>
        `;
        messagesBox.appendChild(indicator);
        scrollToBottom();
    }

    function removeTypingIndicator() {
        const indicator = document.getElementById('hyk-chatbot-typing');
        if (indicator) indicator.remove();
    }

    async function loadHistory() {
        try {
            const res = await fetch(historyUrl, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await res.json();
            messagesBox.innerHTML = '';

            if (Array.isArray(data.messages)) {
                data.messages.forEach(m => appendMessage(m));
            }
            if (Array.isArray(data.suggestions)) {
                renderSuggestions(data.suggestions);
            }
            historyLoaded = true;
        } catch (e) {
            messagesBox.innerHTML = '';
            appendMessage({
                role: 'assistant',
                text: '¡Hola! Soy el Asistente Virtual de HyK Mayorista. ¿En qué puedo ayudarte hoy?'
            });
        }
    }

    async function handleSendMessage(e) {
        if (e) e.preventDefault();
        if (isSending) return;

        const text = chatInput.value.trim();
        if (!text) return;

        chatInput.value = '';
        isSending = true;
        sendBtn.disabled = true;

        appendMessage({
            role: 'user',
            text: text,
            time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
        });

        showTypingIndicator();

        try {
            const res = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ message: text })
            });

            const data = await res.json();
            removeTypingIndicator();

            if (res.ok && data.reply) {
                appendMessage({
                    role: 'assistant',
                    text: data.reply,
                    products: data.products || [],
                    orders: data.orders || [],
                    time: data.time || ''
                });
                if (Array.isArray(data.suggestions)) {
                    renderSuggestions(data.suggestions);
                }
            } else {
                appendMessage({
                    role: 'assistant',
                    text: data.message || 'Ocurrió un inconveniente al procesar tu consulta. Intentá nuevamente.'
                });
            }
        } catch (err) {
            removeTypingIndicator();
            appendMessage({
                role: 'assistant',
                text: 'No pude conectarme con el servidor en este momento. Por favor verificá tu conexión.'
            });
        } finally {
            isSending = false;
            sendBtn.disabled = false;
            chatInput.focus();
        }
    }

    function toggleChat() {
        const isHidden = panel.classList.contains('hidden');
        if (isHidden) {
            panel.classList.remove('hidden');
            panel.classList.add('flex');
            fabIcon.textContent = 'close';
            if (!historyLoaded) {
                loadHistory();
            } else {
                scrollToBottom();
            }
            setTimeout(() => chatInput.focus(), 100);
        } else {
            panel.classList.add('hidden');
            panel.classList.remove('flex');
            fabIcon.textContent = 'smart_toy';
        }
    }

    toggleBtn.addEventListener('click', toggleChat);

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            panel.classList.add('hidden');
            panel.classList.remove('flex');
            fabIcon.textContent = 'smart_toy';
        });
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', async () => {
            try {
                await fetch(clearUrl, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                historyLoaded = false;
                loadHistory();
            } catch (e) {}
        });
    }

    if (chatForm) {
        chatForm.addEventListener('submit', handleSendMessage);
    }
});
</script>
