<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260506140935 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, tags JSON DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, date_start DATE DEFAULT NULL, date_end DATE DEFAULT NULL, spot INT DEFAULT NULL, created_at DATE DEFAULT NULL, updated_at DATETIME DEFAULT NULL, status VARCHAR(255) DEFAULT NULL, state VARCHAR(255) DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_AC74095AA76ED395 (user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE image_file (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) DEFAULT NULL, position INT DEFAULT NULL, activity_id INT DEFAULT NULL, INDEX IDX_7EA5DC8E81C06096 (activity_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE theme (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, icon_filename VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE theme_activity (theme_id INT NOT NULL, activity_id INT NOT NULL, INDEX IDX_E4A1B2D59027487 (theme_id), INDEX IDX_E4A1B2D81C06096 (activity_id), PRIMARY KEY (theme_id, activity_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, profile_filename VARCHAR(255) DEFAULT NULL, username VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, password VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, location VARCHAR(255) DEFAULT NULL, birthday DATETIME DEFAULT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE image_file ADD CONSTRAINT FK_7EA5DC8E81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE theme_activity ADD CONSTRAINT FK_E4A1B2D59027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE theme_activity ADD CONSTRAINT FK_E4A1B2D81C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AA76ED395');
        $this->addSql('ALTER TABLE image_file DROP FOREIGN KEY FK_7EA5DC8E81C06096');
        $this->addSql('ALTER TABLE theme_activity DROP FOREIGN KEY FK_E4A1B2D59027487');
        $this->addSql('ALTER TABLE theme_activity DROP FOREIGN KEY FK_E4A1B2D81C06096');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE image_file');
        $this->addSql('DROP TABLE theme');
        $this->addSql('DROP TABLE theme_activity');
        $this->addSql('DROP TABLE `user`');
    }
}
