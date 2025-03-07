<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250307165059 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Migration for creating conference_room and reservation tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE conference_room (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, capacity INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reservation (id SERIAL NOT NULL, conference_room_id INT NOT NULL, date DATE NOT NULL, start_time TIME(0) WITHOUT TIME ZONE NOT NULL, end_time TIME(0) WITHOUT TIME ZONE NOT NULL, reserved_by VARCHAR(255) NOT NULL, PRIMARY KEY(id))');

        $this->addSql('CREATE INDEX IDX_42C84955AFEAEF2B ON reservation (conference_room_id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955AFEAEF2B FOREIGN KEY (conference_room_id) REFERENCES conference_room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // Drop the tables in case of rollback
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE conference_room');
    }
}
