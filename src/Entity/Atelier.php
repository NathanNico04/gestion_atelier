<?php

namespace App\Entity;

use App\Repository\AtelierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AtelierRepository::class)]
class Atelier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'ateliers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'ateliersInscrits')]
    private Collection $apprentis;

    /**
     * @var Collection<int, AtelierSatisfaction>
     */
    #[ORM\OneToMany(targetEntity: AtelierSatisfaction::class, mappedBy: 'atelier')]
    private Collection $atelierSatisfactions;

    public function __construct()
    {
        $this->apprentis = new ArrayCollection();
        $this->atelierSatisfactions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

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

    /**
     * @return Collection<int, User>
     */
    public function getApprentis(): Collection
    {
        return $this->apprentis;
    }

    public function addApprenti(User $apprenti): static
    {
        if (!$this->apprentis->contains($apprenti)) {
            $this->apprentis->add($apprenti);
        }

        return $this;
    }

    public function removeApprenti(User $apprenti): static
    {
        $this->apprentis->removeElement($apprenti);

        return $this;
    }

    public function isParticipant(User $user): bool
    {
        return $this->apprentis->contains($user);
    }

    /**
     * @return Collection<int, AtelierSatisfaction>
     */
    public function getAtelierSatisfactions(): Collection
    {
        return $this->atelierSatisfactions;
    }

    public function addAtelierSatisfaction(AtelierSatisfaction $atelierSatisfaction): static
    {
        if (!$this->atelierSatisfactions->contains($atelierSatisfaction)) {
            $this->atelierSatisfactions->add($atelierSatisfaction);
            $atelierSatisfaction->setAtelier($this);
        }

        return $this;
    }

    public function removeAtelierSatisfaction(AtelierSatisfaction $atelierSatisfaction): static
    {
        if ($this->atelierSatisfactions->removeElement($atelierSatisfaction)) {
            // set the owning side to null (unless already changed)
            if ($atelierSatisfaction->getAtelier() === $this) {
                $atelierSatisfaction->setAtelier(null);
            }
        }

        return $this;
    }
}
