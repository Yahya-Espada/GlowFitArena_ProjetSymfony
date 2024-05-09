<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\SeanceRepository;




#[ORM\Entity(repositoryClass: SeanceRepository::class)]
class Seance
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    private $id;

    #[ORM\Column(name: "heuredebut", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message: "L'heure de début est obligatoire")]
    private $heuredebut;

    #[ORM\Column(name: "heurefin", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message: "L'heure de fin est obligatoire")]
    private $heurefin;

    #[ORM\Column(name: "date", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message: "La date est obligatoire")]
    private $date;

    #[ORM\Column(name: "prix", type: "float", precision: 10, scale: 0, nullable: false)]
    #[Assert\NotBlank(message: "Le prix est obligatoire")]
    #[Assert\Positive(message: "Le prix doit être un nombre positif")]
    private $prix;

    #[ORM\Column(name: "typeSeance", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank(message: "Le type de séance est obligatoire")]
    private $typeseance;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Planning")]
    #[ORM\JoinColumn(name: "idPlanning", referencedColumnName: "idPlanning")]
    #[Assert\NotBlank(message: "Le numéro de planning est obligatoire")]
    private $idPlanning;

   
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "iduser", referencedColumnName: "id")]
    private $iduser;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeuredebut(): ?string
    {
        return $this->heuredebut;
    }

    public function setHeuredebut(string $heuredebut): static
    {
        $this->heuredebut = $heuredebut;

        return $this;
    }

    public function getHeurefin(): ?string
    {
        return $this->heurefin;
    }

    public function setHeurefin(string $heurefin): static
    {
        $this->heurefin = $heurefin;

        return $this;
    }

    public function getDate(): ?string
    {
        return $this->date;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

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

    public function getTypeseance(): ?string
    {
        return $this->typeseance;
    }

    public function setTypeseance(string $typeseance): static
    {
        $this->typeseance = $typeseance;

        return $this;
    }



    public function setIdplanning(?Planning $idplanning): static
    {
        $this->idPlanning = $idplanning;

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
    public function getIdPlanning(): ?Planning
    {
        return $this->idPlanning;
    }
}
