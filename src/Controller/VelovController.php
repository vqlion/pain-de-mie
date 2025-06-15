<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class VelovController extends AbstractController
{

    private array $stopList;
    private string $nameRegex;

    public function __construct()
    {
        $jsonVeloStops = file_get_contents('../public/static/json/velov_stops.json');
        $this->stopList = json_decode($jsonVeloStops, true);
        $this->nameRegex = "/^\d+\s-\s(.+)/";
    }

    #[Route('/velov', name: 'app_velov')]
    public function index(
        HttpClientInterface $client,
    ): Response {
        $response = $client->request('GET', $_ENV['VELOV_TEMPSREEL_URL']);

        $data = json_decode(str_replace('\"', '', $response->getContent()), true);
        $stops = array_filter($data['values'], fn($value) => in_array($value['number'] ?? null, $this->stopList));
        $stops = array_values($stops);
        usort($stops, function($a, $b) {
            $aKey = array_search($a['number'], $this->stopList);
            $bKey = array_search($b['number'], $this->stopList);

            return ($aKey < $bKey) ? -1 : 1;
        });

        $stops = array_map(function ($value) {
            preg_match($this->nameRegex, $value['name'], $matches);
            $value['name'] = $matches[1];
            return $value;
        }, $stops);

        return $this->render('velov/index.html.twig', [
            "data" => $stops
        ]);
    }
}
