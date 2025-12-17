<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    public function submit(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'message' => ['required', 'string', 'max:5000'],
            'product_name' => ['nullable', 'string', 'max:255'],
            'product_slug' => ['nullable', 'string', 'max:255'],
            'product_url' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            Mail::to(env('MAIL_CONTACT_RECIPIENT', 'Lhboss06@gmail.com'))
                ->send(new ContactMail($validated));

            Log::info('Contact form submitted', [
                'email' => $validated['email'],
                'product' => $validated['product_name'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Your message has been sent successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Contact form email failed', [
                'error' => $e->getMessage(),
                'email' => $validated['email'],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, there was an error sending your message. Please try again.',
            ], 500);
        }
    }
}
