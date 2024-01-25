<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240125101531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cfg_space ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE cfg_space ALTER description TYPE TEXT');
        $this->addSql('ALTER TABLE cfg_space ALTER description DROP NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE cfg_space DROP title');
        $this->addSql('ALTER TABLE cfg_space ALTER description TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE cfg_space ALTER description SET NOT NULL');
    }
}
