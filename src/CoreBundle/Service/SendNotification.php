<?php

namespace CoreBundle\Service;

use CoreBundle\Entity\Alert;

class SendNotification
{
    private $emailNotification;

    private $translator;

    public function __construct(\Corebundle\Service\Notification\EmailNotification $emailNotification, \Symfony\Component\Translation\DataCollectorTranslator $translator)
    {
        $this->emailNotification = $emailNotification;
        $this->translator = $translator;
    }

    public function send(Alert $alert)
    {
        $title = $this->createTitle();
        $message = $this->createMessage($alert);
        $email = $alert->getOwner()->getEmail();

        $this->emailNotification->send($title, $message, $email);
    }

    private function createMessage(Alert $alert)
    {
        return $alert->getMessage();
    }

    private function createTitle()
    {
        return $this->translator->trans('core.alert.title');
    }
}