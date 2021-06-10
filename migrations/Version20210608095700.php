<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210608095700 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pressure_record (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, tire_id INT DEFAULT NULL, driver_id INT DEFAULT NULL, circuit_id INT DEFAULT NULL, datetime DATETIME NOT NULL, temp_ground SMALLINT NOT NULL, temp_front_left SMALLINT NOT NULL, temp_front_right SMALLINT NOT NULL, temp_rear_left SMALLINT NOT NULL, temp_rear_right SMALLINT NOT NULL, press_front_left DOUBLE PRECISION NOT NULL, press_front_right DOUBLE PRECISION NOT NULL, press_rear_left DOUBLE PRECISION NOT NULL, press_rear_right DOUBLE PRECISION NOT NULL, INDEX IDX_54130C24A76ED395 (user_id), INDEX IDX_54130C24C37A925C (tire_id), INDEX IDX_54130C24C3423909 (driver_id), INDEX IDX_54130C24CF2182C8 (circuit_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pressure_record ADD CONSTRAINT FK_54130C24A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE pressure_record ADD CONSTRAINT FK_54130C24C37A925C FOREIGN KEY (tire_id) REFERENCES tire (id)');
        $this->addSql('ALTER TABLE pressure_record ADD CONSTRAINT FK_54130C24C3423909 FOREIGN KEY (driver_id) REFERENCES driver (id)');
        $this->addSql('ALTER TABLE pressure_record ADD CONSTRAINT FK_54130C24CF2182C8 FOREIGN KEY (circuit_id) REFERENCES circuit (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE pressure_record');
    }
}
