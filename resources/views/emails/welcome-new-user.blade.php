<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 560px; margin: 40px auto; background: #fff; border-radius: 12px; overflow: hidden; }
        .header { background: #2E7D32; padding: 32px 24px; text-align: center; }
        .header h1 { color: #fff; margin: 0; font-size: 22px; font-weight: 600; }
        .body { padding: 32px 24px; }
        .body p { color: #333; font-size: 15px; line-height: 1.6; margin: 0 0 16px; }
        .credentials { background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 8px; padding: 20px; margin: 20px 0; }
        .credentials .label { font-size: 13px; color: #666; margin-bottom: 4px; }
        .credentials .value { font-size: 18px; font-weight: 600; color: #166534; margin-bottom: 16px; }
        .footer { text-align: center; padding: 24px; color: #999; font-size: 12px; border-top: 1px solid #eee; }
        .btn { display: inline-block; background: #2E7D32; color: #fff; text-decoration: none; padding: 12px 32px; border-radius: 8px; font-weight: 600; font-size: 15px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>AgroGreen</h1>
        </div>
        <div class="body">
            <p>Hola <strong>{{ $user->name }}</strong>,</p>
            <p>Se ha creado una cuenta para ti en la plataforma AgroGreen. Estas son tus credenciales de acceso:</p>

            <div class="credentials">
                <div class="label">Email</div>
                <div class="value">{{ $user->email }}</div>
                <div class="label">Contraseña temporal</div>
                <div class="value">{{ $password }}</div>
            </div>

            <p style="text-align: center; margin-top: 24px;">
                <a href="{{ config('app.url') }}" class="btn">Ingresar a AgroGreen</a>
            </p>

            <p style="font-size: 13px; color: #999; margin-top: 24px;">
                Por seguridad, cambia tu contraseña en tu primer inicio de sesión.
                Si no solicitaste esta cuenta, ignora este mensaje.
            </p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} AgroGreen. Todos los derechos reservados.
        </div>
    </div>
</body>
</html>
