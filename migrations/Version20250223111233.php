<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250223111233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier_satisfaction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, apprenti_id INTEGER DEFAULT NULL, atelier_id INTEGER DEFAULT NULL, note INTEGER NOT NULL, CONSTRAINT FK_70FF4D0B2A4EC778 FOREIGN KEY (apprenti_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_70FF4D0B82E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_70FF4D0B2A4EC778 ON atelier_satisfaction (apprenti_id)');
        $this->addSql('CREATE INDEX IDX_70FF4D0B82E2CF35 ON atelier_satisfaction (atelier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE atelier_satisfaction');
    }
}
