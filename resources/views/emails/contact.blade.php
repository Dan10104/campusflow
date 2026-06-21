<!DOCTYPE html>
<html>
<head>
    <title>Nuevo Mensaje de Contacto - SIGEA</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e0e0e0; border-radius: 5px;">
        <h2 style="color: #2563EB;">Nuevo Mensaje de Contacto</h2>
        <p>Has recibido un nuevo mensaje desde el formulario de contacto corporativo de SIGEA.</p>
        
        <div style="background-color: #f9fafb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Nombre:</strong> {{ $data['name'] }}</p>
            <p><strong>Email:</strong> {{ $data['email'] }}</p>
        </div>

        <h3>Mensaje:</h3>
        <p style="background-color: #ffffff; padding: 15px; border: 1px solid #e0e0e0; border-radius: 5px;">
            {{ $data['message'] }}
        </p>
        
        <hr style="border: 0; border-top: 1px solid #e0e0e0; margin: 20px 0;">
        <p style="font-size: 12px; color: #6b7280; text-align: center;">
            Este correo fue enviado automáticamente por el sistema SIGEA.
        </p>
    </div>
</body>
</html>
