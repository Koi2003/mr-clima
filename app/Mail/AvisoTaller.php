<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Aviso genérico al taller: nuevo pedido, nueva reserva, etc.
 *
 * Se resuelve con una sola clase y una sola plantilla porque todos estos
 * avisos son lo mismo (un asunto y una tabla de "campo: valor"); tener una
 * clase por evento obligaría a mantener tres plantillas idénticas.
 */
class AvisoTaller extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param string               $titulo Texto grande de la cabecera.
     * @param array<string,string> $filas  Pares etiqueta => valor a mostrar.
     */
    public function __construct(
        public readonly string $asunto,
        public readonly string $titulo,
        public readonly array $filas,
        public readonly ?string $nota = null,
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->asunto);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.aviso-taller');
    }
}
