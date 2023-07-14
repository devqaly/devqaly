<?php

namespace App\Mail\Auth;

use App\Models\Auth\RegisterToken;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SignupEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public RegisterToken $registerToken;

    public function __construct(RegisterToken $registerToken)
    {
        $this->registerToken = $registerToken;
    }

    public function build()
    {
        return $this
            ->subject(sprintf('Finish your registration at %s', config('app.name')))
            ->markdown('emails.auth.signup', [
                'finishRegistrationUrl' => sprintf(
                    '%s/%s/%s',
                    config('frontend.FRONTEND_BASE_URL'),
                    'auth/finishRegistration',
                    $this->registerToken->token,
                ),
            ]);
    }
}
