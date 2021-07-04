<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210622005731 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_info ADD admin_token VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE monitoring_record ADD CONSTRAINT FK_880AB7F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_880AB7F8A76ED395 ON monitoring_record (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE group_info DROP admin_token');
        $this->addSql('ALTER TABLE monitoring_record DROP FOREIGN KEY FK_880AB7F8A76ED395');
        $this->addSql('DROP INDEX IDX_880AB7F8A76ED395 ON monitoring_record');
    }
}
