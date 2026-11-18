<?php

namespace App\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Mail::extend('brevo', function () {
            return new class extends AbstractTransport
            {
                protected function doSend(SentMessage $message): void
                {
                    $email = MessageConverter::toEmail($message->getOriginalMessage());
                    $apiKey = trim((string) config('services.brevo.api_key', ''));

                    if ($apiKey === '') {
                        Log::warning('BREVO_API_KEY no configurada; correo omitido.');

                        return;
                    }

                    $httpClient = Http::withHeaders([
                        'api-key' => $apiKey,
                        'accept' => 'application/json',
                        'content-type' => 'application/json',
                    ])->timeout(12);

                    if (app()->environment('local', 'testing')) {
                        $httpClient = $httpClient->withoutVerifying();
                    }

                    $from = $email->getFrom()[0] ?? null;
                    $senderEmail = $from ? $from->getAddress() : (string) config('mail.from.address');
                    $senderName = ($from && $from->getName()) ? $from->getName() : (string) config('mail.from.name', 'HyK Mayorista');

                    // Si el correo remitente es el genérico por defecto, obtener automáticamente el remitente verificado de Brevo
                    if ($senderEmail === '' || $senderEmail === 'hello@example.com') {
                        $sendersResp = $httpClient->get('https://api.brevo.com/v3/senders');
                        $activeSender = collect($sendersResp->json('senders', []))->firstWhere('active', true)
                            ?? collect($sendersResp->json('senders', []))->first();

                        if (! empty($activeSender['email'])) {
                            $senderEmail = (string) $activeSender['email'];
                        } else {
                            $accountResp = $httpClient->get('https://api.brevo.com/v3/account');
                            if (! empty($accountResp->json('email'))) {
                                $senderEmail = (string) $accountResp->json('email');
                            }
                        }
                    }

                    $to = [];
                    foreach ($email->getTo() as $addr) {
                        $item = ['email' => $addr->getAddress()];
                        if ($addr->getName()) {
                            $item['name'] = $addr->getName();
                        }
                        $to[] = $item;
                    }

                    $payload = [
                        'sender' => [
                            'email' => $senderEmail,
                            'name' => $senderName,
                        ],
                        'to' => $to,
                        'subject' => (string) $email->getSubject(),
                        'replyTo' => [
                            'email' => $senderEmail,
                            'name' => $senderName,
                        ],
                    ];

                    if ($email->getHtmlBody()) {
                        $payload['htmlContent'] = (string) $email->getHtmlBody();
                    }
                    if ($email->getTextBody()) {
                        $payload['textContent'] = (string) $email->getTextBody();
                    }

                    $response = $httpClient->post('https://api.brevo.com/v3/smtp/email', $payload);

                    if ($response->failed()) {
                        Log::error('Error al enviar correo mediante Brevo API', [
                            'status' => $response->status(),
                            'body' => $response->json(),
                        ]);
                    }
                }

                public function __toString(): string
                {
                    return 'brevo';
                }
            };
        });
    }
}
