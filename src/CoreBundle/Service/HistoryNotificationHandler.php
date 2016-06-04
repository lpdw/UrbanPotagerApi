<?php

namespace CoreBundle\Service;

use CoreBundle\Entity\HistoryNotification;
use CoreBundle\Entity\Garden;
use CoreBundle\Entity\Alert;

class HistoryNotificationHandler
{
    /**
     * @var \CoreBundle\Repository\HistoryNotificationRepository
     */
    private $repo;

    public function __construct(\CoreBundle\Repository\HistoryNotificationRepository $repo)
    {
        $this->repo = $repo;
    }

    /**
     * @param Garden $garden
     * @param Alert $alert
     * @return bool
     */
    public function canSendNotification(Garden $garden, Alert $alert)
    {
        $count = $this->repo->countNotificationLast24Hours($garden, $alert);

        return 0 == $count;
    }
}
