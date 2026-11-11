<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    /**
     * Muestra la vista dedicada de notificaciones del cliente.
     */
    public function index(): View
    {
        $user = Auth::user();
        $notificaciones = $user->notifications()->latest()->paginate(10);
        $notificacionesNoLeidas = $user->unreadNotifications()->count();

        return view('notificaciones.index', [
            'user' => $user,
            'notificaciones' => $notificaciones,
            'notificacionesNoLeidas' => $notificacionesNoLeidas,
        ]);
    }

    /**
     * Marca una notificación específica como leída.
     */
    public function marcarLeida(Request $request, string $id): RedirectResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->markAsRead();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notificación marcada como leída.');
    }

    /**
     * Marca todas las notificaciones pendientes del usuario como leídas.
     */
    public function marcarTodasLeidas(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Todas las notificaciones fueron marcadas como leídas.');
    }

    /**
     * Elimina una notificación del historial del usuario.
     */
    public function destroy(Request $request, string $id): RedirectResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();

        if ($notification) {
            $notification->delete();
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Notificación eliminada de tu historial.');
    }
}
