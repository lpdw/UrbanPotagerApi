<?php

namespace UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use UserBundle\Event\UserCreateEvent;
use UserBundle\Service\Mailer;

class RegisterListener implements EventSubscriberInterface
{
    private $mailer;

    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public static function getSubscribedEvents()
    {
        return array(
            UserCreateEvent::NAME => 'register',
        );
    }

    public function register(UserCreateEvent $event)
    {
        $this->mailer->sendRegisterEmailMessage($event->getUser());
    }
}
