<?php

namespace App\Entity;

use App\Repository\DriverRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DriverRepository::class)
 */
class Driver
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="drivers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=PressureRecord::class, mappedBy="driver")
     */
    private $pressureRecords;

    public function __construct()
    {
        $this->pressureRecords = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

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
            $pressureRecord->setDriver($this);
        }

        return $this;
    }

    public function removePressureRecord(PressureRecord $pressureRecord): self
    {
        if ($this->pressureRecords->removeElement($pressureRecord)) {
            // set the owning side to null (unless already changed)
            if ($pressureRecord->getDriver() === $this) {
                $pressureRecord->setDriver(null);
            }
        }

        return $this;
    }
}
