<?php

namespace App\DataFixtures;

use App\Entity\Event;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class EventFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $event = new Event;
            $event->setName($faker->words(3, true));
            $event->setDate($faker->dateTimeBetween('+1 days', '+6 months'));
            $event->setLocation($faker->streetAddress);
            $event->setAvailableSpots($faker->numberBetween(5, 50));
            $event->setCreatedAt(new DateTimeImmutable);
            $event->setUpdatedAt(new DateTime);
            $manager->persist($event);

            // Optionally keep a reference for other fixtures:
            $this->addReference('event_' . $i, $event);
        }

        $manager->flush();
    }
}
