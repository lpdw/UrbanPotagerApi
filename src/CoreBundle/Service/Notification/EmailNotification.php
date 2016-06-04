<?php

namespace CoreBundle\Service\Notification;

class EmailNotification
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send($title, $message, $to)
    {
        $mail = \Swift_Message::newInstance()
                ->setSubject($title)
                ->setTo($to)
                ->setBody($message);

        $this->mailer->send($mail);
    }
}