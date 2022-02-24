<?php


namespace App\Service\Notification;

use Doctrine\ORM\Mapping as ORM;
use DateTime;


/**
 * Interface NotificationManagerBaseInterface
 * @package App\Service\Notification
 */
interface NotificationManagerBaseInterface
{
    public function updatedTimestamps(): void;

    /**
     * @return DateTime|null
     */
    public function getCreatedAt();

    /**
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(DateTime $createdAt): void;

    /**
     * @return DateTime|null
     */
    public function getUpdatedAt();

    /**
     * @param DateTime $updatedAt
     * @return $this
     */
    public function setUpdatedAt(DateTime $updatedAt): void;

}