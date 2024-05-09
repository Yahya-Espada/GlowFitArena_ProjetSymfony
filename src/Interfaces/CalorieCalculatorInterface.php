<?php
namespace App\Interfaces;

interface CalorieCalculatorInterface
{
    public function calculateCalories(int $age, string $activityLevel, string $gender, float $weight, float $height): float;
}