<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231010155810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie ADD motif_annulation VARCHAR(255) DEFAULT NULL, ADD photo_sortie VARCHAR(255) DEFAULT NULL, CHANGE duree duree TIME DEFAULT NULL, CHANGE infos_sortie infos_sortie LONGTEXT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sortie DROP motif_annulation, DROP photo_sortie, CHANGE duree duree TIME NOT NULL, CHANGE infos_sortie infos_sortie LONGTEXT NOT NULL');
    }
}
