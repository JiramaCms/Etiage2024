<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926082827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE productionMonth (mois VARCHAR(255) NOT NULL, quantite INT NOT NULL, id_site INT NOT NULL, PRIMARY KEY(mois, quantite)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL');
       // $this->addSql('ALTER TABLE station ADD site_id INT NOT NULL');
        $this->addSql('ALTER TABLE station ADD CONSTRAINT FK_9F39F8B1F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_9F39F8B1F6BD1646 ON station (site_id)');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        //$this->addSql('DROP TABLE productionMonth');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL COMMENT \'(DC2Type:points)\'');
        $this->addSql('ALTER TABLE station DROP FOREIGN KEY FK_9F39F8B1F6BD1646');
        $this->addSql('DROP INDEX IDX_9F39F8B1F6BD1646 ON station');
        $this->addSql('ALTER TABLE station DROP site_id');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL COMMENT \'(DC2Type:polygons)\'');
    }
}
