<?php

namespace App\Entity;

use App\Repository\PressureRecordRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PressureRecordRepository::class)
 */
class PressureRecord
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="pressureRecords")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity=Tire::class, inversedBy="pressureRecords")
     */
    private $tire;

    /**
     * @ORM\ManyToOne(targetEntity=Driver::class, inversedBy="pressureRecords")
     */
    private $driver;

    /**
     * @ORM\ManyToOne(targetEntity=Circuit::class, inversedBy="pressureRecords")
     */
    private $circuit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $datetime;

    /**
     * @ORM\Column(type="smallint")
     */
    private $tempTrack;

    /**
     * @ORM\Column(type="smallint")
     */
    private $tempFrontLeft;

    /**
     * @ORM\Column(type="smallint")
     */
    private $tempFrontRight;

    /**
     * @ORM\Column(type="smallint")
     */
    private $tempRearLeft;

    /**
     * @ORM\Column(type="smallint")
     */
    private $tempRearRight;

    /**
     * @ORM\Column(type="float")
     */
    private $pressFrontLeft;

    /**
     * @ORM\Column(type="float")
     */
    private $pressFrontRight;

    /**
     * @ORM\Column(type="float")
     */
    private $pressRearLeft;

    /**
     * @ORM\Column(type="float")
     */
    private $pressRearRight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $note;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;
        // $user->addPressureRecord($this);

        return $this;
    }

    public function getTire(): ?Tire
    {
        return $this->tire;
    }

    public function setTire(?Tire $tire): self
    {
        $this->tire = $tire;

        return $this;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function setDriver(?Driver $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getCircuit(): ?Circuit
    {
        return $this->circuit;
    }

    public function setCircuit(?Circuit $circuit): self
    {
        $this->circuit = $circuit;

        return $this;
    }

    public function getDatetime(): ?\DateTimeInterface
    {
        return $this->datetime;
    }

    public function setDatetime(\DateTimeInterface $datetime): self
    {
        $this->datetime = $datetime;

        return $this;
    }

    public function getTempTrack(): ?int
    {
        return $this->tempTrack;
    }

    public function setTempTrack(int $tempTrack): self
    {
        $this->tempTrack = $tempTrack;

        return $this;
    }

    public function getTempFrontLeft(): ?int
    {
        return $this->tempFrontLeft;
    }

    public function setTempFrontLeft(int $tempFrontLeft): self
    {
        $this->tempFrontLeft = $tempFrontLeft;

        return $this;
    }

    public function getTempFrontRight(): ?int
    {
        return $this->tempFrontRight;
    }

    public function setTempFrontRight(int $tempFrontRight): self
    {
        $this->tempFrontRight = $tempFrontRight;

        return $this;
    }

    public function getTempRearLeft(): ?int
    {
        return $this->tempRearLeft;
    }

    public function setTempRearLeft(int $tempRearLeft): self
    {
        $this->tempRearLeft = $tempRearLeft;

        return $this;
    }

    public function getTempRearRight(): ?int
    {
        return $this->tempRearRight;
    }

    public function setTempRearRight(int $tempRearRight): self
    {
        $this->tempRearRight = $tempRearRight;

        return $this;
    }

    public function getPressFrontLeft(): ?float
    {
        return $this->pressFrontLeft;
    }

    public function setPressFrontLeft(float $pressFrontLeft): self
    {
        $this->pressFrontLeft = $pressFrontLeft;

        return $this;
    }

    public function getPressFrontRight(): ?float
    {
        return $this->pressFrontRight;
    }

    public function setPressFrontRight(float $pressFrontRight): self
    {
        $this->pressFrontRight = $pressFrontRight;

        return $this;
    }

    public function getPressRearLeft(): ?float
    {
        return $this->pressRearLeft;
    }

    public function setPressRearLeft(float $pressRearLeft): self
    {
        $this->pressRearLeft = $pressRearLeft;

        return $this;
    }

    public function getPressRearRight(): ?float
    {
        return $this->pressRearRight;
    }

    public function setPressRearRight(float $pressRearRight): self
    {
        $this->pressRearRight = $pressRearRight;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }
}
