<?php

namespace App\Entity;

use App\Repository\CalendarDatesRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CalendarDatesRepository::class)]
class CalendarDates extends ParentEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $service_id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    protected ?\DateTime $date = null;

    #[ORM\Column(nullable: true)]
    protected ?string $exception_type = null;

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

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getExceptionType(): ?string
    {
        return $this->exception_type;
    }

    public function setExceptionType(?string $exception_type): static
    {
        $this->exception_type = $exception_type;

        return $this;
    }
}
