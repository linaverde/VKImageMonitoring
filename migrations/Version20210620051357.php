<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210620051357 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE group_monitoring_settings (id INT AUTO_INCREMENT NOT NULL, group_info_id INT NOT NULL, days_count INT DEFAULT NULL, posts_count INT DEFAULT NULL, UNIQUE INDEX UNIQ_BCED510DDB8B95F4 (group_info_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_monitoring_settings (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, days_count INT DEFAULT NULL, posts_count INT DEFAULT NULL, UNIQUE INDEX UNIQ_4E0CA49DA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE group_monitoring_settings ADD CONSTRAINT FK_BCED510DDB8B95F4 FOREIGN KEY (group_info_id) REFERENCES group_info (id)');
        $this->addSql('ALTER TABLE user_monitoring_settings ADD CONSTRAINT FK_4E0CA49DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE group_monitoring_settings');
        $this->addSql('DROP TABLE user_monitoring_settings');
    }
}
