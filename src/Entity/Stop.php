<?php

namespace App\Entity;

use App\Repository\StopRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StopRepository::class)]
class Stop extends ParentEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    protected ?string $stop_id = null;

    #[ORM\Column(type: Types::TEXT)]
    protected ?string $stop_name = null;

    #[ORM\Column(length: 255)]
    protected ?string $stop_lat = null;

    #[ORM\Column(length: 255)]
    protected ?string $stop_lon = null;

    #[ORM\Column]
    protected ?string $location_type = null;

    #[ORM\Column(length: 255)]
    protected ?string $parent_station = null;

    #[ORM\Column]
    protected ?string $wheelchair_boarding = null;

    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStopName(): ?string
    {
        return $this->stop_name;
    }

    public function setStopName(string $stop_name): static
    {
        $this->stop_name = $stop_name;

        return $this;
    }

    public function getStopLat(): ?string
    {
        return $this->stop_lat;
    }

    public function setStopLat(string $stop_lat): static
    {
        $this->stop_lat = $stop_lat;

        return $this;
    }

    public function getStopLon(): ?string
    {
        return $this->stop_lon;
    }

    public function setStopLon(string $stop_lon): static
    {
        $this->stop_lon = $stop_lon;

        return $this;
    }

    public function getLocationType(): ?string
    {
        return $this->location_type;
    }

    public function setLocationType(string $location_type): static
    {
        $this->location_type = $location_type;

        return $this;
    }

    public function getParentStation(): ?string
    {
        return $this->parent_station;
    }

    public function setParentStation(string $parent_station): static
    {
        $this->parent_station = $parent_station;

        return $this;
    }

    public function isWheelchairBoarding(): ?string
    {
        return $this->wheelchair_boarding;
    }

    public function setWheelchairBoarding(string $wheelchair_boarding): static
    {
        $this->wheelchair_boarding = $wheelchair_boarding;

        return $this;
    }
}
