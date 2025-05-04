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
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\JsonResponse;

final class TclController extends AbstractController
{
    private Package $package;

    public function __construct()
    {
        $this->package = new Package(new EmptyVersionStrategy());
    }

    #[Route('/tcl', name: 'app_tcl')]
    public function index(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger
    ): Response {
        $now = new DateTime('2025-05-04 12:00');
        $dayName = $now->format('l');

        $stopList = ['INSA - Einstein', 'Place Croix-Luizet'];

        $timesThreshold = 2;

        $conn = $entityManager->getConnection();

        $sql = '
        SELECT
	*
FROM
	(
		SELECT
	S.STOP_NAME,
	ST.DEPARTURE_TIME,
    ROW_NUMBER() OVER (
				PARTITION BY
					ST.DEPARTURE_TIME
				ORDER BY
					ST.ID DESC
			) RN,
	S.STOP_ID,
	T.TRIP_ID,
	T.SERVICE_ID,
	T.TRIP_HEADSIGN,
	R.ROUTE_ID,
	R.ROUTE_SHORT_NAME,
	R.ROUTE_LONG_NAME,
    C.' . $dayName . ',
    R.ROUTE_COLOR
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
	) A
WHERE
	RN = 1';



        $stopTimes = $conn->executeQuery($sql, ['now' => $now->format('H:i:s')]);
        $stopTimes = $stopTimes->fetchAllAssociative();

        $filteredTimesList = [];
        $singleRouteName = [];
        foreach ($stopTimes as $time) {
            if (count(array_filter($singleRouteName, fn($value) => $value === $time['route_long_name'])) >= $timesThreshold)
                continue;
            $singleRouteName[] = $time['route_long_name'];

            $time['icon_url'] = $this->package->getUrl('/static/tcl_icons/' . $time['route_short_name'] . '.svg');

            $filteredTimesList[$time['stop_name']][$time['route_short_name']][] = $time;
        }

        // return new JsonResponse($filteredTimesList);

        return $this->render('tcl/index.html.twig', [
            'data' => $filteredTimesList
        ]);
    }

    private function formatListToSQL($array)
    {
        return ' (\'' . join('\', \'', $array) . '\')';
    }
}
