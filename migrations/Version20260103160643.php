<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260103160643 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE is_active is_active TINYINT(1) DEFAULT 1 NOT NULL, CHANGE is_verified is_verified TINYINT(1) DEFAULT 0 NOT NULL, CHANGE last_login_at last_login_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE failed_login_count failed_login_count INT DEFAULT 0 NOT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE is_verified is_verified TINYINT(1) NOT NULL, CHANGE last_login_at last_login_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE failed_login_count failed_login_count INT NOT NULL, CHANGE deleted_at deleted_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
