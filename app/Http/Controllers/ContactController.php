<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\ContactFormMail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ]);

        try {
            // Send email to the admin defined in env or a default one
            // Using a hardcoded email for demo or env variable
            $adminEmail = env('MAIL_FROM_ADDRESS', 'admin@sigea.com');
            
            Mail::to($adminEmail)->send(new ContactFormMail($request->all()));

            return redirect()->back()->with('success', 'Mensaje enviado correctamente.');
        } catch (\Exception $e) {
            Log::error('Error sending contact email: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Hubo un error al enviar el mensaje. Inténtalo de nuevo.');
        }
    }
}
