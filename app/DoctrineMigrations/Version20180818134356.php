<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Migration script to add the required index and fk constraints
 */
class Version20180818134356 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_books ADD CONSTRAINT order_books_ibfk_1 FOREIGN KEY (order_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE order_books ADD CONSTRAINT order_books_ibfk_2 FOREIGN KEY (book_id) REFERENCES books (id)');
        $this->addSql('CREATE INDEX book_id ON order_books (book_id)');
        $this->addSql('CREATE INDEX order_id ON order_books (order_id)');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT orders_ibfk_1 FOREIGN KEY (user_id) REFERENCES users (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('CREATE INDEX user_id ON orders (user_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE order_books DROP FOREIGN KEY order_books_ibfk_1');
        $this->addSql('ALTER TABLE order_books DROP FOREIGN KEY order_books_ibfk_2');
        $this->addSql('DROP INDEX book_id ON order_books');
        $this->addSql('DROP INDEX order_id ON order_books');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY orders_ibfk_1');
        $this->addSql('DROP INDEX user_id ON orders');

    }
}
