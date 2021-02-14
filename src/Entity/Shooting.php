<?php

namespace App\Entity;

use App\Repository\ShootingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass=ShootingRepository::class)
 */
class Shooting
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Gedmo\Slug(
     *     fields={"date", "nom"},
     *     separator="-",
     *     updatable=false,
     *     unique=true,
     *     dateFormat="Y-m-d")
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $bookshoot;

    /**
     * @ORM\Column(type="date", nullable=false)
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut = "Brouillon";

    /**
     * @ORM\ManyToMany(targetEntity=Modele::class, inversedBy="shootings")
     */
    private $modeles;

    /**
     * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="shooting", orphanRemoval=true)
     * @ORM\OrderBy({"file" = "ASC"})
     */
    private $photos;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;


    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->tags = new ArrayCollection();
        $this->modeles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBookshoot(): ?string
    {
        return $this->bookshoot;
    }

    public function setBookshoot(?string $bookshoot): self
    {
        $this->bookshoot = $bookshoot;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setShooting($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getShooting() === $this) {
                $photo->setShooting(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Modele[]
     */
    public function getModeles(): Collection
    {
        return $this->modeles;
    }

    public function hasModele(?Modele $modele): bool
    {
        return $modele && $this->modeles->contains($modele);
    }

    public function addModele(Modele $modele): self
    {
        if (!$this->modeles->contains($modele)) {
            $this->modeles[] = $modele;
        }

        return $this;
    }

    public function removeModele(Modele $modele): self
    {
        $this->modeles->removeElement($modele);

        return $this;
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

    public function __toString(): string
    {
        return "BS".$this->bookshoot.' '.$this->nom;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCouv(): ?Photo
    {
        /** @var Photo $photo */
        foreach ($this->photos as $photo) {
            if ($photo->isCouv()) {
                return $photo;
            }
        }
        return null;
    }

}
