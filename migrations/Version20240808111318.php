<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240808111318 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE objectif (id INT AUTO_INCREMENT NOT NULL, site_id INT NOT NULL, libelle VARCHAR(255) NOT NULL, description VARCHAR(900) DEFAULT NULL, budget INT DEFAULT NULL, deadline DATE DEFAULT NULL, estimation_cible INT DEFAULT NULL, resultat INT DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, INDEX IDX_E2F86851F6BD1646 (site_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE objectif ADD CONSTRAINT FK_E2F86851F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('ALTER TABLE action ADD objectif_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92157D1AD4 FOREIGN KEY (objectif_id) REFERENCES objectif (id)');
        $this->addSql('CREATE INDEX IDX_47CC8C92157D1AD4 ON action (objectif_id)');
        $this->addSql('ALTER TABLE materiel ADD action_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE materiel ADD CONSTRAINT FK_18D2B0919D32F035 FOREIGN KEY (action_id) REFERENCES action (id)');
        $this->addSql('CREATE INDEX IDX_18D2B0919D32F035 ON materiel (action_id)');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C92157D1AD4');
        $this->addSql('ALTER TABLE objectif DROP FOREIGN KEY FK_E2F86851F6BD1646');
        $this->addSql('DROP TABLE objectif');
        $this->addSql('DROP INDEX IDX_47CC8C92157D1AD4 ON action');
        $this->addSql('ALTER TABLE action DROP objectif_id');
        $this->addSql('ALTER TABLE materiel DROP FOREIGN KEY FK_18D2B0919D32F035');
        $this->addSql('DROP INDEX IDX_18D2B0919D32F035 ON materiel');
        $this->addSql('ALTER TABLE materiel DROP action_id');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL COMMENT \'(DC2Type:points)\'');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL COMMENT \'(DC2Type:polygons)\'');
    }
}
