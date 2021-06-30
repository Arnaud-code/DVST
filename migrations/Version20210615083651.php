<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210615083651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pressure_record ADD note VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE pressure_record RENAME INDEX idx_54130c24c37a925c TO IDX_54130C24BC5ADD68');
        $this->addSql('ALTER TABLE tire RENAME INDEX idx_5ab45d5ea76ed395 TO IDX_A2CE96DBA76ED395');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pressure_record DROP note');
        $this->addSql('ALTER TABLE pressure_record RENAME INDEX idx_54130c24bc5add68 TO IDX_54130C24C37A925C');
        $this->addSql('ALTER TABLE tire RENAME INDEX idx_a2ce96dba76ed395 TO IDX_5AB45D5EA76ED395');
    }
}
