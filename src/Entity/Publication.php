<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PublicationRepository::class)
 */
class Publication
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="publication")
     */
    private Collection $photos;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class)
     */
    private Collection $tags;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $moreTags = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $description = null;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private ?\DateTime $date = null;


    public function __construct()
    {
        $this->photos = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $photo->setPublication($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getPublication() === $this) {
                $photo->setPublication(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tag[]
     */
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

    /**
     * URL des images pour l'affichage dans l'admin
     */
    public function getThumbnails(?string $filtre = "thumbnail")
    {
        $res = [];
        /** @var Photo $photo */
        foreach ($this->photos as $photo) {
            $res[$photo->getId()] = "/shootings/" . $photo->getShooting()->getSlug() . "/" . $photo->getFile() . "?filter=" . $filtre;
        }
        return $res;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(?\DateTime $date): self
    {
        $this->date = $date;
        return $this;
    }

}
