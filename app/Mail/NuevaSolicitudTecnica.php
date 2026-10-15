<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\SolicitudTecnica;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Aviso que recibe el dueño cada vez que un cliente envía el
 * formulario de servicio técnico. Contiene todos los datos de la
 * solicitud para poder llamar al cliente sin entrar a ningún panel.
 */
class NuevaSolicitudTecnica extends Mailable
{
    use Queueable, SerializesModels;

    /** La solicitud recién creada, accesible desde la vista del email. */
    public function __construct(
        public readonly SolicitudTecnica $solicitud,
    ) {
    }

    /** Sobre del mensaje: asunto con el nº de solicitud para localizarla. */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Nueva solicitud de servicio técnico #{$this->solicitud->id} — {$this->solicitud->cliente_nombre}",
        );
    }

    /** Cuerpo: plantilla Blade en resources/views/emails/. */
    public function content(): Content
    {
        return new Content(
            view: 'emails.nueva-solicitud',
        );
    }
}
