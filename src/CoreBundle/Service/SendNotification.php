<?php

namespace CoreBundle\Service;

use CoreBundle\Entity\Garden;
use Symfony\Component\Translation\DataCollectorTranslator;
use CoreBundle\Entity\Alert;
use CoreBundle\Service\HistoryNotificationHandler;
use Corebundle\Service\Notification\EmailNotification;

class SendNotification
{
    /**
     * @var HistoryNotificationHandler
     */
    private $historyNotificationHandler;

    /**
     * @var EmailNotification
     */
    private $emailNotification;

    /**
     * @var DataCollectorTranslator
     */
    private $translator;

    public function __construct(HistoryNotificationHandler $historyNotificationHandler, EmailNotification $emailNotification, DataCollectorTranslator $translator)
    {
        $this->historyNotificationHandler = $historyNotificationHandler;
        $this->emailNotification = $emailNotification;
        $this->translator = $translator;
    }

    public function send(Garden $garden, Alert $alert)
    {
        if (!$this->historyNotificationHandler->canSendNotification($garden, $alert)) {
            return;
        }

        $title = $this->createTitle($garden);
        $message = $this->createMessage($alert);
        $email = $alert->getOwner()->getEmail();

        $this->emailNotification->send($title, $message, $email);

        $this->historyNotificationHandler->addHistoryNotification($garden, $alert);
    }

    private function createMessage(Alert $alert)
    {
        return $alert->getMessage();
    }

    private function createTitle(Garden $garden)
    {
        return $this->translator->trans('core.alert.title', ['%garden%' => $garden->getName()]);
    }
}