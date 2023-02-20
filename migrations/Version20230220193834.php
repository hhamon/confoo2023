<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230220193834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add `agenda_slot` table schema.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE agenda_slot (id VARCHAR(36) NOT NULL, agenda_id VARCHAR(36) NOT NULL, opens_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', closes_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(20) NOT NULL, INDEX IDX_912B8217EA67784A (agenda_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agenda_slot ADD CONSTRAINT FK_912B8217EA67784A FOREIGN KEY (agenda_id) REFERENCES agenda (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE agenda_slot DROP FOREIGN KEY FK_912B8217EA67784A');
        $this->addSql('DROP TABLE agenda_slot');
    }
}
