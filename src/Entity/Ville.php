<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VilleRepository::class)
 */
class Ville
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=5, nullable=true)
     */
    private $codePostal;

    /**
     * @ORM\OneToMany(targetEntity=Lieu::class, mappedBy="ville")
     */
    private $lieuVille;

    public function __construct()
    {
        $this->lieuVille = new ArrayCollection();
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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(?string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection|Lieu[]
     */
    public function getLieuVille(): Collection
    {
        return $this->lieuVille;
    }

    public function addLieuVille(Lieu $lieuVille): self
    {
        if (!$this->lieuVille->contains($lieuVille)) {
            $this->lieuVille[] = $lieuVille;
            $lieuVille->setVille($this);
        }

        return $this;
    }

    public function removeLieuVille(Lieu $lieuVille): self
    {
        if ($this->lieuVille->removeElement($lieuVille)) {
            // set the owning side to null (unless already changed)
            if ($lieuVille->getVille() === $this) {
                $lieuVille->setVille(null);
            }
        }

        return $this;
    }
}
