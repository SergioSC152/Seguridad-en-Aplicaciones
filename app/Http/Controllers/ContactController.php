<?php

namespace App\Http\Controllers;

use App\Mail\ContactConfirmation;
use App\Mail\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Muestra la vista del formulario de contacto.
     */
    public function show()
    {
        return view('contact');
    }

    /**
     * Valida y procesa el envío de correo desde el formulario (Pasos 6.6 y 6.7).
     */
    public function send(Request $request)
    {
        // Validación del lado del servidor (Paso 6.6)
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        try {
            // Envío del correo principal al administrador (Paso 6.7)
            Mail::to('administracion@empresa.com')
                ->send(new ContactMessage(
                    $validated['name'],
                    $validated['email'],
                    $validated['message']
                ));

            // Envío del correo de confirmación al usuario (Paso 6.9)
            Mail::to($validated['email'])
                ->send(new ContactConfirmation(
                    $validated['name']
                ));

            return redirect()
                ->back()
                ->with('mail_success', '¡Mensaje enviado con éxito! Se ha notificado al administrador y se envió una confirmación a tu correo.');
        } catch (\Throwable $e) {
            Log::error('Error en envío de correo: ' . $e->getMessage());

            return redirect()
                ->back()
                ->withInput()
                ->with('mail_error', 'Ocurrió un inconveniente al enviar el correo. Por favor verifique las credenciales SMTP en .env.');
        }
    }
}
