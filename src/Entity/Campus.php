<?php

namespace App\Entity;

use App\Repository\CampusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CampusRepository::class)]
class Campus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $nom = null;

    #[ORM\OneToMany(mappedBy: 'campus', targetEntity: Participant::class)]
    private Collection $idParticipant;

    #[ORM\OneToMany(mappedBy: 'campus', targetEntity: Sortie::class)]
    private Collection $idSortie;

    public function __construct()
    {
        $this->idParticipant = new ArrayCollection();
        $this->idSortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Participant>
     */
    public function getIdParticipant(): Collection
    {
        return $this->idParticipant;
    }

    public function addIdParticipant(Participant $idParticipant): self
    {
        if (!$this->idParticipant->contains($idParticipant)) {
            $this->idParticipant->add($idParticipant);
            $idParticipant->setCampus($this);
        }

        return $this;
    }

    public function removeIdParticipant(Participant $idParticipant): self
    {
        if ($this->idParticipant->removeElement($idParticipant)) {
            // set the owning side to null (unless already changed)
            if ($idParticipant->getCampus() === $this) {
                $idParticipant->setCampus(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getIdSortie(): Collection
    {
        return $this->idSortie;
    }

    public function addIdSortie(Sortie $idSortie): self
    {
        if (!$this->idSortie->contains($idSortie)) {
            $this->idSortie->add($idSortie);
            $idSortie->setCampus($this);
        }

        return $this;
    }

    public function removeIdSortie(Sortie $idSortie): self
    {
        if ($this->idSortie->removeElement($idSortie)) {
            // set the owning side to null (unless already changed)
            if ($idSortie->getCampus() === $this) {
                $idSortie->setCampus(null);
            }
        }

        return $this;
    }
}
