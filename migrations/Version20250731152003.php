<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250731152003 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product ADD image2 VARCHAR(255) DEFAULT NULL, ADD image3 VARCHAR(255) DEFAULT NULL, ADD image4 VARCHAR(255) DEFAULT NULL, ADD image5 VARCHAR(255) DEFAULT NULL, ADD is_swimming_pool TINYINT(1) NOT NULL, ADD is_bath TINYINT(1) NOT NULL, ADD is_clim TINYINT(1) NOT NULL, ADD is_lave_linge TINYINT(1) NOT NULL, ADD is_seche_linge TINYINT(1) NOT NULL, ADD is_lave_vaisselle TINYINT(1) NOT NULL, ADD is_chauffage TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE product DROP image2, DROP image3, DROP image4, DROP image5, DROP is_swimming_pool, DROP is_bath, DROP is_clim, DROP is_lave_linge, DROP is_seche_linge, DROP is_lave_vaisselle, DROP is_chauffage');
    }
}
