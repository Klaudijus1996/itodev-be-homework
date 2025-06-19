<?php

namespace App\Doctrine\Id;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Id\AbstractIdGenerator;

class ShortflakeIdGenerator extends AbstractIdGenerator
{
    private const EPOCH = 1672531200000;
    private const SEQUENCE_BITS = 12;
    private const MAX_SEQUENCE = (1 << self::SEQUENCE_BITS) - 1;

    private int $lastTimestamp = -1;
    private int $sequence = 0;

    public function generateId(EntityManagerInterface $em, object|null $entity): mixed
    {
        $ts = $this->currentTime();

        if ($ts === $this->lastTimestamp) {
            $this->sequence = ($this->sequence + 1) & self::MAX_SEQUENCE;
            if ($this->sequence === 0) {
                $ts = $this->waitNextMs($ts);
            }
        } else {
            $this->sequence = 0;
        }

        $this->lastTimestamp = $ts;

        return ($ts << self::SEQUENCE_BITS) | $this->sequence;
    }

    private function currentTime(): int
    {
        return (int)floor(microtime(true) * 1_000) - self::EPOCH;
    }

    private function waitNextMs(int $last): int
    {
        do {
            $ts = (int)floor(microtime(true) * 1_000) - self::EPOCH;
        } while ($ts <= $last);

        return $ts;
    }
}
