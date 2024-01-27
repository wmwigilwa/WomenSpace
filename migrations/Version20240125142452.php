<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240125142452 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE topic_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE topic (id INT NOT NULL, owner_id INT DEFAULT NULL, space_id INT DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, body TEXT DEFAULT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9D40DE1B7E3C61F9 ON topic (owner_id)');
        $this->addSql('CREATE INDEX IDX_9D40DE1B23575340 ON topic (space_id)');
        $this->addSql('CREATE TABLE topic_tag (topic_id INT NOT NULL, tag_id INT NOT NULL, PRIMARY KEY(topic_id, tag_id))');
        $this->addSql('CREATE INDEX IDX_302AC6211F55203D ON topic_tag (topic_id)');
        $this->addSql('CREATE INDEX IDX_302AC621BAD26311 ON topic_tag (tag_id)');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B7E3C61F9 FOREIGN KEY (owner_id) REFERENCES tbl_user_accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE topic ADD CONSTRAINT FK_9D40DE1B23575340 FOREIGN KEY (space_id) REFERENCES cfg_space (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE topic_tag ADD CONSTRAINT FK_302AC6211F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE topic_tag ADD CONSTRAINT FK_302AC621BAD26311 FOREIGN KEY (tag_id) REFERENCES cfg_tags (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE topic_id_seq CASCADE');
        $this->addSql('ALTER TABLE topic DROP CONSTRAINT FK_9D40DE1B7E3C61F9');
        $this->addSql('ALTER TABLE topic DROP CONSTRAINT FK_9D40DE1B23575340');
        $this->addSql('ALTER TABLE topic_tag DROP CONSTRAINT FK_302AC6211F55203D');
        $this->addSql('ALTER TABLE topic_tag DROP CONSTRAINT FK_302AC621BAD26311');
        $this->addSql('DROP TABLE topic');
        $this->addSql('DROP TABLE topic_tag');
    }
}
