<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MeteoFranceController extends AbstractController
{

    private array $meteoList;
    private string $color = "7D85E2";

    public function __construct()
    {
        $meteoListJson = file_get_contents('../public/static/json/meteo_france.json');
        $this->meteoList = json_decode($meteoListJson, true);
        $this->meteoList = array_map(fn($value) => $value . $this->color, $this->meteoList);
    }

    #[Route('/meteo', name: 'app_meteo_france')]
    public function index(): Response
    {
        return $this->render('meteo_france/index.html.twig', [
            'data' => $this->meteoList
        ]);
    }
}
