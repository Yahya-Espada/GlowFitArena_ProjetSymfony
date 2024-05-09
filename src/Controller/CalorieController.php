<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CalorieCalculator;
class CalorieController extends AbstractController
{
    #[Route('/calorie_calculator', name: 'calorie_calculator', methods: ['POST', 'GET'])]
    public function calculate(Request $request)
    {
        $age = $request->request->get('age');
        $activityLevel = $request->request->get('activity_level');
        $gender = $request->request->get('gender');
        $weight = $request->request->get('weight');
        $height = $request->request->get('height');

        if (null === $age || null === $activityLevel || null === $gender || null === $weight || null === $height) {
            // One or more fields are empty, return an error message
            return $this->render('calorie_calculator.html.twig', [
                'error' => 'All fields must be filled',
            ]);
        }

        $calculator = new CalorieCalculator();
        $calories = $calculator->calculateCalories((int)$age, $activityLevel, $gender, (float)$weight, (float)$height);

        return $this->render('calorie_calculator.html.twig', [
            'calorie_needs' => $calories,
        ]);
    }
}