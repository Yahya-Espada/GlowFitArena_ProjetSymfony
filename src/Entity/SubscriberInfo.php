<?php
namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class SubscriberInfo
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private $id;

    #[ORM\Column(name: "height", type: "float", precision: 10, scale: 0, nullable: true)]
    private $height;

    #[ORM\Column(name: "weight", type: "float", precision: 10, scale: 0, nullable: true)]
    private $weight;

    #[ORM\Column(name: "age", type: "integer", nullable: true)]
    private $age;

    #[ORM\Column(name: "gender", type: "string", length: 0, nullable: true)]
    private $gender;

    #[ORM\Column(name: "goals", type: "text", length: 65535, nullable: true)]
    private $goals;

    #[ORM\Column(name: "activity_level", type: "string", length: 0, nullable: true)]
    private $activityLevel;

    #[ORM\Column(name: "dietary_restrictions", type: "text", length: 65535, nullable: true)]
    private $dietaryRestrictions;

    #[ORM\Column(name: "food_preferences", type: "text", length: 65535, nullable: true)]
    private $foodPreferences;

    #[ORM\Column(name: "created_at", type: "datetime", nullable: true)]
    private $createdAt;

    #[ORM\Column(name: "updated_at", type: "datetime", nullable: true)]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: "User",fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "id")]
    private  $user;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHeight(): ?float
    {
        return $this->height;
    }

    public function setHeight(?float $height): self
    {
        $this->height = $height;

        return $this;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(?float $weight): self
    {
        $this->weight = $weight;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function getGoals(): ?string
    {
        return $this->goals;
    }

    public function setGoals(?string $goals): self
    {
        $this->goals = $goals;

        return $this;
    }

    public function getActivityLevel(): ?string
    {
        return $this->activityLevel;
    }

    public function setActivityLevel(?string $activityLevel): self
    {
        $this->activityLevel = $activityLevel;

        return $this;
    }

    public function getDietaryRestrictions(): ?string
    {
        return $this->dietaryRestrictions;
    }

    public function setDietaryRestrictions(?string $dietaryRestrictions): self
    {
        $this->dietaryRestrictions = $dietaryRestrictions;

        return $this;
    }

    public function getFoodPreferences(): ?string
    {
        return $this->foodPreferences;
    }

    public function setFoodPreferences(?string $foodPreferences): self
    {
        $this->foodPreferences = $foodPreferences;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

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
    public function __toString()
    {
        return $this->user->getUsername();
    }
}
