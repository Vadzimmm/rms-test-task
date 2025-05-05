<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250501102250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE log_entry (id BINARY(16) NOT NULL COMMENT '(DC2Type:uuid)', service_name VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', request_line VARCHAR(2048) NOT NULL, status_code INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql('CREATE INDEX idx_service_name ON log_entry (service_name)');
        $this->addSql('CREATE INDEX idx_timestamp ON log_entry (timestamp)');
        $this->addSql('CREATE INDEX idx_status_code ON log_entry (status_code)');
        $this->addSql('CREATE INDEX idx_service_status ON log_entry (service_name, status_code)');
        $this->addSql('CREATE INDEX idx_service_date ON log_entry (service_name, timestamp)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE log_entry
        SQL);
    }
}
