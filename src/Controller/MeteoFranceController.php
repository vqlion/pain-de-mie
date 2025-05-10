<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MeteoFranceController extends AbstractController
{
    #[Route('/meteo', name: 'app_meteo_france')]
    public function index(): Response
    {
        return $this->render('meteo_france/index.html.twig');
    }
}
