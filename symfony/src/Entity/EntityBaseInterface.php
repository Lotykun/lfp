<?php


namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * Interface EntityBaseInterface
 * @package App\Entity
 */
interface EntityBaseInterface
{
    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
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