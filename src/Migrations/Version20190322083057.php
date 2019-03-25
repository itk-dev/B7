<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190322083057 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE survey ADD follow_up_text1 VARCHAR(255) NOT NULL, ADD follow_up_text2 VARCHAR(255) NOT NULL, ADD follow_up_text3 VARCHAR(255) NOT NULL, ADD follow_up_text4 VARCHAR(255) DEFAULT NULL, ADD follow_up_text5 VARCHAR(255) DEFAULT NULL, DROP follow_up_text_1, DROP follow_up_text_2, DROP follow_up_text_3, DROP follow_up_text_4, DROP follow_up_text_5');
        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT NULL, CHANGE last_login last_login DATETIME DEFAULT NULL, CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT NULL, CHANGE password_requested_at password_requested_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('mysql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE fos_user CHANGE salt salt VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE last_login last_login DATETIME DEFAULT \'NULL\', CHANGE confirmation_token confirmation_token VARCHAR(180) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE password_requested_at password_requested_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE survey ADD follow_up_text_1 VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD follow_up_text_2 VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD follow_up_text_3 VARCHAR(255) NOT NULL COLLATE utf8mb4_unicode_ci, ADD follow_up_text_4 VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, ADD follow_up_text_5 VARCHAR(255) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, DROP follow_up_text1, DROP follow_up_text2, DROP follow_up_text3, DROP follow_up_text4, DROP follow_up_text5');
    }
}
