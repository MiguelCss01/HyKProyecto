<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Código de recuperación de contraseña</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1f2937;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f6f9; padding: 30px 15px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width: 580px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);">
                    <!-- Header -->
                    <tr>
                        <td style="background-color: #00317e; padding: 28px 32px; text-align: center;">
                            <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 700; letter-spacing: -0.5px;">HyK Mayorista</h1>
                            <p style="margin: 6px 0 0 0; color: #b2c5ff; font-size: 13px;">Tu socio estratégico en abastecimiento</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding: 36px 32px;">
                            <h2 style="margin: 0 0 16px 0; color: #111827; font-size: 20px; font-weight: 600;">Recuperación de contraseña</h2>
                            <p style="margin: 0 0 16px 0; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Recibimos una solicitud para restablecer la contraseña de tu cuenta registrada en <strong>HyK Mayorista</strong>.
                            </p>
                            <p style="margin: 0 0 24px 0; color: #4b5563; font-size: 15px; line-height: 1.6;">
                                Para continuar con el proceso, introduce el siguiente código de verificación de 6 dígitos en la pantalla de recuperación:
                            </p>

                            <!-- Code Box -->
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 24px 0;">
                                <tr>
                                    <td align="center" style="background: #f0f4ff; border: 2px dashed #00317e; border-radius: 10px; padding: 20px 16px;">
                                        <div style="font-size: 13px; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; color: #00317e; margin-bottom: 8px;">
                                            Tu código de verificación
                                        </div>
                                        <div style="font-family: 'Courier New', Courier, monospace; font-size: 38px; font-weight: 700; letter-spacing: 10px; color: #00317e;">
                                            {{ $code }}
                                        </div>
                                    </td>
                                </tr>
                            </table>

                            <!-- Expiry Info -->
                            <div style="background-color: #fffbeb; border-left: 4px solid #f59e0b; padding: 12px 16px; border-radius: 4px; margin-bottom: 24px;">
                                <p style="margin: 0; color: #92400e; font-size: 13px; line-height: 1.5;">
                                    ⏱ <strong>Tiempo de validez:</strong> Este código expira en <strong>{{ $expiresInMinutes }} minutos</strong>. Una vez vencido, deberás solicitar uno nuevo.
                                </p>
                            </div>

                            <p style="margin: 0 0 12px 0; color: #6b7280; font-size: 13px; line-height: 1.5;">
                                Si tú no realizaste esta solicitud, puedes ignorar este mensaje de forma segura. Tu contraseña actual no sufrirá ningún cambio.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 20px 32px; text-align: center;">
                            <p style="margin: 0; color: #9ca3af; font-size: 12px;">
                                &copy; {{ date('Y') }} HyK Mayorista. Todos los derechos reservados.
                            </p>
                            <p style="margin: 6px 0 0 0; color: #9ca3af; font-size: 12px;">
                                Este es un correo electrónico automático, por favor no respondas a este mensaje.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
