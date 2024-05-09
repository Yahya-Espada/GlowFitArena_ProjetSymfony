<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Index(columns: ["subscriber_id"], name: "subscriber_id")]
#[ORM\Index(columns: ["coach_id"], name: "coach_id")]
class DietaryProgramsCopy
{
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    private int $id;


    #[ORM\Column(name: "name", type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Name cannot be blank.")]
    private ?string $name;

    #[Assert\NotBlank(message: "Description cannot be blank.")]
    #[ORM\Column(name: "description", type: "text", length: 65535, nullable: true)]
    private ?string $description;


    #[Assert\NotBlank(message: "Start date cannot be blank.")]
    #[ORM\Column(name: "start_date", type: "date", nullable: true)]
    private ?\DateTime $startDate;

    #[Assert\NotBlank(message: "End date cannot be blank.")]
    #[ORM\Column(name: "end_date", type: "date", nullable: true)]
    private ?\DateTime $endDate;

    #[Assert\NotBlank(message: "Calorie goal cannot be blank.")]
    #[Assert\Positive(message: "Calorie goal must be a positive number.")]
    #[ORM\Column(name: "calorie_goal", type: "integer", nullable: true)]
    private ?int $calorieGoal;

    #[ORM\Column(name: "macro_ratio_carbs", type: "float", precision: 10, scale: 0, nullable: true)]
    #[Assert\NotBlank(message: "Macro ratio for carbs cannot be blank.")]
    private ?float $macroRatioCarbs;

    #[ORM\Column(name: "macro_ratio_protein", type: "float", precision: 10, scale: 0, nullable: true)]
    #[Assert\NotBlank(message: "Macro ratio for protein cannot be blank.")]
    private ?float $macroRatioProtein;

    #[ORM\Column(name: "macro_ratio_fat", type: "float", precision: 10, scale: 0, nullable: true)]
    #[Assert\NotBlank(message: "Macro ratio for fat cannot be blank.")]
    private ?float $macroRatioFat;


    #[ORM\Column(name: "meal_types", type: "string", length: 255, nullable: true)]
    #[Assert\NotBlank(message: "Meal types cannot be blank.")]
    private ?string $mealTypes;

    #[ORM\Column(name: "notes", type: "text", length: 65535, nullable: true)]
    private ?string $notes;

    #[ORM\Column(name: "created_at", type: "datetime", nullable: true)]
    private ?\DateTime $createdAt;
    #[ORM\Column(name: "updated_at", type: "datetime", nullable: true)]
    private ?\DateTime $updatedAt;

    #[ORM\ManyToOne(targetEntity: "User" )]
    #[ORM\JoinColumn(name: "coach_id", referencedColumnName: "id")]
    private ?User $coach;

    #[ORM\ManyToOne(targetEntity: "User" )]
    #[ORM\JoinColumn(name: "subscriber_id", referencedColumnName: "id")]
    private ?User $subscriber;


    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?\DateTime $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): self
    {
        $this->endDate = $endDate;
        return $this;
    }

    public function getCalorieGoal(): ?int
    {
        return $this->calorieGoal;
    }

    public function setCalorieGoal(?int $calorieGoal): self
    {
        $this->calorieGoal = $calorieGoal;
        return $this;
    }

    public function getMacroRatioCarbs(): ?float
    {
        return $this->macroRatioCarbs;
    }

    public function setMacroRatioCarbs(?float $macroRatioCarbs): self
    {
        $this->macroRatioCarbs = $macroRatioCarbs;
        return $this;
    }

    public function getMacroRatioProtein(): ?float
    {
        return $this->macroRatioProtein;
    }

    public function setMacroRatioProtein(?float $macroRatioProtein): self
    {
        $this->macroRatioProtein = $macroRatioProtein;
        return $this;
    }

    public function getMacroRatioFat(): ?float
    {
        return $this->macroRatioFat;
    }

    public function setMacroRatioFat(?float $macroRatioFat): self
    {
        $this->macroRatioFat = $macroRatioFat;
        return $this;
    }

    public function getMealTypes(): ?string
    {
        return $this->mealTypes;
    }

    public function setMealTypes(?string $mealTypes): self
    {
        $this->mealTypes = $mealTypes;
        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): self
    {
        $this->notes = $notes;
        return $this;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getCoach(): ?User
    {
        return $this->coach;
    }

    public function setCoach(?User $coach): self
    {
        $this->coach = $coach;
        return $this;
    }

    public function getSubscriber(): ?User
    {
        return $this->subscriber;
    }

    public function setSubscriber(?User $subscriber): self
    {
        $this->subscriber = $subscriber;
        return $this;
    }
}