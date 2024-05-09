<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CategorieRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id_categorie", type: "integer", nullable: false)]
    private int $idCategorie;

    #[ORM\Column(name: "nom_categorie", type: "string", length: 255, nullable: false)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Assert\Type(type: 'string')]
    private string $nomCategorie;

    #[ORM\Column(name: "type_categorie", type: "string", length: 20, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Assert\Type(type: 'string')]
    private ?string $typeCategorie;

    public function getIdCategorie(): ?int
    {
        return $this->idCategorie;
    }

    public function getNomCategorie(): ?string
    {
        return $this->nomCategorie;
    }

    public function setNomCategorie(string $nomCategorie): static
    {
        $this->nomCategorie = $nomCategorie;

        return $this;
    }

    public function getTypeCategorie(): ?string
    {
        return $this->typeCategorie;
    }

    public function setTypeCategorie(?string $typeCategorie): static
    {
        $this->typeCategorie = $typeCategorie;

        return $this;
    }

    #[Assert\Callback]
    public function validateTypeCategorie(ExecutionContextInterface $context): void
    {
        // Ensure that typeCategorie does not contain any numbers
        if (preg_match('/\d/', $this->typeCategorie)) {
            $context->buildViolation('The typeCategorie field cannot contain numbers.')
                ->atPath('typeCategorie')
                ->addViolation();
        }
        if (preg_match('/\d/', $this->nomCategorie)) {
            $context->buildViolation('The nomCategorie field cannot contain numbers.')
                ->atPath('nomCategorie')
                ->addViolation();
        }
    }


}
