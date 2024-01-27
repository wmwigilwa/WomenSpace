<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240127161952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE reply_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE reply (id INT NOT NULL, owner_id INT DEFAULT NULL, topic_id INT DEFAULT NULL, body TEXT DEFAULT NULL, date_created TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FDA8C6E07E3C61F9 ON reply (owner_id)');
        $this->addSql('CREATE INDEX IDX_FDA8C6E01F55203D ON reply (topic_id)');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E07E3C61F9 FOREIGN KEY (owner_id) REFERENCES tbl_user_accounts (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reply ADD CONSTRAINT FK_FDA8C6E01F55203D FOREIGN KEY (topic_id) REFERENCES topic (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE reply_id_seq CASCADE');
        $this->addSql('ALTER TABLE reply DROP CONSTRAINT FK_FDA8C6E07E3C61F9');
        $this->addSql('ALTER TABLE reply DROP CONSTRAINT FK_FDA8C6E01F55203D');
        $this->addSql('DROP TABLE reply');
    }
}
