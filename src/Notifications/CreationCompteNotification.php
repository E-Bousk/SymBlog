<?php

namespace App\Notifications;

use Swift_Message;
use Twig\Environment;

class CreationCompteNotification
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
    public function notify()
    {
       // Construit le mail
       $message = (new Swift_Message('Blog - Nouvelle inscription'))
            ->setFrom('no-reply@blog.fr')
            ->setTo('contact@blog.fr')
            ->setBody($this->renderer->render('emails/notification-ajout-compte.html.twig'),'text/html')
        ;

        $this->mailer->send($message);
    }
}
