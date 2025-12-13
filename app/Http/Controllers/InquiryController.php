<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class InquiryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'to' => ['nullable','email'],
            'product' => ['nullable','array'],
            'product.name' => ['nullable','string','max:255'],
            'product.slug' => ['nullable','string','max:255'],
            'product.url'  => ['nullable','url'],
            'form' => ['required','array'],
            'form.first_name' => ['required','string','max:120'],
            'form.last_name'  => ['required','string','max:120'],
            'form.email'      => ['required','email','max:255'],
            'form.phone'      => ['nullable','string','max:60'],
            'form.message'    => ['required','string','max:5000'],
        ]);

        // Send email (or log)
        $to = $data['to'] ?: config('mail.from.address');
        if ($to) {
            Mail::send('mail.inquiry', ['payload' => $data], function ($m) use ($to, $data) {
                $m->to($to)->subject('Product inquiry: '.($data['product']['name'] ?? 'Item'));
            });
        } else {
            \Log::info('Inquiry received', $data);
        }

        return response()->json(['ok' => true]);
    }
}
