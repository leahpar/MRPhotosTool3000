<?php

namespace App\Entity;

use App\Repository\ModeleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: ModeleRepository::class)]
class Modele implements UserInterface, PasswordAuthenticatedUserInterface, \Stringable
{
    use ResetPwdTrait;
    use CRMTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nom = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $pseudo = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $instagram = null;

    #[ORM\Column(type: 'string', length: 180, unique: true, nullable: true)]
    private ?string $username = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private ?string $password = null;
    private ?string $plainPassword = null;

    #[ORM\ManyToMany(targetEntity: Shooting::class, mappedBy: 'modeles')]
    private Collection $shootings;

    #[ORM\Column(type: 'datetime', nullable: true)]
    public ?\Datetime $dateDernierShooting = null;

    public function __construct()
    {
        $this->shootings = new ArrayCollection();
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

    public function getInstagram(): ?string
    {
        return $this->instagram;
    }

    public function setInstagram(?string $instagram): self
    {
        $this->instagram = str_replace('@', '', $instagram);

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(?string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getShootings(): Collection
    {
        return $this->shootings;
    }

    public function addShooting(Shooting $shooting): self
    {
        if (!$this->shootings->contains($shooting)) {
            $this->shootings[] = $shooting;
            $shooting->addModele($this);
        }

        return $this;
    }

    public function removeShooting(Shooting $shooting): self
    {
        if ($this->shootings->removeElement($shooting)) {
            $shooting->removeModele($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getPseudo() ?? $this->getNom() ?? ("@".$this->getInstagram());
    }

    /**
     * A visual identifier that represents this user.
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_MODELE';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->getRoles());
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function updateDateDernierShooting(): void
    {
        $date = null;
        /** @var Shooting $shooting */
        foreach ($this->getShootings() as $shooting) {
            if ($date === null || $shooting->getDate() > $date) {
                $date = $shooting->getDate();
            }
        }
        $this->dateDernierShooting = $date;
    }
}
