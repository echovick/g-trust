<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Mailtrap\MailtrapClient;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\MessageConverter;

class MailtrapServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Mail::extend('mailtrap', function (array $config = []) {
            return new MailtrapTransport(
                config('services.mailtrap.api_key')
            );
        });
    }
}

class MailtrapTransport extends AbstractTransport
{
    protected $client;

    public function __construct(string $apiKey)
    {
        parent::__construct();

        $this->client = MailtrapClient::initSendingEmails(
            apiKey: $apiKey
        );
    }

    protected function doSend(SentMessage $message): void
    {
        $email = MessageConverter::toEmail($message->getOriginalMessage());

        $this->client->send($email);
    }

    public function __toString(): string
    {
        return 'mailtrap';
    }
}
