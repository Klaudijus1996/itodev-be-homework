<?php

namespace App\Dto;

use App\Entity\EventRegistration;

class EventRegistrationResponseDto
{
    public function __construct(
        public int $id,
        public string $name,
        public string $email,
        public \DateTimeImmutable $created_at,
        public EventResponseDto $event
    ) {
    }

    public static function make(EventRegistration $object): self
    {
        return new self(
            $object->getId(),
            $object->getName(),
            $object->getEmail(),
            $object->getCreatedAt(),
            new EventResponseDto($object->getEvent())
        );
    }

    /**
     * @param iterable<EventRegistration> $registrations
     * @return EventRegistrationResponseDto[]
     */
    public static function fromCollection(iterable $registrations): array
    {
        return array_map(fn(EventRegistration $r) => new self(
            $r->getId(),
            $r->getName(),
            $r->getEmail(),
            $r->getCreatedAt(),
            event: new EventResponseDto($r->getEvent())
        ), (array)$registrations);
    }
}
