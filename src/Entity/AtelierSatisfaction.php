<?php

namespace App\Entity;

use App\Repository\AtelierSatisfactionRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AtelierSatisfactionRepository::class)]
class AtelierSatisfaction
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'atelierSatisfactions')]
    private ?User $apprenti = null;

    #[ORM\ManyToOne(inversedBy: 'atelierSatisfactions')]
    private ?Atelier $atelier = null;

    #[ORM\Column]
    private ?int $note = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApprenti(): ?User
    {
        return $this->apprenti;
    }

    public function setApprenti(?User $apprenti): static
    {
        $this->apprenti = $apprenti;

        return $this;
    }

    public function getAtelier(): ?Atelier
    {
        return $this->atelier;
    }

    public function setAtelier(?Atelier $atelier): static
    {
        $this->atelier = $atelier;

        return $this;
    }

    public function getNote(): ?int
    {
        return $this->note;
    }

    public function setNote(int $note): static
    {
        $this->note = $note;

        return $this;
    }
}
