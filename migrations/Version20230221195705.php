<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230221195705 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add `appointment` table schema.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE appointment (id VARCHAR(36) NOT NULL, slot_id VARCHAR(36) NOT NULL, guest_name VARCHAR(50) NOT NULL, guest_email VARCHAR(180) NOT NULL, message LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_FE38F84459E5119C (slot_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE appointment ADD CONSTRAINT FK_FE38F84459E5119C FOREIGN KEY (slot_id) REFERENCES agenda_slot (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE appointment DROP FOREIGN KEY FK_FE38F84459E5119C');
        $this->addSql('DROP TABLE appointment');
    }
}
