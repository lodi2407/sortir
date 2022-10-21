<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'etat', targetEntity: Sortie::class)]
    private Collection $idSortie;

    public function __construct()
    {
        $this->idSortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $idSortie->setEtat($this);
        }

        return $this;
    }

    public function removeIdSortie(Sortie $idSortie): self
    {
        if ($this->idSortie->removeElement($idSortie)) {
            // set the owning side to null (unless already changed)
            if ($idSortie->getEtat() === $this) {
                $idSortie->setEtat(null);
            }
        }

        return $this;
    }
}
