<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank(message: "Reservation date is required.")]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: "time")]
    #[Assert\NotBlank(message: "Start time is required.")]
    private ?\DateTimeInterface $start_time = null;

    #[ORM\Column(type: "time")]
    #[Assert\NotBlank(message: "End time is required.")]
    #[Assert\GreaterThan(propertyPath: "startTime", message: "End time must be after start time.")]
    private ?\DateTimeInterface $end_time = null;

    #[ORM\Column(length: 255)]
    private ?string $reserved_by = null;

    #[ORM\ManyToOne(targetEntity: ConferenceRoom::class, inversedBy: "reservations")]
    #[ORM\JoinColumn(nullable: false)]
    private ?ConferenceRoom $conferenceRoom = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getStartTime(): ?\DateTimeInterface
    {
        return $this->start_time;
    }

    public function setStartTime(\DateTimeInterface $start_time): static
    {
        $this->start_time = $start_time;

        return $this;
    }

    public function getEndTime(): ?\DateTimeInterface
    {
        return $this->end_time;
    }

    public function setEndTime(\DateTimeInterface $end_time): static
    {
        $this->end_time = $end_time;

        return $this;
    }

    public function getReservedBy(): ?string
    {
        return $this->reserved_by;
    }

    public function setReservedBy(string $reserved_by): static
    {
        $this->reserved_by = $reserved_by;

        return $this;
    }

    public function getConferenceRoom(): ?ConferenceRoom
    {
        return $this->conferenceRoom;
    }

    public function setConferenceRoom(?ConferenceRoom $conferenceRoom): static
    {
        $this->conferenceRoom = $conferenceRoom;

        return $this;
    }
}
