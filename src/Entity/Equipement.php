<?php

namespace App\Entity;

use App\Repository\EquipementRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EquipementRepository::class)]
class Equipement
{
    
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    #[Assert\Range(
        min: 1,
        max: 5,
        notInRangeMessage: 'Le taux doit être compris entre 1 et 5.'
    )]
    private ?int $rate = null;

    #[ORM\Column(length: 255)]
    #[Assert\DateTime(message: 'La date doit être au format datetime.')]
    private ?string $date_equip = null;

    #[ORM\Column(length: 255)]
    private ?string $Type_equip = null;

    #[ORM\Column(length: 255)]
    private ?string $Reclamation = null;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'id')]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(int $rate): static
    {
        $this->rate = $rate;

        return $this;
    }

    public function getDateEquip(): ?string
    {
        return $this->date_equip;
    }

    public function setDateEquip(string $date_equip): static
    {
        $this->date_equip = $date_equip;

        return $this;
    }

    public function getReclamation(): ?string
    {
        return $this->Reclamation;
    }

    public function setReclamation(string $Reclamation): static
    {
        $this->Reclamation = $Reclamation;

        return $this;
    }

    public function getTypeEquip(): ?string
    {
        return $this->Type_equip;
    }

    public function setTypeEquip(string $Type_equip): static
    {
        $this->Type_equip = $Type_equip;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): static
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setIdEquip($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): static
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getIdEquip() === $this) {
                $reservation->setIdEquip(null);
            }
        }

        return $this;
    }








    
}
