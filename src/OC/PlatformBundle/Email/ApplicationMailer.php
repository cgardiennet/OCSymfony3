<?php
// src/OC/PlatformBundle/Email/ApplicationMailer.php

namespace OC\PlatformBundle\Email;

use OC\PlatformBundle\Entity\Application;
use Swift_Message;
use Swift_Mailer;

class ApplicationMailer
{
    /**
    * @var \Swift_Mailer
    */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendNewNotification(Application $application)
    {
        $message = new Swift_Message(
            'Nouvelle candidature',
            "Vous avez reçu une nouvelle candidature pour l'annonce suivante : " . $application->getAdvert()->getTitle() . "."
        );

        $message
            ->addTo($application->getAdvert()->getEmail()) // Ici bien sûr il faudrait un attribut "email", j'utilise "author" à la place
            ->addFrom('c.gardiennet@training-orchestra.com')
        ;

        $this->mailer->send($message);
    }
}
