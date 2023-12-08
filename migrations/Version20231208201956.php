<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231208201956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE produits_tailles (produits_id INT NOT NULL, tailles_id INT NOT NULL, INDEX IDX_AE885C7CD11A2CF (produits_id), INDEX IDX_AE885C71AEC613E (tailles_id), PRIMARY KEY(produits_id, tailles_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE produits_tailles ADD CONSTRAINT FK_AE885C7CD11A2CF FOREIGN KEY (produits_id) REFERENCES produits (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE produits_tailles ADD CONSTRAINT FK_AE885C71AEC613E FOREIGN KEY (tailles_id) REFERENCES tailles (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE produits_tailles DROP FOREIGN KEY FK_AE885C7CD11A2CF');
        $this->addSql('ALTER TABLE produits_tailles DROP FOREIGN KEY FK_AE885C71AEC613E');
        $this->addSql('DROP TABLE produits_tailles');
    }
}
