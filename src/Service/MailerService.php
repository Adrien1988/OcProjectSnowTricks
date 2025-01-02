<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

/**
 * Service pour gérer l'envoi des emails.
 */
class MailerService
{

    /**
     * Interface pour l'envoi d'emails.
     *
     * @var MailerInterface
     */
    private MailerInterface $mailer;

    /**
     * Générateur d'URL.
     *
     * @var UrlGeneratorInterface
     */
    private UrlGeneratorInterface $urlGenerator;

    /**
     * Moteur de rendu Twig.
     *
     * @var Environment
     */
    private Environment $twig;


    /**
     * Constructeur du service MailerService.
     *
     * @param MailerInterface       $mailer       Interface pour l'envoi d'emails.
     * @param UrlGeneratorInterface $urlGenerator Générateur d'URL pour créer les liens.
     * @param Environment           $twig         Moteur de rendu Twig pour les templates.
     */
    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $urlGenerator, Environment $twig)
    {
        $this->mailer       = $mailer;
        $this->urlGenerator = $urlGenerator;
        $this->twig         = $twig;

    }//end __construct()


    /**
     * Envoie un email d'activation à un utilisateur.
     *
     * @param string $email Adresse email du destinataire.
     * @param string $token Jeton d'activation unique.
     *
     * @return void
     */
    public function sendActivationEmail(string $email, string $token): void
    {
        // Génération de l'URL d'activation.
        $activationUrl = $this->urlGenerator->generate(
            'app_activate_account',
            ['token' => $token],
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        // Rendu du template Twig.
        $htmlContent = $this->twig->render(
            'emails/activation.html.twig',
            [
                'activationUrl' => $activationUrl,
            ]
        );

        // Création du message email.
        $emailMessage = (new Email())
            ->from('no-reply@votre-domaine.com')
            ->to($email)
            ->subject('Activez votre compte')
            ->html($htmlContent);

        // Envoi de l'email.
        $this->mailer->send($emailMessage);

    }//end sendActivationEmail()


}//end class
