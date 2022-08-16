<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220801153641 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE customers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE customers (id INT NOT NULL, nick_name VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE contacts ADD customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contacts ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contacts ALTER birthday TYPE DATE');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_334015739395C3F3 FOREIGN KEY (customer_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE contacts ADD CONSTRAINT FK_334015737E3C61F9 FOREIGN KEY (owner_id) REFERENCES customers (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_334015739395C3F3 ON contacts (customer_id)');
        $this->addSql('CREATE INDEX IDX_334015737E3C61F9 ON contacts (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contacts DROP CONSTRAINT FK_334015739395C3F3');
        $this->addSql('ALTER TABLE contacts DROP CONSTRAINT FK_334015737E3C61F9');
        $this->addSql('DROP SEQUENCE customers_id_seq CASCADE');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP INDEX IDX_334015739395C3F3');
        $this->addSql('DROP INDEX IDX_334015737E3C61F9');
        $this->addSql('ALTER TABLE contacts DROP customer_id');
        $this->addSql('ALTER TABLE contacts DROP owner_id');
        $this->addSql('ALTER TABLE contacts ALTER phone TYPE VARCHAR(50)');
        $this->addSql('ALTER TABLE contacts ALTER address TYPE TEXT');
        $this->addSql('ALTER TABLE contacts ALTER address DROP DEFAULT');
        $this->addSql('ALTER TABLE contacts ALTER email TYPE VARCHAR(150)');
        $this->addSql('ALTER TABLE contacts ALTER birthday TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE contacts ALTER birthday DROP DEFAULT');
        $this->addSql('ALTER TABLE contacts ALTER img_url DROP NOT NULL');
    }
}
