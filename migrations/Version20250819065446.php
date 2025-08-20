<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250819065446 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation ADD cancelled_by_id INT DEFAULT NULL, ADD confirmed_by_id INT DEFAULT NULL, ADD cancelled_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD confirmed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C84955187B2D12 FOREIGN KEY (cancelled_by_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849556F45385D FOREIGN KEY (confirmed_by_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_42C84955187B2D12 ON reservation (cancelled_by_id)');
        $this->addSql('CREATE INDEX IDX_42C849556F45385D ON reservation (confirmed_by_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C84955187B2D12');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C849556F45385D');
        $this->addSql('DROP INDEX IDX_42C84955187B2D12 ON reservation');
        $this->addSql('DROP INDEX IDX_42C849556F45385D ON reservation');
        $this->addSql('ALTER TABLE reservation DROP cancelled_by_id, DROP confirmed_by_id, DROP cancelled_at, DROP confirmed_at');
    }
}
