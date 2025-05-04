<?php

namespace App\Controller;

use App\Entity\Stop;
use App\Entity\StopTime;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use PhpParser\Node\Stmt\Continue_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

final class TclController extends AbstractController
{
    #[Route('/tcl', name: 'app_tcl')]
    public function index(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        $now = new DateTime();
        $dayName = $now->format('l');

        $stopList = ['INSA - Einstein', 'Place Croix-Luizet'];

        $conn = $entityManager->getConnection();

        $sql = '
        SELECT
	S.STOP_NAME,
	ST.DEPARTURE_TIME,
	S.STOP_ID,
	T.TRIP_ID,
	T.SERVICE_ID,
	T.TRIP_HEADSIGN,
	R.ROUTE_ID,
	R.ROUTE_SHORT_NAME,
	R.ROUTE_LONG_NAME,
    C.' . $dayName .'
FROM
	STOP S,
	STOP_TIME ST,
	TRIP T,
	ROUTES R,
	CALENDAR C
WHERE
	S.STOP_NAME IN ' . $this->formatListToSQL($stopList) . '
	AND ST.STOP_ID = S.STOP_ID
	AND ST.DEPARTURE_TIME > :now
	AND T.TRIP_ID = ST.TRIP_ID
	AND T.ROUTE_ID = R.ROUTE_ID
	AND C.SERVICE_ID = T.SERVICE_ID
	AND C.' . $dayName . '= \'1\'
ORDER BY ST.DEPARTURE_TIME

        ';

        $stopTimes = $conn->executeQuery($sql, ['now' => $now->format('H:i:s')]);
        $stopTimes = $stopTimes->fetchAllAssociative();

        $filteredTimesList = [];
        foreach ($stopTimes as $time) {
            if (count(array_filter($filteredTimesList, fn($value) => $value['route_long_name'] === $time['route_long_name'])) >= 2) 
                continue;
            $filteredTimesList[] = $time;

        }

        return new JsonResponse($filteredTimesList);
            
        // return $this->render('tcl/index.html.twig', [
        //     'data' => json_encode(array_map(fn($value) => $serializer->serialize($value, 'json'), $stopTimes)),
        //     // 'data' => $serializer->serialize($stopTimes[0], 'json'),
        // ]);
    }

    private function formatListToSQL($array)
    {
        return ' (\''. join('\', \'', $array) . '\')';
    }
}
