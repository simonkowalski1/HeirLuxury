<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public array $data
    ) {}

    public function envelope(): Envelope
    {
        $subject = $this->data['product_name']
            ? 'Product Inquiry: '.$this->data['product_name']
            : 'Contact Form Submission';

        return new Envelope(
            from: new Address($this->data['email'], $this->data['first_name'].' '.$this->data['last_name']),
            replyTo: [new Address($this->data['email'], $this->data['first_name'].' '.$this->data['last_name'])],
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'firstName' => $this->data['first_name'],
                'lastName' => $this->data['last_name'],
                'email' => $this->data['email'],
                'phone' => $this->data['phone'] ?? null,
                'messageContent' => $this->data['message'],
                'productName' => $this->data['product_name'] ?? null,
                'productUrl' => $this->data['product_url'] ?? null,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
