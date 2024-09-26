<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240926074045 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production DROP FOREIGN KEY FK_D3EDB1E0F6BD1646');
        $this->addSql('DROP INDEX IDX_D3EDB1E0F6BD1646 ON production');
        $this->addSql('ALTER TABLE production DROP site_id');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL');
        //$this->addSql('ALTER TABLE site_source ADD CONSTRAINT FK_2F24D12CF6BD1646 FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE');
        //$this->addSql('ALTER TABLE site_source ADD CONSTRAINT FK_2F24D12C953C1C61 FOREIGN KEY (source_id) REFERENCES source (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE production ADD site_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE production ADD CONSTRAINT FK_D3EDB1E0F6BD1646 FOREIGN KEY (site_id) REFERENCES site (id)');
        $this->addSql('CREATE INDEX IDX_D3EDB1E0F6BD1646 ON production (site_id)');
        $this->addSql('ALTER TABLE site CHANGE coord coord POINT NOT NULL COMMENT \'(DC2Type:points)\'');
        $this->addSql('ALTER TABLE site_source DROP FOREIGN KEY FK_2F24D12CF6BD1646');
        $this->addSql('ALTER TABLE site_source DROP FOREIGN KEY FK_2F24D12C953C1C61');
        $this->addSql('ALTER TABLE zone CHANGE coord coord POLYGON NOT NULL COMMENT \'(DC2Type:polygons)\'');
    }
}
