<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250326161438 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE atelier (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id INTEGER NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, CONSTRAINT FK_E1BB1823A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_E1BB1823A76ED395 ON atelier (user_id)');
        $this->addSql('CREATE TABLE atelier_user (atelier_id INTEGER NOT NULL, user_id INTEGER NOT NULL, PRIMARY KEY(atelier_id, user_id), CONSTRAINT FK_4D145FAA82E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_4D145FAAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_4D145FAA82E2CF35 ON atelier_user (atelier_id)');
        $this->addSql('CREATE INDEX IDX_4D145FAAA76ED395 ON atelier_user (user_id)');
        $this->addSql('CREATE TABLE atelier_satisfaction (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, apprenti_id INTEGER DEFAULT NULL, atelier_id INTEGER DEFAULT NULL, note INTEGER NOT NULL, CONSTRAINT FK_70FF4D0B2A4EC778 FOREIGN KEY (apprenti_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_70FF4D0B82E2CF35 FOREIGN KEY (atelier_id) REFERENCES atelier (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_70FF4D0B2A4EC778 ON atelier_satisfaction (apprenti_id)');
        $this->addSql('CREATE INDEX IDX_70FF4D0B82E2CF35 ON atelier_satisfaction (atelier_id)');
        $this->addSql('CREATE TABLE user (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USERNAME ON user (username)');
        $this->addSql('CREATE TABLE messenger_messages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, body CLOB NOT NULL, headers CLOB NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , available_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        , delivered_at DATETIME DEFAULT NULL --(DC2Type:datetime_immutable)
        )');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE atelier');
        $this->addSql('DROP TABLE atelier_user');
        $this->addSql('DROP TABLE atelier_satisfaction');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
