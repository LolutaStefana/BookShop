<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230912133545 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organisation ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE organisation ADD CONSTRAINT FK_E6E132B47E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_E6E132B47E3C61F9 ON organisation (owner_id)');
        $this->addSql('ALTER TABLE review ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C67E3C61F9 FOREIGN KEY (owner_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_794381C67E3C61F9 ON review (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organisation DROP CONSTRAINT FK_E6E132B47E3C61F9');
        $this->addSql('DROP INDEX IDX_E6E132B47E3C61F9');
        $this->addSql('ALTER TABLE organisation DROP owner_id');
        $this->addSql('ALTER TABLE review DROP CONSTRAINT FK_794381C67E3C61F9');
        $this->addSql('DROP INDEX IDX_794381C67E3C61F9');
        $this->addSql('ALTER TABLE review DROP owner_id');
    }
}
