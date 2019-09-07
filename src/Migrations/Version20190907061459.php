<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190907061459 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE transaction CHANGE adresse_exp adresse_exp VARCHAR(255) DEFAULT NULL, CHANGE typepiece_exp typepiece_exp VARCHAR(255) DEFAULT NULL, CHANGE adresse_ben adresse_ben VARCHAR(255) DEFAULT NULL, CHANGE numeropiece_ben numeropiece_ben BIGINT DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_32FFA373C678AEBE ON partenaire (ninea)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_32FFA373C678AEBE ON partenaire');
        $this->addSql('ALTER TABLE transaction CHANGE adresse_exp adresse_exp VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE typepiece_exp typepiece_exp VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE adresse_ben adresse_ben VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE numeropiece_ben numeropiece_ben BIGINT NOT NULL');
    }
}
