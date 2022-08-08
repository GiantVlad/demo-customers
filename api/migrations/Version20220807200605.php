<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220807200605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER INDEX uniq_334015736db2eb0 RENAME TO UNIQ_3340157393CB796C');
        $this->addSql('ALTER TABLE customers ALTER nick_name TYPE VARCHAR(100)');
        $this->addSql('ALTER TABLE images ADD contact_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AE7A1254A FOREIGN KEY (contact_id) REFERENCES contacts (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E01FBE6AE7A1254A ON images (contact_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customers ALTER nick_name TYPE VARCHAR(255)');
        $this->addSql('ALTER INDEX uniq_3340157393cb796c RENAME TO uniq_334015736db2eb0');
        $this->addSql('ALTER TABLE images DROP CONSTRAINT FK_E01FBE6AE7A1254A');
        $this->addSql('DROP INDEX UNIQ_E01FBE6AE7A1254A');
        $this->addSql('ALTER TABLE images DROP contact_id');
    }
}
