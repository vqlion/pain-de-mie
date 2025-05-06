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
        $now = new DateTime();
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
    C.START_DATE,
	C.END_DATE,
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
    AND C.START_DATE <= :nowDate
	AND C.END_DATE >= :nowDate
ORDER BY ST.DEPARTURE_TIME
	) A
WHERE
	RN = 1';



        $stopTimes = $conn->executeQuery($sql, [
            'now' => $now->format('H:i:s'),
            'nowDate' => $now->format('Y-m-d')
        ]);
        $stopTimes = $stopTimes->fetchAllAssociative();

        $processedTimes = [];
        $singleRouteName = [];

        $routeStopHashList = [];
        $routeIdList = [];

        foreach ($stopTimes as $stop) {
            if (count(array_filter($singleRouteName, fn($value) => $value === $stop['route_long_name'])) >= $timesThreshold)
                continue;
            $singleRouteName[] = $stop['route_long_name'];

            $stop['icon_url'] = $this->package->getUrl('/static/tcl_icons/' . $stop['route_short_name'] . '.svg');
            $stop['route_stop_hash'] = hash('md5', $stop['route_short_name'] . $stop['stop_name']);

            $processedTimes[] = $stop;

            $hashListEntry = [
                'hash' => $stop['route_stop_hash'],
                'icon' => $stop['icon_url'],
                'route_name' => $stop['route_short_name'],
                'stop_name' => $stop['stop_name'],
                'route_id' => [$stop['route_id']],
            ];

            $key = array_find_key($routeStopHashList, fn($value) => $value['hash'] == $stop['route_stop_hash']);
            if ($key === null)
                array_push($routeStopHashList, $hashListEntry);
            else if (!in_array($stop['route_id'], $routeStopHashList[$key]['route_id']))
                array_push($routeStopHashList[$key]['route_id'], $stop['route_id']);

            // if ($key = array_find_key($processedTimes, function ($value) use ($stop) {
            //     return (
            //         ($value['stop_name'] == $stop['stop_name'])
            //         &&
            //         ($value['route_short_name'] == $stop['route_short_name'])
            //     );
            // })) {
            //     $processedTimes[$key]['stop_times'][] = $stop;
            // } else {
            //     $processedTimes[] = [
            //         'stop_name' => $stop['stop_name'],
            //         'route_short_name' => $stop['route_short_name'],
            //         'icon_url' => $stop['icon_url'],
            //         'stop_times' => [$stop],
            //     ];
            // }
        }

        $filteredTimesList = [];
        $singleRouteName = [];
        foreach ($stopTimes as $time) {
            if (count(array_filter($singleRouteName, fn($value) => $value === $time['route_long_name'])) >= $timesThreshold)
                continue;
            $singleRouteName[] = $time['route_long_name'];

            $time['icon_url'] = $this->package->getUrl('/static/tcl_icons/' . $time['route_short_name'] . '.svg');

            $filteredTimesList[$time['stop_name']][$time['route_short_name']][] = $time;
        }

        return $this->render('tcl/index.html.twig', [
            'data' => $processedTimes,
            'route_stop_hash_list' => $routeStopHashList
        ]);
    }

    private function formatListToSQL($array)
    {
        return ' (\'' . join('\', \'', $array) . '\')';
    }
}
