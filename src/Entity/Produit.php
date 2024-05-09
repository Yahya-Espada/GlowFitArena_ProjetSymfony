<?php

namespace App\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProduitRepository;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[Vich\Uploadable]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "id_produit", type: "integer", nullable: false)]
    private int $idProduit;

    #[ORM\Column(name: "libelle", type: "string", length: 20, nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 20)]
    #[Assert\Type(type: 'string')]
    private ?string $libelle;


    #[ORM\Column(name: "quantite", type: "integer", nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?int $quantite;

    #[ORM\Column(name: "prix", type: "integer", nullable: true)]
    #[Assert\NotBlank]
    #[Assert\Positive]
    private ?int $prix;

    #[ORM\Column(name: "photo_produit", type: "string", length: 255, nullable: true)]
    private ?string $photoProduit;

    #[Vich\UploadableField(mapping: "product_images", fileNameProperty: "photoProduit")]
    private ?File $imageFile = null;

    #[ORM\ManyToOne(targetEntity: Categorie::class)]
    #[ORM\JoinColumn(name: "id_categorie", referencedColumnName: "id_categorie")]
    private ?Categorie $idCategorie;

    public function getIdProduit(): ?int
    {
        return $this->idProduit;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(?string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(?int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(?int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getPhotoProduit(): ?string
    {
        return $this->photoProduit;
    }

    public function setPhotoProduit(?string $photoProduit): self
    {
        $this->photoProduit = $photoProduit;

        return $this;
    }

    public function getIdCategorie(): ?Categorie
    {
        return $this->idCategorie;
    }

    public function setIdCategorie(?Categorie $idCategorie): self
    {
        $this->idCategorie = $idCategorie;

        return $this;
    }

    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It's required that at least one field changes if you are using doctrine
            $this->updatedAt = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    
}
