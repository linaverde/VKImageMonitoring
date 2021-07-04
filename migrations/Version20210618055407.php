<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210618055407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INT AUTO_INCREMENT NOT NULL, publication_id INT NOT NULL, text LONGTEXT NOT NULL, mood VARCHAR(255) NOT NULL, likes_count INT NOT NULL, user_type VARCHAR(255) NOT NULL, INDEX IDX_9474526C38B217A7 (publication_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE monitoring_record (id INT AUTO_INCREMENT NOT NULL, group_link_id INT NOT NULL, time DATETIME NOT NULL, INDEX IDX_880AB7F82BA4567D (group_link_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE publication (id INT AUTO_INCREMENT NOT NULL, record_id INT NOT NULL, link VARCHAR(255) NOT NULL, likes_count INT NOT NULL, reposts_count INT NOT NULL, INDEX IDX_AF3C67794DFD750C (record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE repost (id INT AUTO_INCREMENT NOT NULL, publication_id INT NOT NULL, link VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, mood VARCHAR(255) DEFAULT NULL, user_type VARCHAR(255) NOT NULL, likes_count INT NOT NULL, INDEX IDX_DD3446C538B217A7 (publication_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subsctiber_info (id INT AUTO_INCREMENT NOT NULL, record_id INT NOT NULL, male_count INT NOT NULL, female_count INT NOT NULL, expected_age_count INT NOT NULL, expected_country_count INT NOT NULL, expected_area_count INT NOT NULL, expected_city_count INT NOT NULL, INDEX IDX_8356E304DFD750C (record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C38B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE monitoring_record ADD CONSTRAINT FK_880AB7F82BA4567D FOREIGN KEY (group_link_id) REFERENCES group_info (id)');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C67794DFD750C FOREIGN KEY (record_id) REFERENCES monitoring_record (id)');
        $this->addSql('ALTER TABLE repost ADD CONSTRAINT FK_DD3446C538B217A7 FOREIGN KEY (publication_id) REFERENCES publication (id)');
        $this->addSql('ALTER TABLE subsctiber_info ADD CONSTRAINT FK_8356E304DFD750C FOREIGN KEY (record_id) REFERENCES monitoring_record (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C67794DFD750C');
        $this->addSql('ALTER TABLE subsctiber_info DROP FOREIGN KEY FK_8356E304DFD750C');
        $this->addSql('ALTER TABLE comment DROP FOREIGN KEY FK_9474526C38B217A7');
        $this->addSql('ALTER TABLE repost DROP FOREIGN KEY FK_DD3446C538B217A7');
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP TABLE monitoring_record');
        $this->addSql('DROP TABLE publication');
        $this->addSql('DROP TABLE repost');
        $this->addSql('DROP TABLE subsctiber_info');
    }
}
