<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SiteRepository::class)
 */
class Site
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
     * @ORM\OneToMany(targetEntity=Participant::class, mappedBy="site")
     */
    private $rattachement;

    /**
     * @ORM\OneToMany(targetEntity=Sortie::class, mappedBy="site")
     */
    private $siteOrganisateur;

    public function __construct()
    {
        $this->rattachement = new ArrayCollection();
        $this->siteOrganisateur = new ArrayCollection();
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
     * @return Collection|Participant[]
     */
    public function getRattachement(): Collection
    {
        return $this->rattachement;
    }

    public function addRattachement(Participant $rattachement): self
    {
        if (!$this->rattachement->contains($rattachement)) {
            $this->rattachement[] = $rattachement;
            $rattachement->setSite($this);
        }

        return $this;
    }

    public function removeRattachement(Participant $rattachement): self
    {
        if ($this->rattachement->removeElement($rattachement)) {
            // set the owning side to null (unless already changed)
            if ($rattachement->getSite() === $this) {
                $rattachement->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSiteOrganisateur(): Collection
    {
        return $this->siteOrganisateur;
    }

    public function addSiteOrganisateur(Sortie $siteOrganisateur): self
    {
        if (!$this->siteOrganisateur->contains($siteOrganisateur)) {
            $this->siteOrganisateur[] = $siteOrganisateur;
            $siteOrganisateur->setSite($this);
        }

        return $this;
    }

    public function removeSiteOrganisateur(Sortie $siteOrganisateur): self
    {
        if ($this->siteOrganisateur->removeElement($siteOrganisateur)) {
            // set the owning side to null (unless already changed)
            if ($siteOrganisateur->getSite() === $this) {
                $siteOrganisateur->setSite(null);
            }
        }

        return $this;
    }
}
