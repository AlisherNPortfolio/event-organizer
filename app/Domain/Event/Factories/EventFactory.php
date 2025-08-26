<?php

namespace App\Domain\Event\Factories;

use App\Domain\Event\Entities\Event;
use App\Domain\Event\ValueObjects\EventId;
use App\Domain\Event\ValueObjects\EventTitle;
use App\Domain\Event\ValueObjects\EventDescription;
use App\Domain\Event\ValueObjects\ParticipantLimit;
use App\Domain\Event\ValueObjects\EventPrice;
use App\Domain\Auth\ValueObjects\UserId;
use DateTime;

class EventFactory
{
    public static function create(
        UserId $organizerId,
        EventTitle $title,
        EventDescription $description,
        string $address,
        int $minParticipants,
        int $maxParticipants,
        float $price = 0.0,
        DateTime $startTime,
        DateTime $endTime,
        string $currency = 'UZS',
        string $image
    ): Event {
        return Event::create(
            EventId::generate(),
            $organizerId,
            $title,
            $description,
            $address,
            new ParticipantLimit($minParticipants, $maxParticipants),
            $price > 0 ? new EventPrice($price, $currency) : EventPrice::free(),
            $startTime,
            $endTime,
            $image
        );
    }
}
