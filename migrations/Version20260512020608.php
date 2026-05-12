<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260512020608 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participation CHANGE registered_at registered_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL, CHANGE user_id user_id INT NOT NULL, CHANGE activity_id activity_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participation CHANGE registered_at registered_at DATE DEFAULT NULL, CHANGE updated_at updated_at DATE DEFAULT NULL, CHANGE user_id user_id INT DEFAULT NULL, CHANGE activity_id activity_id INT DEFAULT NULL');
    }
}
