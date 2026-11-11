<?php

namespace App\Notifications;

use App\Models\Pedido;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EstadoPedidoActualizadoNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Pedido $pedido,
        public string $estadoAnterior,
        public string $nuevoEstado
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $estado = strtoupper($this->nuevoEstado);

        $titulo = match ($estado) {
            'EN_PREPARACION' => "Tu pedido #{$this->pedido->id} está en preparación",
            'LISTO_RETIRO' => "¡Tu pedido #{$this->pedido->id} está listo para retirar en el local!",
            'ENTREGADO' => "Tu pedido #{$this->pedido->id} ha sido entregado",
            'CANCELADO' => "Tu pedido #{$this->pedido->id} ha sido cancelado",
            'PAGADO' => "Pago acreditado para tu pedido #{$this->pedido->id}",
            'PENDIENTE' => "Tu pedido #{$this->pedido->id} se encuentra pendiente de pago",
            default => "Actualización en tu pedido #{$this->pedido->id}",
        };

        $mensaje = match ($estado) {
            'EN_PREPARACION' => 'Estamos preparando tu pedido en nuestro depósito central. Te avisaremos apenas esté listo para retirar.',
            'LISTO_RETIRO' => '¡Buenas noticias! Tu pedido ya está embalado y listo para que pases a retirarlo por nuestra sucursal. Recuerda traer tu número de pedido o DNI/CUIT.',
            'ENTREGADO' => 'El pedido ha sido completado y entregado con éxito. ¡Gracias por confiar en HyK Mayorista!',
            'CANCELADO' => 'El pedido fue cancelado. Si consideras que se trata de un error o requieres asistencia, contáctanos a la brevedad.',
            'PAGADO' => 'Tu pago fue procesado correctamente y ha quedado registrado en el sistema. Iniciaremos la preparación de tus productos.',
            'PENDIENTE' => 'Tu pedido fue registrado y está a la espera de confirmación de pago.',
            default => "El estado de tu pedido fue actualizado a '{$this->nuevoEstado}'.",
        };

        $icono = match ($estado) {
            'EN_PREPARACION' => 'inventory_2',
            'LISTO_RETIRO' => 'storefront',
            'ENTREGADO' => 'task_alt',
            'CANCELADO' => 'cancel',
            'PAGADO' => 'paid',
            default => 'notifications',
        };

        $color = match ($estado) {
            'EN_PREPARACION' => 'amber',
            'LISTO_RETIRO' => 'emerald',
            'ENTREGADO' => 'blue',
            'CANCELADO' => 'red',
            'PAGADO' => 'green',
            default => 'slate',
        };

        return [
            'pedido_id' => $this->pedido->id,
            'estado_anterior' => $this->estadoAnterior,
            'estado_nuevo' => $this->nuevoEstado,
            'titulo' => $titulo,
            'mensaje' => $mensaje,
            'icono' => $icono,
            'color' => $color,
            'url' => route('pedidos.show', $this->pedido->id),
        ];
    }
}
