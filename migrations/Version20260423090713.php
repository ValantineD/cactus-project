<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260423090713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, icon_filename VARCHAR(255) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE theme_activity (theme_id INT NOT NULL, activity_id INT NOT NULL, INDEX IDX_E4A1B2D59027487 (theme_id), INDEX IDX_E4A1B2D81C06096 (activity_id), PRIMARY KEY (theme_id, activity_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE theme_activity ADD CONSTRAINT FK_E4A1B2D59027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE theme_activity ADD CONSTRAINT FK_E4A1B2D81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE theme_activity DROP FOREIGN KEY FK_E4A1B2D59027487');
        $this->addSql('ALTER TABLE theme_activity DROP FOREIGN KEY FK_E4A1B2D81C06096');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE theme_activity');
    }
}
