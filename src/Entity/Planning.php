<?php

namespace App\Entity;

use App\Repository\PlanningRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: PlanningRepository::class)]
class Planning
{
    #[ORM\Column(name: "idPlanning", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $idplanning;

    #[ORM\Column(name: "titre", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Le titre est obligatoire")]
    private string $titre;

    #[ORM\Column(name: "nbSeance", type: "integer", nullable: false)]
    #[Assert\NotBlank(message: "Le nombre de séance est obligatoire")]
    #[Assert\Positive(message: "Le nombre de séance doit être un entier positif")]
    private int $nbseance;

    #[ORM\Column(name: "prix", type: "float", precision: 10, scale: 0, nullable: false)]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif")]
    private float $prix;


    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "iduser", referencedColumnName: "id")]
    public ?User $iduser;


    public function getIdplanning(): ?int
    {
        return $this->idplanning;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNbseance(): ?int
    {
        return $this->nbseance;
    }

    public function setNbseance(int $nbseance): static
    {
        $this->nbseance = $nbseance;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): static
    {
        $this->iduser = $iduser;

        return $this;
    }
    public function __toString(): string
    {
        // Retourner la valeur de l'identifiant du planning
        return (string) $this->getIdplanning();
    }
}
