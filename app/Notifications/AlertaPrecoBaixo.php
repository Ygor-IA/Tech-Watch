<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\ComponenteHardware;

class AlertaPrecoBaixo extends Notification
{
    use Queueable;

    public $componente;
    public $precoAtual;

    public function __construct(ComponenteHardware $componente, $precoAtual)
    {
        $this->componente = $componente;
        $this->precoAtual = $precoAtual;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $simbolo = $this->componente->simbolo_moeda;
        
        if ($simbolo === 'US$') {
            $precoFormatado = 'US$ ' . number_format($this->precoAtual, 2, '.', ',');
        } else {
            $precoFormatado = 'R$ ' . number_format($this->precoAtual, 2, ',', '.');
        }

        return (new MailMessage)
                    ->subject('🚨 Alerta de Preço: ' . $this->componente->nome)
                    ->greeting('Olá!')
                    ->line('Temos uma ótima notícia para você.')
                    ->line('O componente **' . $this->componente->nome . '** atingiu o preço que você estava esperando!')
                    ->line('Preço atual: **' . $precoFormatado . '**')
                    ->action('Acessar Loja e Comprar', $this->componente->link)
                    ->line('Corra antes que o estoque acabe!')
                    ->salutation('Equipe Tech-Watch');
    }
}