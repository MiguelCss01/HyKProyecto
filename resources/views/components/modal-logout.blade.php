<!-- Modal de Confirmación de Cierre de Sesión -->
<div id="modal-logout" 
     class="fixed inset-0 top-0 left-0 w-screen h-screen z-[99999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm transition-all duration-200 opacity-0 pointer-events-none shrink-0 grow-0" 
     style="position: fixed; inset: 0; top: 0; left: 0; right: 0; bottom: 0; width: 100vw; height: 100vh; z-index: 99999; box-sizing: border-box;" 
     aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-logout-title">
    
    <!-- Card del Modal -->
    <div id="modal-logout-card" 
         class="bg-white rounded-2xl w-full max-w-md mx-auto p-6 shadow-2xl border-2 border-red-200 text-center transform transition-all duration-200 scale-95 shrink-0" 
         style="width: 100%; max-width: 28rem; min-width: 280px; box-sizing: border-box; margin: auto; flex-shrink: 0;">
        
        <!-- Ícono en Círculo Rojo -->
        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 text-red-600 mb-4 border-2 border-red-200" style="margin-left: auto; margin-right: auto;">
            <span class="material-symbols-outlined text-4xl" style="font-size: 36px;">logout</span>
        </div>

        <!-- Título en Rojo -->
        <h3 id="modal-logout-title" class="text-xl font-bold text-red-600 mb-2 tracking-tight" style="color: #ba1a1a; word-break: normal;">
            ¿Estás seguro de salir?
        </h3>

        <!-- Mensaje de Advertencia en Rojo -->
        <div class="bg-red-50 border border-red-200 text-red-700 rounded-xl p-3 text-sm mb-6 text-center" style="background-color: #fef2f2; border-color: #fecaca; color: #b91c1c;">
            <p class="font-semibold text-red-700" style="color: #b91c1c; margin: 0; font-size: 14px;">Se cerrará tu sesión actual.</p>
            <p class="text-xs text-red-600 mt-1" style="color: #dc2626; margin-top: 4px; font-size: 12px; line-height: 1.4;">Tendrás que volver a ingresar tus credenciales para acceder nuevamente a tu cuenta.</p>
        </div>

        <!-- Botones de Acción -->
        <div class="flex items-center justify-center gap-3 w-full" style="display: flex; gap: 12px; width: 100%;">
            <button type="button" onclick="closeLogoutModal()" class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 bg-white text-gray-700 font-semibold text-sm hover:bg-gray-100 transition-colors cursor-pointer" style="flex: 1; min-width: 100px; padding: 10px 16px; border: 1px solid #d1d5db; border-radius: 8px; font-weight: 600; font-size: 14px; background: #ffffff; color: #374151;">
                Cancelar
            </button>
            <form action="{{ route('logout') }}" method="POST" class="flex-1 m-0" style="flex: 1; margin: 0; min-width: 100px;">
                @csrf
                <button type="submit" class="w-full px-4 py-2.5 rounded-lg bg-red-600 hover:bg-red-700 active:bg-red-800 text-white font-semibold text-sm shadow-md hover:shadow transition-all cursor-pointer flex items-center justify-center gap-1.5 whitespace-nowrap" style="width: 100%; padding: 10px 16px; border-radius: 8px; font-weight: 600; font-size: 14px; background-color: #dc2626; color: #ffffff; border: none; display: flex; align-items: center; justify-content: center; gap: 6px; white-space: nowrap;">
                    <span class="material-symbols-outlined text-base" style="font-size: 18px;">logout</span>
                    <span>Sí, salir</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
    if (typeof window.openLogoutModal === 'undefined') {
        window.openLogoutModal = function () {
            const modal = document.getElementById('modal-logout');
            const card = document.getElementById('modal-logout-card');
            if (!modal) return;
            modal.classList.remove('opacity-0', 'pointer-events-none');
            modal.classList.add('opacity-100', 'pointer-events-auto');
            if (card) {
                card.classList.remove('scale-95');
                card.classList.add('scale-100');
            }
            document.body.classList.add('overflow-hidden');
        };

        window.closeLogoutModal = function () {
            const modal = document.getElementById('modal-logout');
            const card = document.getElementById('modal-logout-card');
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
            const modal = document.getElementById('modal-logout');
            if (modal) {
                modal.addEventListener('click', function (e) {
                    if (e.target === modal) {
                        window.closeLogoutModal();
                    }
                });
            }
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    window.closeLogoutModal();
                }
            });
        });
    }
</script>
