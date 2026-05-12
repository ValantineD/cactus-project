<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260512002053 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE participation (id INT AUTO_INCREMENT NOT NULL, status VARCHAR(255) DEFAULT NULL, registered_at DATE DEFAULT NULL, updated_at DATE DEFAULT NULL, user_id INT DEFAULT NULL, activity_id INT DEFAULT NULL, INDEX IDX_AB55E24FA76ED395 (user_id), INDEX IDX_AB55E24F81C06096 (activity_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24FA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE participation ADD CONSTRAINT FK_AB55E24F81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24FA76ED395');
        $this->addSql('ALTER TABLE participation DROP FOREIGN KEY FK_AB55E24F81C06096');
        $this->addSql('DROP TABLE participation');
    }
}
