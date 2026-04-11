<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Mención - Gestor de Tareas</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #0f172a;">
    <table width="100%" cellpadding="0" cellspacing="0"
        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); padding: 40px 20px;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="max-width: 600px; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">

                    <!-- Header -->
                    <tr>
                        <td
                            style="background: linear-gradient(135deg, #3b82f6 0%, #3b82f6 50%, #3b82f6 100%); padding: 40px 30px; text-align: center;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td align="center">
                                        <div
                                            style="width: 70px; height: 70px; background: rgba(255,255,255,0.2); border-radius: 50%; line-height: 70px; margin: 0 auto 15px;">
                                            <span style="font-size: 32px;">💬</span>
                                        </div>
                                        <h1
                                            style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">
                                            Nueva Mención
                                        </h1>
                                        <p style="color: rgba(255,255,255,0.85); margin: 10px 0 0; font-size: 16px;">
                                            Alguien te ha etiquetado en un comentario
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 35px;">

                            <!-- Sender Info -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                <tr>
                                    <td width="60" valign="top">
                                        <div
                                            style="width: 50px; height: 50px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border-radius: 50%; text-align: center; line-height: 50px; color: white; font-weight: bold; font-size: 20px;">
                                            {{ strtoupper(substr($sender->name, 0, 1)) }}
                                        </div>
                                    </td>
                                    <td style="padding-left: 15px;" valign="middle">
                                        <p style="margin: 0; font-weight: 600; color: #1e293b; font-size: 17px;">
                                            {{ $sender->name }}
                                        </p>
                                        <p style="margin: 4px 0 0; color: #64748b; font-size: 14px;">
                                            {{ $sender->department ?? $sender->position ?? 'Usuario del sistema' }}
                                        </p>
                                    </td>
                                </tr>
                            </table>

                            <!-- Task Card -->
                            <div
                                style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin-bottom: 25px;">
                                <p
                                    style="margin: 0 0 8px; color: #64748b; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; font-weight: 600;">
                                    📋 Tarea
                                </p>
                                <p style="margin: 0; color: #1e40af; font-size: 18px; font-weight: 600;">
                                    {{ $task->title }}
                                </p>
                            </div>

                            <!-- Message -->
                            <div style="background: #1e293b; border-radius: 12px; padding: 25px;">
                                <p style="margin: 0; color: #f1f5f9; font-size: 16px; line-height: 1.7;">
                                    {{ $comment->content }}
                                </p>
                                <p style="margin: 15px 0 0; color: #64748b; font-size: 12px; text-align: right;">
                                    {{ $comment->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top: 30px;">
                                <tr>
                                    <td align="center">
                                        <a href="{{ url('/tasks/' . $task->id . '?open_chat=1') }}"
                                            style="display: inline-block; background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); color: #ffffff; padding: 16px 40px; text-decoration: none; border-radius: 10px; font-weight: 600; font-size: 16px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4);">
                                            Ver Conversación →
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background: #f8fafc; padding: 25px 35px; border-top: 1px solid #e2e8f0;">
                            <table width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td>
                                        <p style="margin: 0; color: #64748b; font-size: 13px;">
                                            <strong>Gestor de Tareas</strong><br>
                                            Alcaldía de La Paz Este
                                        </p>
                                    </td>
                                    <td align="right">
                                        <p style="margin: 0; color: #94a3b8; font-size: 12px;">
                                            Para desactivar estas notificaciones,<br>
                                            ve a tu perfil y ajusta las preferencias.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <!-- Sub-footer -->
                <p style="color: #475569; font-size: 12px; margin-top: 25px; text-align: center;">
                    © {{ date('Y') }} Gestor de Tareas • Todos los derechos reservados
                </p>
            </td>
        </tr>
    </table>
</body>

</html>