<?php

namespace App\Entity;

use App\Repository\RoutesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RoutesRepository::class)]
class Routes extends ParentEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $route_id = null;

    #[ORM\Column(nullable: true)]
    protected ?string $agency_id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $route_short_name = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    protected ?string $route_long_name = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $route_desc = null;

    #[ORM\Column(nullable: true)]
    protected ?string $route_type = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $route_color = null;


    public function __construct()
    {
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRouteId(): ?string
    {
        return $this->route_id;
    }

    public function setRouteId(?string $route_id): static
    {
        $this->route_id = $route_id;

        return $this;
    }

    public function getAgencyId(): ?string
    {
        return $this->agency_id;
    }

    public function setAgencyId(?string $agency_id): static
    {
        $this->agency_id = $agency_id;

        return $this;
    }

    public function getRouteShortName(): ?string
    {
        return $this->route_short_name;
    }

    public function setRouteShortName(?string $route_short_name): static
    {
        $this->route_short_name = $route_short_name;

        return $this;
    }

    public function getRouteLongName(): ?string
    {
        return $this->route_long_name;
    }

    public function setRouteLongName(?string $route_long_name): static
    {
        $this->route_long_name = $route_long_name;

        return $this;
    }

    public function getRouteDesc(): ?string
    {
        return $this->route_desc;
    }

    public function setRouteDesc(?string $route_desc): static
    {
        $this->route_desc = $route_desc;

        return $this;
    }

    public function getRouteType(): ?string
    {
        return $this->route_type;
    }

    public function setRouteType(?string $route_type): static
    {
        $this->route_type = $route_type;

        return $this;
    }

    public function getRouteColor(): ?string
    {
        return $this->route_color;
    }

    public function setRouteColor(?string $route_color): static
    {
        $this->route_color = $route_color;

        return $this;
    }
}
