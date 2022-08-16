<?php

declare(strict_types=1);

namespace App\Entity\Interfaces;

// use App\Model\ValueObject\UpdateContext;

interface IdentifiableInterface
{
    /**
     * Get ID of entity.
     */
    public function getId(): ?int;

//    public function getUpdateContext(): ?UpdateContext;
//
//    public function setUpdateContext(?UpdateContext $updateContext): void;
}
