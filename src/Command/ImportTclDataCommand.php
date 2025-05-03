<?php

namespace App\Command;

use App\Entity\Calendar;
use App\Entity\CalendarDates;
use App\Entity\Routes;
use App\Entity\Stop;
use App\Entity\StopTime;
use App\Entity\Trip;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use ZipArchive;

#[AsCommand(
    name: 'app:import-tcl-data',
    description: 'Import data from TCL open-data',
)]
class ImportTclDataCommand extends Command
{
    private ZipArchive $zip;

    private array $files;

    public function __construct(
        private HttpClientInterface $client,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializerInterface
    ) {
        $this->zip = new ZipArchive;
        $this->files = [
            [
                'name' => "stop_times.txt",
                'entity' => StopTime::class,
                'columns' => ['trip_id', 'arrival_time', 'departure_time', 'stop_id', 'stop_sequence', 'pickup_type', 'drop_off_type']
            ],
            [
                'name' => "calendar_dates.txt",
                'entity' => CalendarDates::class,
                'columns' => ['service_id', 'date', 'exception_type']
            ],
            [
                'name' => "calendar.txt",
                'entity' => Calendar::class,
                'columns' => ['service_id', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday', 'start_date', 'end_date']
            ],
            [
                'name' => "routes.txt",
                'entity' => Routes::class,
                'columns' => ['route_id', 'agency_id', 'route_short_name', 'route_long_name', 'route_desc', 'route_type', 'route_color']
            ],
            [
                'name' => "stops.txt",
                'entity' => Stop::class,
                'columns' => ['stop_id', 'stop_name', 'stop_lat', 'stop_lon', 'location_type', 'parent_station', 'wheelchair_boarding']
            ],
            [
                'name' => "trips.txt",
                'entity' => Trip::class,
                'columns' => ['route_id', 'service_id', 'trip_id', 'trip_headsign', 'wheelchair_accessible']
            ],
        ];
        parent::__construct();
    }

    protected function configure(): void {}

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Making request to grandlyon data...');

        $response = $this->client->request(
            'GET',
            $_ENV['TCL_THEORIQUE_URL'],
            [
                'auth_basic' => [$_ENV['GRANDLYON_USERNAME'], $_ENV['GRANDLYON_PASSWORD']],
            ]
        );
        if ($response->getStatusCode(200)) {
            $output->writeln('Request to grandlyon data ok');
        } else {
            $output->writeln('Request to grandlyon data failed.');
            return Command::FAILURE;
        }

        $fileHandler = fopen('./tcl_import.zip', 'w');
        foreach ($this->client->stream($response) as $chunk) {
            fwrite($fileHandler, $chunk->getContent());
        }

        if ($this->zip->open('./tcl_import.zip') === TRUE) {
            $this->zip->extractTo('./tcl_import');
            $this->zip->close();
            $output->writeln('ZIP export ok');
        } else {
            $output->writeln('ZIP export failed');
            return Command::FAILURE;
        }

        foreach ($this->files as $file) {
            $entityClass = $file['entity'];
            $output->writeln("Handling " . $file['name'] . ", entity " . $entityClass . "...");

            $repo = $this->entityManager->getRepository($entityClass);
            $repo->createQueryBuilder('deleter')->delete()->getQuery()->execute();

            $counter = 0;

            foreach ($this->parseCsvGenerator($file['name']) as $row) {
                $attributes = [];
                if ($row && $row != $file['columns']) {
                    foreach ($row as $key => $column) {
                        $attributeName = $file['columns'][$key];
                        if ($attributeName === "arrival_time" || $attributeName === "departure_time") {
                            $column = DateTime::createFromFormat('H:i:s', $column);
                        }
                        $attributes[$file['columns'][$key]] = $column;
                    }
                    $jsonContent = $this->serializerInterface->serialize($attributes, 'json');
                    $entity = $this->serializerInterface->deserialize($jsonContent, $entityClass, 'json');

                    $this->entityManager->persist($entity);
                    $counter++;
                    if ($counter >= 1000) {
                        $counter = 0;
                        $this->entityManager->flush();
                        $this->entityManager->clear();
                    }
                }
            }
            $this->entityManager->flush();
            $this->entityManager->clear();
        }


        return Command::SUCCESS;
    }

    private function parseCsvGenerator($file)
    {
        if (($open = fopen("./tcl_import/" . $file, "r")) !== false) {

            while (($data = fgetcsv($open, 0, ',')) !== false) {
                yield $data; // Yield one row at a time
            }
            fclose($open);
        } else {
            return false;
        }
    }
}
