<?php

namespace App\Entity;

use App\Repository\GalerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

#[ORM\Entity(repositoryClass: GalerieRepository::class)]
class Galerie implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToMany(targetEntity: Photo::class, inversedBy: 'galeries')]
    private $photos;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    private ?string $slug = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isCover = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isCouv = false;

    #[ORM\Column(type: 'boolean')]
    private bool $isFront = false;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $statut = null;

    public function __construct()
    {
        $this->photos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function setPhotos(Collection $photos): self
    {
        $this->photos = $photos;
        return $this;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        $this->photos->removeElement($photo);

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getRandomPhoto(): Photo
    {
        return $this->photos[random_int(0, count($this->photos)-1)];
    }

    public function __toString(): string
    {
        return (string) $this->nom;
    }

    public function isCover(): ?bool
    {
        return $this->isCover;
    }

    public function setIsCover(bool $isCover): self
    {
        $this->isCover = $isCover;

        return $this;
    }

    public function isCouv(): ?bool
    {
        return $this->isCouv;
    }

    public function setIsCouv(bool $isCouv): self
    {
        $this->isCouv = $isCouv;

        return $this;
    }

    public function isFront(): ?bool
    {
        return $this->isFront;
    }

    public function setIsFront(bool $isFront): self
    {
        $this->isFront = $isFront;

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
}
