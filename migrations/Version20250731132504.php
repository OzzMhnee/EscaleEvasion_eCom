<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250731132504 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD couchages INT NOT NULL, ADD departement VARCHAR(255) NOT NULL, ADD start_date DATETIME NOT NULL, ADD end_date DATETIME NOT NULL, ADD image_url LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', ADD city VARCHAR(255) NOT NULL, ADD surface INT NOT NULL, ADD is_available TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP couchages, DROP departement, DROP start_date, DROP end_date, DROP image_url, DROP city, DROP surface, DROP is_available');
    }
}
