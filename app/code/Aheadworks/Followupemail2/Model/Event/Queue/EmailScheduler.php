<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\Followupemail2\Model\Event\Queue;

use Aheadworks\Followupemail2\Api\Data\EmailInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueEmailInterface;
use Aheadworks\Followupemail2\Api\Data\EventQueueEmailInterfaceFactory;
use Aheadworks\Followupemail2\Api\EventQueueRepositoryInterface;
use Aheadworks\Followupemail2\Api\QueueManagementInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class EmailScheduler
 * @package Aheadworks\Followupemail2\Model\Event\Queue
 */
class EmailScheduler
{
    /**
     * @var EventQueueEmailInterfaceFactory
     */
    private $eventQueueEmailFactory;

    /**
     * @var QueueManagementInterface
     */
    private $queueManagement;

    /**
     * @var EventQueueRepositoryInterface
     */
    private $eventQueueRepository;

    /**
     * @param EventQueueEmailInterfaceFactory $eventQueueEmailFactory
     * @param QueueManagementInterface $queueManagement
     * @param EventQueueRepositoryInterface $eventQueueRepository
     */
    public function __construct(
        EventQueueEmailInterfaceFactory $eventQueueEmailFactory,
        QueueManagementInterface $queueManagement,
        EventQueueRepositoryInterface $eventQueueRepository
    ) {
        $this->eventQueueEmailFactory = $eventQueueEmailFactory;
        $this->queueManagement = $queueManagement;
        $this->eventQueueRepository = $eventQueueRepository;
    }

    /**
     * Schedule next email
     *
     * @param EventQueueInterface $eventQueueItem
     * @param EmailInterface $email
     * @return EventQueueInterface
     */
    public function scheduleNextEmail($eventQueueItem, $email)
    {
        /** @var EventQueueEmailInterface $queueEmail */
        $queueEmail = $this->eventQueueEmailFactory->create();
        $queueEmail->setStatus(EventQueueEmailInterface::STATUS_PENDING);
        /** @var EventQueueEmailInterface[] $queueEmails */
        $queueEmails = $eventQueueItem->getEmails();
        $queueEmails[] = $queueEmail;
        $eventQueueItem->setEmails($queueEmails);

        try {
            $eventQueueItem = $this->eventQueueRepository->save($eventQueueItem);

            /** @var EventQueueEmailInterface[] $queueEmails */
            $queueEmails = $eventQueueItem->getEmails();
            /** @var EventQueueEmailInterface $queueEmail */
            $queueEmail = end($queueEmails);

            if (!$this->queueManagement->schedule($eventQueueItem, $email, $queueEmail->getId())) {
                $queueEmail->setStatus(EventQueueEmailInterface::STATUS_FAILED);
                try {
                    $eventQueueItem = $this->eventQueueRepository->save($eventQueueItem);
                } catch (LocalizedException $e) {
                    // do nothing
                }
            }
        } catch (LocalizedException $e) {
            // do nothing
        }

        return $eventQueueItem;
    }

    /**
     * Cancel scheduled email
     *
     * @param EventQueueEmailInterface $email
     * @return $this
     */
    public function cancelScheduledEmail($email)
    {
        $this->queueManagement->cancelByEventQueueEmailId($email->getId());
        return $this;
    }

    /**
     * Send scheduled email
     *
     * @param EventQueueEmailInterface $email
     * @return $this
     */
    public function sendScheduledEmail($email)
    {
        $this->queueManagement->sendByEventQueueEmailId($email->getId());
        return $this;
    }
}
