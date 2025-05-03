<?php

namespace App\Entity;

use App\Repository\CalendarRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarRepository::class)]
class Calendar extends ParentEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $service_id = null;

    #[ORM\Column(nullable: true)]
    protected ?string $monday = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    protected ?\DateTime $start_date = null;

    #[ORM\Column(nullable: true)]
    protected ?string $tuesday = null;

    #[ORM\Column(nullable: true)]
    protected ?string $wednesday = null;

    #[ORM\Column(nullable: true)]
    protected ?string $thursday = null;

    #[ORM\Column(nullable: true)]
    protected ?string $friday = null;

    #[ORM\Column(nullable: true)]
    protected ?string $saturday = null;

    #[ORM\Column(nullable: true)]
    protected ?string $sunday = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    protected ?\DateTime $end_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getServiceId(): ?string
    {
        return $this->service_id;
    }

    public function setServiceId(?string $service_id): static
    {
        $this->service_id = $service_id;

        return $this;
    }

    public function isMonday(): ?string
    {
        return $this->monday;
    }

    public function setMonday(?string $monday): static
    {
        $this->monday = $monday;

        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->start_date;
    }

    public function setStartDate(?\DateTime $start_date): static
    {
        $this->start_date = $start_date;

        return $this;
    }

    public function isTuesday(): ?string
    {
        return $this->tuesday;
    }

    public function setTuesday(?string $tuesday): static
    {
        $this->tuesday = $tuesday;

        return $this;
    }

    public function isWednesday(): ?string
    {
        return $this->wednesday;
    }

    public function setWednesday(?string $wednesday): static
    {
        $this->wednesday = $wednesday;

        return $this;
    }

    public function isThursday(): ?string
    {
        return $this->thursday;
    }

    public function setThursday(?string $thursday): static
    {
        $this->thursday = $thursday;

        return $this;
    }

    public function isFriday(): ?string
    {
        return $this->friday;
    }

    public function setFriday(?string $friday): static
    {
        $this->friday = $friday;

        return $this;
    }

    public function isSaturday(): ?string
    {
        return $this->saturday;
    }

    public function setSaturday(?string $saturday): static
    {
        $this->saturday = $saturday;

        return $this;
    }

    public function isSunday(): ?string
    {
        return $this->sunday;
    }

    public function setSunday(?string $sunday): static
    {
        $this->sunday = $sunday;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->end_date;
    }

    public function setEndDate(?\DateTime $end_date): static
    {
        $this->end_date = $end_date;

        return $this;
    }
}
