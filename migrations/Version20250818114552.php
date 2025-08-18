<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250818114552 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_message ADD answered_by_id INT DEFAULT NULL, ADD read_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD answered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD answer_content LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE contact_message ADD CONSTRAINT FK_2C9211FE2FC55A77 FOREIGN KEY (answered_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_2C9211FE2FC55A77 ON contact_message (answered_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_message DROP FOREIGN KEY FK_2C9211FE2FC55A77');
        $this->addSql('DROP INDEX IDX_2C9211FE2FC55A77 ON contact_message');
        $this->addSql('ALTER TABLE contact_message DROP answered_by_id, DROP read_at, DROP answered_at, DROP answer_content');
    }
}
