<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210518113155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE uploaded_log_file (id INT AUTO_INCREMENT NOT NULL, created_by_id INT NOT NULL, hash VARCHAR(65) NOT NULL, INDEX IDX_F97F4F4B03A8386 (created_by_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE uploaded_log_file ADD CONSTRAINT FK_F97F4F4B03A8386 FOREIGN KEY (created_by_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE uploaded_log_files');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE uploaded_log_files (id INT AUTO_INCREMENT NOT NULL, hash VARCHAR(65) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE uploaded_log_file');
    }
}
