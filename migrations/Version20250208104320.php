<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250208104320 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alimentation DROP FOREIGN KEY FK_8E65DFA05EB747A3');
        $this->addSql('DROP INDEX IDX_8E65DFA05EB747A3 ON alimentation');
        $this->addSql('ALTER TABLE alimentation CHANGE animal_id animal_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE alimentation ADD CONSTRAINT FK_8E65DFA05EB747A3 FOREIGN KEY (animal_id_id) REFERENCES animal (id)');
        $this->addSql('CREATE INDEX IDX_8E65DFA05EB747A3 ON alimentation (animal_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alimentation DROP FOREIGN KEY FK_8E65DFA05EB747A3');
        $this->addSql('DROP INDEX IDX_8E65DFA05EB747A3 ON alimentation');
        $this->addSql('ALTER TABLE alimentation CHANGE animal_id_id animal_id INT NOT NULL');
        $this->addSql('ALTER TABLE alimentation ADD CONSTRAINT FK_8E65DFA05EB747A3 FOREIGN KEY (animal_id) REFERENCES animal (id)');
        $this->addSql('CREATE INDEX IDX_8E65DFA05EB747A3 ON alimentation (animal_id)');
    }
}
