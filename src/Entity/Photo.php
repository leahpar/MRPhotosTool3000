<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhotoRepository::class)
 */
class Photo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Shooting::class, inversedBy="photos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $shooting;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $motsCles;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $file;

    /**
     * @ORM\Column(type="float")
     */
    private $ratio;

    /**
     * @ORM\ManyToMany(targetEntity=Galerie::class, mappedBy="photos", cascade={"persist"})
     */
    private $galeries;

    /**
     * @ORM\ManyToOne(targetEntity=Publication::class, inversedBy="photos")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $publication;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $datePublication;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class)
     */
    private $tags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $moreTags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $censure = [];


    public function __construct()
    {
        $this->galeries = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShooting(): ?Shooting
    {
        return $this->shooting;
    }

    public function setShooting(?Shooting $shooting): self
    {
        $this->shooting = $shooting;

        return $this;
    }

    public function getMotsCles(): ?string
    {
        return $this->motsCles;
    }

    public function setMotsCles(?string $motsCles): self
    {
        $this->motsCles = $motsCles;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(string $file): self
    {
        $this->file = $file;

        return $this;
    }

    /**
     * URL de l'image pour l'affichage dans l'admin
     * @return string
     */
    public function getThumbnail(?string $filtre = "thumbnail"): string
    {
        return "/shootings/".$this->shooting->getSlug()."/".$this->file."?filter=".$filtre;
    }

    public function getRatio(): ?float
    {
        return $this->ratio;
    }

    public function setRatio(float $ratio): self
    {
        $this->ratio = $ratio;

        return $this;
    }

    public function getGaleries(): Collection
    {
        return $this->galeries;
    }

    public function setGaleries($galeries): self
    {
        $this->galeries = $galeries;
        return $this;
    }

    public function addGalery(Galerie $galery): self
    {
        if (!$this->galeries->contains($galery)) {
            $this->galeries[] = $galery;
            $galery->addPhoto($this);
        }

        return $this;
    }

    public function removeGalery(Galerie $galery): self
    {
        if ($this->galeries->removeElement($galery)) {
            $galery->removePhoto($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->shooting->getNom().' - '.$this->file;
    }


    public function setTags(Collection $tags): self
    {
        $this->tags = $tags;
        return $this;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }

    public function getMoreTags(): ?string
    {
        return $this->moreTags;
    }

    public function setMoreTags(?string $moreTags): self
    {
        $this->moreTags = $moreTags;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->publication;
    }

    public function setPublication(?Publication $publication): self
    {
        $this->publication = $publication;

        return $this;
    }

    public function getDatePublication(?string $format = null)
    {
        if ($format && $this->datePublication != null) {
            return $this->datePublication->format($format);
        }
        else {
            return $this->datePublication;
        }
    }

    public function setDatePublication(?\DateTimeInterface $datePublication): self
    {
        $this->datePublication = $datePublication;

        return $this;
    }

    public function getDatePlanifiee(): ?\DateTime
    {
        if ($this->getPublication()) {
            return $this->getPublication()->getDate();
        }
        return null;
    }

    public function isPublished(): bool
    {
        return $this->datePublication != null;
    }


    public function addCensure(string $censure): self
    {
        $this->censure[] = $censure;

        return $this;
    }

    public function getCensure(): ?array
    {
        return $this->censure;
    }

    public function setCensure(?array $censure): self
    {
        $this->censure = $censure;

        return $this;
    }

    public function isCensure(): bool
    {
        return count($this->censure) > 0;
    }

    public function isCouv(): bool
    {
        /** @var Galerie $galery */
        foreach ($this->galeries as $galerie) {
            if ($galerie->isCouv()) return true;
        }
        return false;
    }

    public function isCover(): bool
    {
        /** @var Galerie $galery */
        foreach ($this->galeries as $galerie) {
            if ($galerie->isCover()) return true;
        }
        return false;
    }

    public function isSite(): bool
    {
        /** @var Galerie $galery */
        foreach ($this->galeries as $galerie) {
            if ($galerie->isFront()) return true;
        }
        return false;
    }

}
