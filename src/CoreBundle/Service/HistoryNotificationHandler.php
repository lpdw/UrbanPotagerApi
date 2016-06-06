<?php

namespace CoreBundle\Service;

use Doctrine\ORM\EntityManager;
use CoreBundle\Entity\HistoryNotification;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Alert;

class HistoryNotificationHandler
{
    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @param Garden $garden
     * @param Alert $alert
     * @return bool
     */
    public function canSendNotification(Garden $garden, Alert $alert)
    {
        /** @var \CoreBundle\Repository\HistoryNotificationRepository $repo */
        $repo = $this->em->getRepository('CoreBundle:HistoryNotification');

        $count = $repo->countNotificationLast24Hours($garden, $alert);

        return 0 == $count;
    }

    public function addHistoryNotification(Garden $garden, Alert $alert)
    {
        $notification = new HistoryNotification();
        $notification->setGarden($garden)
                    ->setAlert($alert)
                    ->setSendAt(new \DateTime());

        $this->em->persist($notification);
        $this->em->flush();
    }
}
