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

    private string $tramRegex;
    private string $metroRegex;
    private string $CBusRegex;


    public function __construct()
    {
        $this->package = new Package(new EmptyVersionStrategy());

        $this->tramRegex = '/^(T|BRT)[0-9]+$/';
        $this->metroRegex = '/^[A-Z]$/';
        $this->CBusRegex = '/^C[0-9]+$/';
    }

    #[Route('/tcl', name: 'app_tcl')]
    public function index(
        EntityManagerInterface $entityManager,
    ): Response {
        $now = new DateTime();
        $dayName = $now->format('l');

        $jsonTclStops = file_get_contents('../public/static/json/tcl_stops.json');
        $stopList = json_decode($jsonTclStops, true);

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
	S.STOP_NAME IN ' . $this->formatListToSQL(array_map(fn($value) => $value['name'], $stopList)) . '
	AND ST.STOP_ID = S.STOP_ID
	AND ST.DEPARTURE_TIME > :now
	AND T.TRIP_ID = ST.TRIP_ID
	AND T.ROUTE_ID = R.ROUTE_ID
	AND C.SERVICE_ID = T.SERVICE_ID
	AND C.' . $dayName . '= \'1\'
    AND C.START_DATE <= :nowDate
	AND C.END_DATE >= :nowDate
ORDER BY ST.DEPARTURE_TIME, S.STOP_NAME
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

        foreach ($stopTimes as $stop) {
            $stopTimeEntry = array_find($stopList, fn($value) => $value['name'] == $stop['stop_name']);
            if ($stopTimeEntry['lines'][0] !== '*' && !in_array($stop['route_short_name'], $stopTimeEntry['lines']))
                continue;

            if (count(array_filter(
                $singleRouteName,
                fn($value) => $value['route_long_name'] === $stop['route_long_name'] && $value['stop_name'] === $stop['stop_name']
            )) >= $timesThreshold)
                continue;
            $singleRouteName[] = [
                'route_long_name' => $stop['route_long_name'],
                'stop_name' => $stop['stop_name'],
            ];

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

        $routeStopHashList = $this->sortRoutes($routeStopHashList);

        return $this->render('tcl/index.html.twig', [
            'data' => $processedTimes,
            'route_stop_hash_list' => $routeStopHashList
        ]);
    }

    private function sortRoutes($routes)
    {
        $metroRoutes = [];
        $tramRoutes = [];
        $CBusRoutes = [];
        $others = [];

        foreach ($routes as $route) {
            if (preg_match($this->metroRegex, $route['route_name'])) $metroRoutes[] = $route;
            else if (preg_match($this->tramRegex, $route['route_name'])) $tramRoutes[] = $route;
            else if (preg_match($this->CBusRegex, $route['route_name'])) $CBusRoutes[] = $route;
            else $others[] = $route;
        }

        return array_merge($metroRoutes, $tramRoutes, $CBusRoutes, $others);
    }

    private function formatListToSQL($array)
    {
        return ' (\'' . join('\', \'', $array) . '\')';
    }
}
