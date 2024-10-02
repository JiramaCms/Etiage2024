<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240930055700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE productionMonth (mois VARCHAR(255) NOT NULL, quantite INT NOT NULL, id_site INT NOT NULL, PRIMARY KEY(mois, quantite)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        //$this->addSql('CREATE TABLE siteproduction (site_id INT NOT NULL, date_production VARCHAR(255) NOT NULL, somme_production DOUBLE PRECISION NOT NULL, PRIMARY KEY(site_id, date_production, somme_production)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE source_station (source_id INT NOT NULL, station_id INT NOT NULL, INDEX IDX_D6E640D9953C1C61 (source_id), INDEX IDX_D6E640D921BDB235 (station_id), PRIMARY KEY(source_id, station_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
       // $this->addSql('CREATE TABLE stationProductionMonth (mois VARCHAR(255) NOT NULL, quantite INT NOT NULL, id_station INT NOT NULL, PRIMARY KEY(mois, quantite)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE source_station ADD CONSTRAINT FK_D6E640D9953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE source_station ADD CONSTRAINT FK_D6E640D921BDB235 FOREIGN KEY (station_id) REFERENCES station (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE source_station DROP FOREIGN KEY FK_D6E640D9953C1C61');
        $this->addSql('ALTER TABLE source_station DROP FOREIGN KEY FK_D6E640D921BDB235');
       // $this->addSql('DROP TABLE productionMonth');
        //$this->addSql('DROP TABLE siteproduction');
        $this->addSql('DROP TABLE source_station');
        //$this->addSql('DROP TABLE stationProductionMonth');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL COMMENT \'(DC2Type:points)\'');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL COMMENT \'(DC2Type:polygons)\'');
    }
}
