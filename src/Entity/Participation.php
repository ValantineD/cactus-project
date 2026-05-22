<?php

namespace App\Entity;

use App\Enum\EnumParticipationStatus;
use App\Repository\ParticipationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ParticipationRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Participation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true, enumType: EnumParticipationStatus::class)]
    private ?EnumParticipationStatus $status = EnumParticipationStatus::PENDING;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $registeredAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTime $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'participations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Activity $activity = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getStatus(): ?EnumParticipationStatus
    {
        return $this->status;
    }

    public function setStatus(?EnumParticipationStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    #[ORM\PrePersist]
    public function onRegisteredAt(): static{
        $this->registeredAt = new \DateTime("now");
        return $this;
    }
    public function getRegisteredAt(): ?\DateTime
    {
        return $this->registeredAt;
    }

    public function setRegisteredAt(?\DateTime $registeredAt): static
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    #[ORM\PreUpdate]
    public function onUpdatedAt(): static
    {
        $this->updatedAt = new \DateTime("now");
        return $this;
    }
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getActivity(): ?Activity
    {
        return $this->activity;
    }

    public function setActivity(?Activity $activity): static
    {
        $this->activity = $activity;

        return $this;
    }

}
