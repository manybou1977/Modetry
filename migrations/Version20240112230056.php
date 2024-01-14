<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240112230056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat ADD sender_id INT DEFAULT NULL, ADD receiver_id INT DEFAULT NULL, ADD content VARCHAR(255) NOT NULL, ADD timestamp DATETIME NOT NULL');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AAF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE chat ADD CONSTRAINT FK_659DF2AACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_659DF2AAF624B39D ON chat (sender_id)');
        $this->addSql('CREATE INDEX IDX_659DF2AACD53EDB6 ON chat (receiver_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AAF624B39D');
        $this->addSql('ALTER TABLE chat DROP FOREIGN KEY FK_659DF2AACD53EDB6');
        $this->addSql('DROP INDEX IDX_659DF2AAF624B39D ON chat');
        $this->addSql('DROP INDEX IDX_659DF2AACD53EDB6 ON chat');
        $this->addSql('ALTER TABLE chat DROP sender_id, DROP receiver_id, DROP content, DROP timestamp');
    }
}
