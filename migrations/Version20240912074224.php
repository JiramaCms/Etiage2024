<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240912074224 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        //$this->addSql('CREATE TABLE site_source (site_id INT NOT NULL, source_id INT NOT NULL, INDEX IDX_2F24D12CF6BD1646 (site_id), INDEX IDX_2F24D12C953C1C61 (source_id), PRIMARY KEY(site_id, source_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        //$this->addSql('CREATE TABLE source (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE site_source ADD CONSTRAINT FK_2F24D12CF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE site_source ADD CONSTRAINT FK_2F24D12C953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site_source DROP FOREIGN KEY FK_2F24D12CF6BD1646');
        $this->addSql('ALTER TABLE site_source DROP FOREIGN KEY FK_2F24D12C953C1C61');
        $this->addSql('DROP TABLE site_source');
        $this->addSql('DROP TABLE source');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL COMMENT \'(DC2Type:points)\'');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL COMMENT \'(DC2Type:polygons)\'');
    }
}
