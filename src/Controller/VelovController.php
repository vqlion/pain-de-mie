<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class VelovController extends AbstractController
{
    #[Route('/velov', name: 'app_velov')]
    public function index(
        HttpClientInterface $client,
    ): Response {
        $response = $client->request('GET', $_ENV['VELOV_TEMPSREEL_URL']);

        $data = json_decode($response->getContent());

        

        return $this->render('velov/index.html.twig', [
            "data" => $data
        ]);
    }
}
