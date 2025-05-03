<?php

namespace App\Entity;

use App\Repository\StopTimeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StopTimeRepository::class)]
class StopTime extends ParentEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    protected ?\DateTime $arrival_time = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    protected ?\DateTime $departure_time = null;

    #[ORM\Column]
    protected ?string $stop_sequence = null;

    #[ORM\Column]
    protected ?string $pickup_type = null;

    #[ORM\Column]
    protected ?string $drop_off_type = null;

    #[ORM\Column(length: 255)]
    private ?string $trip_id = null;

    #[ORM\Column(length: 255)]
    private ?string $stop_id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArrivalTime(): ?\DateTime
    {
        return $this->arrival_time;
    }

    public function setArrivalTime(\DateTime $arrival_time): static
    {
        $this->arrival_time = $arrival_time;

        return $this;
    }

    public function getDepartureTime(): ?\DateTime
    {
        return $this->departure_time;
    }

    public function setDepartureTime(\DateTime $departure_time): static
    {
        $this->departure_time = $departure_time;

        return $this;
    }

    public function getStopSequence(): ?string
    {
        return $this->stop_sequence;
    }

    public function setStopSequence(string $stop_sequence): static
    {
        $this->stop_sequence = $stop_sequence;

        return $this;
    }

    public function getPickupType(): ?string
    {
        return $this->pickup_type;
    }

    public function setPickupType(string $pickup_type): static
    {
        $this->pickup_type = $pickup_type;

        return $this;
    }

    public function getDropOffType(): ?string
    {
        return $this->drop_off_type;
    }

    public function setDropOffType(string $drop_off_type): static
    {
        $this->drop_off_type = $drop_off_type;

        return $this;
    }

    public function getTripId(): ?string
    {
        return $this->trip_id;
    }

    public function setTripId(string $trip_id): static
    {
        $this->trip_id = $trip_id;

        return $this;
    }

    public function getStopId(): ?string
    {
        return $this->stop_id;
    }

    public function setStopId(string $stop_id): static
    {
        $this->stop_id = $stop_id;

        return $this;
    }
}
