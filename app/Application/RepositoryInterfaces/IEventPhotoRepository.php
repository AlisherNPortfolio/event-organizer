<?php

namespace App\Application\RepositoryInterfaces;

use App\Domain\Event\Entities\EventPhoto as DomainEventPhoto;
use App\Domain\Event\Entities\Event as DomainEvent;

interface IEventPhotoRepository
{
    public function savePhoto(DomainEventPhoto $eventPhoto, DomainEvent $event): DomainEventPhoto;
}
