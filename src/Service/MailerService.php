<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

class MailerService
{

    private MailerInterface $mailer;

    private UrlGeneratorInterface $urlGenerator;

    private Environment $twig;


    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, Environment $twig)
    {
        $this->mailer       = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->twig         = $twig;

    }//end __construct()


    public function sendActivationEmail(string $email, string $token): void
    {
        // Génération de l'URL d'activation.
        $activationUrl = $this->urlGenerator->generate('app_activate_account', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

        // Rendu du template Twig.
        $htmlContent = $this->twig->render(
            'emails/activation.html.twig',
            [
                'activationUrl' => $activationUrl,
            ]
        );

        $emailMessage = (new Email())
            ->from('no-reply@votre-domaine.com')
            ->to($email)
            ->subject('Activez votre compte')
            ->html($htmlContent);

        $this->mailer->send($emailMessage);

    }//end sendActivationEmail()


}//end class
