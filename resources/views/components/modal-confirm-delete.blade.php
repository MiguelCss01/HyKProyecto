<!-- Modal de Confirmación para Eliminar Productos / Vaciar Carrito -->
<div id="modal-confirm-delete" 
     class="fixed inset-0 top-0 left-0 w-screen h-screen z-[99999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-200 opacity-0 pointer-events-none shrink-0 grow-0" 
     style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; z-index: 99999; box-sizing: border-box;" 
     aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-confirm-title">
    
    <!-- Card del Modal -->
    <div id="modal-confirm-delete-card" 
         class="bg-white rounded-2xl w-full max-w-md mx-auto p-6 shadow-2xl border-2 border-red-200 text-center transform transition-all duration-200 scale-95 shrink-0" 
         style="width: 100%; max-width: 28rem; min-width: 280px; box-sizing: border-box; margin: auto; flex-shrink: 0;">
        
        <!-- Ícono en Círculo Rojo -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-red-600 mb-4 border-2 border-red-200" style="margin-left: auto; margin-right: auto;">
            <span id="modal-confirm-icon" class="material-symbols-outlined text-4xl" style="font-size: 36px;">delete</span>
        </div>

        <!-- Título -->
        <h3 id="modal-confirm-title" class="text-xl font-bold text-gray-900 mb-2 tracking-tight" style="color: #111827; word-break: normal;">
            ¿Eliminar producto?
        </h3>

        <!-- Tarjeta Informativa del Producto / Advertencia -->
        <div class="bg-red-50/90 border border-red-200 rounded-xl p-3.5 text-sm mb-6 text-center" style="background-color: #fef2f2; border-color: #fecaca;">
            <div id="modal-confirm-product-info" class="flex flex-wrap items-center justify-center gap-1.5 mb-1.5 hidden">
                <span id="modal-confirm-product-name" class="font-bold text-gray-900 text-sm"></span>
                <span id="modal-confirm-product-badge" class="bg-red-200/90 text-red-800 text-[11px] font-bold px-2 py-0.5 rounded uppercase"></span>
            </div>
            <p id="modal-confirm-message" class="text-xs text-red-600 leading-relaxed" style="color: #dc2626; margin: 0; font-size: 13px; line-height: 1.4;">
                Este producto será removido de tu lista de compra actual.
            </p>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-center gap-3 w-full" style="display: flex; gap: 12px; width: 100%;">
            <button type="button" onclick="closeConfirmDeleteModal()" class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold text-sm hover:bg-gray-100 transition-colors cursor-pointer" style="flex: 1; min-width: 100px; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; font-size: 14px; background: #ffffff; color: #374151;">
                Cancelar
            </button>
            <form id="modal-confirm-form" method="POST" action="" class="flex-1 m-0" style="flex: 1; margin: 0; min-width: 100px;">
                @csrf
                @method('DELETE')
                <button type="submit" id="modal-confirm-submit-btn" class="w-full px-4 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-sm shadow-md hover:shadow transition-all cursor-pointer flex items-center justify-center gap-1.5 whitespace-nowrap" style="width: 100%; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; background-color: #dc2626; color: #ffffff; border: none; display: flex; align-items: center; justify-content: center; gap: 6px; white-space: nowrap;">
                    <span class="material-symbols-outlined text-base" style="font-size: 18px;">delete</span>
                    <span id="modal-confirm-btn-text">Sí, eliminar</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    if (typeof window.openConfirmDeleteModal === 'undefined') {
        window.openConfirmDeleteModal = function (options) {
            options = options || {};
            const modal = document.getElementById('modal-confirm-delete');
            const card = document.getElementById('modal-confirm-delete-card');
            const form = document.getElementById('modal-confirm-form');
            const titleEl = document.getElementById('modal-confirm-title');
            const iconEl = document.getElementById('modal-confirm-icon');
            const productInfoEl = document.getElementById('modal-confirm-product-info');
            const productNameEl = document.getElementById('modal-confirm-product-name');
            const productBadgeEl = document.getElementById('modal-confirm-product-badge');
            const messageEl = document.getElementById('modal-confirm-message');
            const btnTextEl = document.getElementById('modal-confirm-btn-text');

            if (!modal) return;

            if (form && options.actionUrl) {
                form.action = options.actionUrl;
            }

            if (titleEl) {
                titleEl.textContent = options.title || '¿Eliminar producto?';
            }

            if (iconEl) {
                iconEl.textContent = options.icon || 'delete';
            }

            if (productNameEl && options.productName) {
                productNameEl.textContent = options.productName;
                if (productBadgeEl) {
                    if (options.productType) {
                        productBadgeEl.textContent = options.productType;
                        productBadgeEl.classList.remove('hidden');
                    } else {
                        productBadgeEl.classList.add('hidden');
                    }
                }
                if (productInfoEl) productInfoEl.classList.remove('hidden');
            } else if (productInfoEl) {
                productInfoEl.classList.add('hidden');
            }

            if (messageEl) {
                messageEl.textContent = options.message || 'Este artículo será eliminado del carrito.';
            }

            if (btnTextEl) {
                btnTextEl.textContent = options.btnText || 'Sí, eliminar';
            }

            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            if (card) {
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }
            document.body.classList.add('overflow-hidden');
        };

        window.closeConfirmDeleteModal = function () {
            const modal = document.getElementById('modal-confirm-delete');
            const card = document.getElementById('modal-confirm-delete-card');
            if (!modal) return;
            modal.classList.remove('opacity-100', 'pointer-events-auto');
            modal.classList.add('opacity-0', 'pointer-events-none');
            if (card) {
                card.classList.remove('scale-100');
                card.classList.add('scale-95');
            }
            document.body.classList.remove('overflow-hidden');
        };

        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('modal-confirm-delete');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        window.closeConfirmDeleteModal();
                    }
                });
            }
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    window.closeConfirmDeleteModal();
                }
            });
        });
    }
</script>
