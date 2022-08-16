<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220806151432 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE images_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE images (id INT NOT NULL, file_name VARCHAR(255) DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE contacts ADD file_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contacts ALTER owner_id SET NOT NULL');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_334015736DB2EB0 FOREIGN KEY (file_id) REFERENCES images (id) ON DELETE SET NULL NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_334015736DB2EB0 ON contacts (file_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62534E21A045A5E9 ON customers (nick_name)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_62534E21E7927C74 ON customers (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contacts DROP CONSTRAINT FK_334015736DB2EB0');
        $this->addSql('DROP SEQUENCE images_id_seq CASCADE');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP INDEX UNIQ_62534E21A045A5E9');
        $this->addSql('DROP INDEX UNIQ_62534E21E7927C74');
        $this->addSql('DROP INDEX UNIQ_334015736DB2EB0');
        $this->addSql('ALTER TABLE contacts DROP file_id');
        $this->addSql('ALTER TABLE contacts ALTER owner_id DROP NOT NULL');
    }
}
