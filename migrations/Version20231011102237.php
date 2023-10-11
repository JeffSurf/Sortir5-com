<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231011102237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant CHANGE telephone telephone VARCHAR(15) DEFAULT NULL');
        $this->addSql('ALTER TABLE sortie CHANGE nom nom VARCHAR(50) NOT NULL, CHANGE duree duree INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participant CHANGE telephone telephone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE sortie CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE duree duree TIME DEFAULT NULL');
    }
}
