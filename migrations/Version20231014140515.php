<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231014140515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE historique (id INT AUTO_INCREMENT NOT NULL, nom_sortie VARCHAR(50) NOT NULL, date_heure_debut DATETIME NOT NULL, date_limite_inscription DATETIME NOT NULL, infos_sortie LONGTEXT DEFAULT NULL, etat VARCHAR(255) NOT NULL, nom_lieu VARCHAR(50) NOT NULL, adresse VARCHAR(255) DEFAULT NULL, nom_ville VARCHAR(50) NOT NULL, code_postal VARCHAR(5) NOT NULL, motif_annulation VARCHAR(255) DEFAULT NULL, nom_organisateur VARCHAR(50) NOT NULL, prenom_organisateur VARCHAR(50) NOT NULL, nom_site_organisateur VARCHAR(255) NOT NULL, telephone_organisateur VARCHAR(10) DEFAULT NULL, nom_participant VARCHAR(50) NOT NULL, prenom_participant VARCHAR(50) NOT NULL, nom_site_participant VARCHAR(255) NOT NULL, telephone_participant VARCHAR(10) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE historique');
    }
}
