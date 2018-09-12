<?php

/**
 * Test case used for Book entity.
 *
 * @author Azraar Azward <mazraara@gmail.com>
 */

namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Book;
use AppBundle\Entity\Category;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
    /**
     * To check the Book model if it has default categories
     */
    public function testItHasNoCategoriesByDefault()
    {
        $book = new Book();
        $this->assertEmpty($book->getCategory());
    }

    /**
     * Checking if it can add categories
     */
    public function testItAddsCategories()
    {
        $book = new Book();
        $book->setCategory([new Category(), new Category()]);
        $this->assertCount(2, $book->getCategory());
    }

}