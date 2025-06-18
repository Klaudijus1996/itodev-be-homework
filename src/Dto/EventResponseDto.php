<?php

namespace App\Dto;

use App\Entity\Event;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

class EventResponseDto
{
    #[Groups(['event:index'])]
    public int $id;

    #[Groups(['event:index'])]
    public string $name;

    #[Groups(['event:index'])]
    public \DateTimeInterface $date;

    #[Groups(['event:index'])]
    public string $location;

    #[SerializedName('available_spots')]
    #[Groups(['event:index'])]
    public int $availableSpots;

    #[SerializedName('created_at')]
    #[Groups(['event:index'])]
    public \DateTimeImmutable $createdAt;

    public function __construct(Event $event)
    {
        $this->id = $event->getId();
        $this->name = $event->getName();
        $this->date = $event->getDate();
        $this->location = $event->getLocation();
        $this->availableSpots = $event->getAvailableSpots();
        $this->createdAt = $event->getCreatedAt();
    }

    /**
     * Map an iterable of Event entities to an array of DTOs
     *
     * @param iterable<Event> $events
     * @return EventResponseDto[]
     */
    public static function fromCollection(iterable $events): array
    {
        return array_map(fn(Event $e) => new self($e), (array)$events);
    }
}
