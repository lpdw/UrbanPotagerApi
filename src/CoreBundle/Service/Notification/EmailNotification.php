<?php

namespace CoreBundle\Service\Notification;

class EmailNotification
{
    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    /**
     * @var \Twig_Environment
     */
    protected $twig;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function send($subject, $content, $to)
    {
        $template = $this->twig->loadTemplate('CoreBundle:Alert:email.txt.twig');
        $htmlBody = $template->renderBlock('body_html', ['message' => $content]);

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setTo($to);

        if (!empty($htmlBody)) {
            $message->setBody($htmlBody, 'text/html')
                    ->addPart($content, 'text/plain');
        } else {
            $message->setBody($content);
        }

        $this->mailer->send($message);
    }
}