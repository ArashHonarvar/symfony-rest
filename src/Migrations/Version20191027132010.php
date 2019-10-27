<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191027132010 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE battle_user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(191) NOT NULL, email VARCHAR(191) NOT NULL, password VARCHAR(80) NOT NULL, UNIQUE INDEX UNIQ_DAF415DFF85E0677 (username), UNIQUE INDEX UNIQ_DAF415DFE7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE programmer');
        $this->addSql('ALTER TABLE battle_api_token ADD CONSTRAINT FK_F97E7085A76ED395 FOREIGN KEY (user_id) REFERENCES battle_user (id)');
        $this->addSql('ALTER TABLE battle_programmer CHANGE user_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE battle_programmer ADD CONSTRAINT FK_EBBE5C73A76ED395 FOREIGN KEY (user_id) REFERENCES battle_user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE battle_api_token DROP FOREIGN KEY FK_F97E7085A76ED395');
        $this->addSql('ALTER TABLE battle_programmer DROP FOREIGN KEY FK_EBBE5C73A76ED395');
        $this->addSql('CREATE TABLE programmer (id INT AUTO_INCREMENT NOT NULL, nick_name VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, avatar_number INT NOT NULL, tag_line VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE battle_user');
        $this->addSql('ALTER TABLE battle_programmer CHANGE user_id user_id INT NOT NULL');
    }
}
