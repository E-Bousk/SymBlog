<?php

namespace App\Notifications;

use Swift_Message;
use App\Entity\Users;
use Twig\Environment;

class ActivationCompteNotification
{
    /**
     * Propriété contenant le module d'envoi de mail
     * 
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * Propriété contenant l'environnement Twig
     * 
     * @var Environment
     */
    private $renderer;

    public function __construct(\Swift_Mailer $mailer, Environment $renderer)
    {
        $this->mailer = $mailer;
        $this->renderer = $renderer;
    }

    /**
     * Méthode de notification (envoi de mail)
     * 
     * @return void 
     */
    public function notify(Users $user)
    {
       // Construit le mail
       $message = (new Swift_Message('Blog - Activation de compte'))
            ->setFrom('no-reply@blog.fr')
            ->setTo($user->getEmail())
            ->setBody(
                $this->renderer->render(
                    'emails/activation.html.twig',
                    ['token' => $user->getActivationToken()]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}
