<?php

namespace App\Service;



use App\Interfaces\CalorieCalculatorInterface;

class CalorieCalculator implements CalorieCalculatorInterface
{
    public function calculateCalories(int $age, string $activityLevel, string $gender, float $weight, float $height): float
    {
        // Calculate BMR using Mifflin-St Jeor Equation
        $bmr = ($gender === 'male')
            ? (10 * $weight) + (6.25 * $height) - (5 * $age) + 5
            : (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;

        // Adjust BMR based on activity level
        switch ($activityLevel) {
            case 'sedentary':
                return $bmr * 1.2;
            case 'light':
                return $bmr * 1.375;
            case 'moderate':
                return $bmr * 1.55;
            case 'active':
                return $bmr * 1.725;
            case 'very active':
                return $bmr * 1.9;
            default:
                throw new InvalidArgumentException('Invalid activity level');
        }
    }
}