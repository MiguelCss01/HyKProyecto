<?php

namespace App\Http\Controllers;

use App\Services\CartService;
use App\Services\ChatbotService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function __construct(
        protected ChatbotService $chatbotService,
        protected CartService $cartService
    ) {}

    /**
     * Obtiene el historial de mensajes de la sesión actual y las sugerencias iniciales.
     */
    public function history(Request $request): JsonResponse
    {
        $user = $request->user();
        $isMayorista = $user?->tipo_cliente === 'mayorista';
        $messages = session()->get('chatbot_messages', []);

        if (empty($messages)) {
            $saludo = $user
                ? "¡Hola {$user->name}! Soy el Asistente Virtual de HyK Mayorista. Tenés activa la lista de precios ".($isMayorista ? 'Mayorista' : 'Minorista').'. ¿En qué te puedo ayudar hoy?'
                : '¡Hola! Soy el Asistente Virtual de HyK Mayorista. Puedo ayudarte a buscar productos, consultar precios por unidad, pack o bulto, o responder dudas sobre tu compra.';

            $messages = [
                [
                    'role' => 'assistant',
                    'text' => $saludo,
                    'products' => [],
                    'orders' => [],
                    'time' => now()->format('H:i'),
                ],
            ];
        }

        return response()->json([
            'messages' => $messages,
            'suggestions' => $this->chatbotService->obtenerSugerencias('saludo', $user),
            'is_authenticated' => $user !== null,
            'tipo_cliente' => $isMayorista ? 'mayorista' : 'minorista',
        ]);
    }

    /**
     * Procesa un nuevo mensaje del usuario en el asistente virtual.
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ], [
            'message.required' => 'Por favor escribe una consulta.',
            'message.max' => 'El mensaje no puede superar los 500 caracteres.',
        ]);

        $user = $request->user();
        $userText = trim($validated['message']);
        $historial = session()->get('chatbot_messages', []);

        $resultado = $this->chatbotService->responder($userText, $user, $historial);

        $historial[] = [
            'role' => 'user',
            'text' => $userText,
            'products' => [],
            'orders' => [],
            'time' => now()->format('H:i'),
        ];

        $historial[] = [
            'role' => 'assistant',
            'text' => $resultado['reply'],
            'products' => $resultado['products'],
            'orders' => $resultado['orders'],
            'time' => now()->format('H:i'),
        ];

        // Conservar los últimos 14 mensajes en sesión para mantener ligereza
        if (count($historial) > 14) {
            $historial = array_slice($historial, -14);
        }

        session()->put('chatbot_messages', $historial);

        return response()->json([
            'reply' => $resultado['reply'],
            'products' => $resultado['products'],
            'orders' => $resultado['orders'],
            'suggestions' => $resultado['suggestions'],
            'source' => $resultado['source'],
            'time' => now()->format('H:i'),
        ]);
    }

    /**
     * Limpia el historial de conversación de la sesión actual.
     */
    public function clearSession(): JsonResponse
    {
        session()->forget('chatbot_messages');

        return response()->json([
            'success' => true,
            'message' => 'Conversación reiniciada.',
        ]);
    }

    /**
     * Agrega rápidamente una presentación sugerida por el bot al carrito de compras.
     */
    public function quickAddToCart(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'presentacion_id' => 'required|integer|exists:presentacion_productos,id',
            'cantidad' => 'required|integer|min:1|max:999',
        ], [
            'presentacion_id.required' => 'Selecciona una presentación válida.',
            'presentacion_id.exists' => 'La presentación seleccionada no existe.',
            'cantidad.min' => 'La cantidad mínima es 1.',
        ]);

        $resultado = $this->cartService->agregar(
            (int) $validated['presentacion_id'],
            (int) $validated['cantidad']
        );

        return response()->json([
            'success' => $resultado['success'],
            'message' => $resultado['message'],
            'cart_count' => $this->cartService->conteoTotal(),
        ], $resultado['success'] ? 200 : 422);
    }
}
