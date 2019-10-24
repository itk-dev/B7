<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20191021073035 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey CHANGE follow_up_text1 follow_up_text1 VARCHAR(50) NOT NULL, CHANGE follow_up_text2 follow_up_text2 VARCHAR(50) NOT NULL, CHANGE follow_up_text3 follow_up_text3 VARCHAR(50) NOT NULL, CHANGE follow_up_text4 follow_up_text4 VARCHAR(50) DEFAULT NULL, CHANGE follow_up_text5 follow_up_text5 VARCHAR(50) DEFAULT NULL');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE survey CHANGE follow_up_text1 follow_up_text1 VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE follow_up_text2 follow_up_text2 VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE follow_up_text3 follow_up_text3 VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE follow_up_text4 follow_up_text4 VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE follow_up_text5 follow_up_text5 VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci');
    }
}
