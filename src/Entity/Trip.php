<?php

namespace App\Entity;

use App\Repository\TripRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TripRepository::class)]
class Trip extends ParentEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $service_id = null;

    #[ORM\Column(length: 255)]
    private ?string $trip_id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $trip_headsign = null;

    #[ORM\Column]
    private ?string $wheelchair_accessible = null;

    #[ORM\Column(length: 255)]
    private ?string $route_id = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceId(): ?string
    {
        return $this->service_id;
    }

    public function setServiceId(string $service_id): static
    {
        $this->service_id = $service_id;

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

    public function getTripHeadsign(): ?string
    {
        return $this->trip_headsign;
    }

    public function setTripHeadsign(string $trip_headsign): static
    {
        $this->trip_headsign = $trip_headsign;

        return $this;
    }

    public function isWheelchairAccessible(): ?string
    {
        return $this->wheelchair_accessible;
    }

    public function setWheelchairAccessible(string $wheelchair_accessible): static
    {
        $this->wheelchair_accessible = $wheelchair_accessible;

        return $this;
    }

    public function getRouteId(): ?string
    {
        return $this->route_id;
    }

    public function setRouteId(string $route_id): static
    {
        $this->route_id = $route_id;

        return $this;
    }
}
