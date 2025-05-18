<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class RadioController extends AbstractController
{
    #[Route('/radio', name: 'app_radio')]
    public function index(): Response
    {
        return $this->render('radio/index.html.twig', [
            'controller_name' => 'RadioController',
        ]);
    }
}
