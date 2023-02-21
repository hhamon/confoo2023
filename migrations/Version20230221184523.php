<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20230221184523 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add relationship between users and agendas.';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE agenda ADD owner_id VARCHAR(36) NOT NULL');
        $this->addSql('ALTER TABLE agenda ADD CONSTRAINT FK_2CEDC8777E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2CEDC8777E3C61F9 ON agenda (owner_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE agenda DROP FOREIGN KEY FK_2CEDC8777E3C61F9');
        $this->addSql('DROP INDEX IDX_2CEDC8777E3C61F9 ON agenda');
        $this->addSql('ALTER TABLE agenda DROP owner_id');
    }
}
