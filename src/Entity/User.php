<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fullName;

    /**
     * @ORM\OneToMany(targetEntity=Tire::class, mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $tires;

    /**
     * @ORM\OneToMany(targetEntity=Driver::class, mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $drivers;

    /**
     * @ORM\OneToMany(targetEntity=Circuit::class, mappedBy="user", orphanRemoval=true, cascade={"persist"})
     */
    private $circuits;

    /**
     * @ORM\OneToMany(targetEntity=PressureRecord::class, mappedBy="user", cascade={"persist"})
     */
    private $pressureRecords;

    public function __construct()
    {
        $this->tires = new ArrayCollection();
        $this->drivers = new ArrayCollection();
        $this->circuits = new ArrayCollection();
        $this->pressureRecords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    /**
     * @return Collection|Tire[]
     */
    public function getTires(): Collection
    {
        return $this->tires;
    }

    public function addTire(Tire $tire): self
    {
        if (!$this->tires->contains($tire)) {
            $this->tires[] = $tire;
            $tire->setUser($this);
        }

        return $this;
    }

    public function removeTire(Tire $tire): self
    {
        if ($this->tires->removeElement($tire)) {
            // set the owning side to null (unless already changed)
            if ($tire->getUser() === $this) {
                $tire->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Driver[]
     */
    public function getDrivers(): Collection
    {
        return $this->drivers;
    }

    public function addDriver(Driver $driver): self
    {
        if (!$this->drivers->contains($driver)) {
            $this->drivers[] = $driver;
            $driver->setUser($this);
        }

        return $this;
    }

    public function removeDriver(Driver $driver): self
    {
        if ($this->drivers->removeElement($driver)) {
            // set the owning side to null (unless already changed)
            if ($driver->getUser() === $this) {
                $driver->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Circuit[]
     */
    public function getCircuits(): Collection
    {
        return $this->circuits;
    }

    public function addCircuit(Circuit $circuit): self
    {
        if (!$this->circuits->contains($circuit)) {
            $this->circuits[] = $circuit;
            $circuit->setUser($this);
        }

        return $this;
    }

    public function removeCircuit(Circuit $circuit): self
    {
        if ($this->circuits->removeElement($circuit)) {
            // set the owning side to null (unless already changed)
            if ($circuit->getUser() === $this) {
                $circuit->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PressureRecord[]
     */
    public function getPressureRecords(): Collection
    {
        return $this->pressureRecords;
    }

    public function addPressureRecord(PressureRecord $pressureRecord): self
    {
        if (!$this->pressureRecords->contains($pressureRecord)) {
            $this->pressureRecords[] = $pressureRecord;
            $pressureRecord->setUser($this);
        }

        return $this;
    }

    public function removePressureRecord(PressureRecord $pressureRecord): self
    {
        if ($this->pressureRecords->removeElement($pressureRecord)) {
            // set the owning side to null (unless already changed)
            if ($pressureRecord->getUser() === $this) {
                $pressureRecord->setUser(null);
            }
        }

        return $this;
    }
}
