<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250503220249 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE calendar (id SERIAL NOT NULL, service_id VARCHAR(255) DEFAULT NULL, monday VARCHAR(255) DEFAULT NULL, start_date DATE DEFAULT NULL, tuesday VARCHAR(255) DEFAULT NULL, wednesday VARCHAR(255) DEFAULT NULL, thursday VARCHAR(255) DEFAULT NULL, friday VARCHAR(255) DEFAULT NULL, saturday VARCHAR(255) DEFAULT NULL, sunday VARCHAR(255) DEFAULT NULL, end_date DATE DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE calendar_dates (id SERIAL NOT NULL, service_id VARCHAR(255) DEFAULT NULL, date DATE DEFAULT NULL, exception_type VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE routes (id SERIAL NOT NULL, route_id VARCHAR(255) DEFAULT NULL, agency_id VARCHAR(255) DEFAULT NULL, route_short_name TEXT DEFAULT NULL, route_long_name TEXT DEFAULT NULL, route_desc VARCHAR(255) DEFAULT NULL, route_type VARCHAR(255) DEFAULT NULL, route_color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE stop (id SERIAL NOT NULL, stop_id VARCHAR(255) NOT NULL, stop_name TEXT NOT NULL, stop_lat VARCHAR(255) NOT NULL, stop_lon VARCHAR(255) NOT NULL, location_type VARCHAR(255) NOT NULL, parent_station VARCHAR(255) NOT NULL, wheelchair_boarding VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE stop_time (id SERIAL NOT NULL, arrival_time TIME(0) WITHOUT TIME ZONE NOT NULL, departure_time TIME(0) WITHOUT TIME ZONE NOT NULL, stop_sequence VARCHAR(255) NOT NULL, pickup_type VARCHAR(255) NOT NULL, drop_off_type VARCHAR(255) NOT NULL, trip_id VARCHAR(255) NOT NULL, stop_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE trip (id SERIAL NOT NULL, service_id VARCHAR(255) NOT NULL, trip_id VARCHAR(255) NOT NULL, trip_headsign TEXT NOT NULL, wheelchair_accessible VARCHAR(255) NOT NULL, route_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE calendar
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE calendar_dates
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE routes
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE stop
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE stop_time
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE trip
        SQL);
    }
}
