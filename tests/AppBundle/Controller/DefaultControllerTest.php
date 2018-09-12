<?php

/**
 * Test case used for functional testing of home page and categories CRUD.
 *
 * @author Azraar Azward <mazraara@gmail.com>
 */

namespace Tests\AppBundle\Controller;

use AppBundle\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    /*
    * Test if home page is correct
    */
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');
        $this->assertEquals(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains('Books List', $crawler->filter('.container h2')->text());
    }

    /*
    * Test if Dashboard is correct
    */
    public function testDashboardPage()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'p@ssword',
        ]);

        $crawler = $client->request('GET', '/dashboard');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
        $this->assertContains('You are logged in!', $crawler->filter('.container-fluid h4')->text());
    }

    /*
    * Test to add new category
    */
    public function testDashboardNewCategory()
    {
        $categoryTitle = 'Fiction Title '.mt_rand();
        $categoryDescription = $this->generateRandomString(16);

        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'p@ssword',
        ]);

        $crawler = $client->request('GET', '/dashboard/category/new');
        $form = $crawler->selectButton('Save')->form([
            'add_category[name]' => $categoryTitle,
            'add_category[description]' => $categoryDescription,
        ]);
        $client->submit($form);

        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());
        $category = $client->getContainer()->get('doctrine')->getRepository(Category::class)->findOneBy([
            'name' => $categoryTitle,
        ]);
        $this->assertNotNull($category);
        $this->assertSame($categoryTitle, $category->getName());
        $this->assertSame($categoryDescription, $category->getDescription());
    }

    /*
    * Test to check edit category page
    */
    public function testDashboardShowEditCategoryPage()
    {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'p@ssword',
        ]);

        $client->request('GET', '/dashboard/category/1/edit');
        $this->assertSame(Response::HTTP_OK, $client->getResponse()->getStatusCode());
    }

    /*
    * Test to edit category
    */
    public function testDashboardEditCategory()
    {
        $categoryTitle = 'New Category Title '.mt_rand();
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'p@ssword',
        ]);

        $crawler = $client->request('GET', '/dashboard/category/1/edit');
        $form = $crawler->selectButton('Save')->form([
            'edit_category[name]' => $categoryTitle,
        ]);
        $client->submit($form);
        $this->assertSame(Response::HTTP_FOUND, $client->getResponse()->getStatusCode());

        $category = $client->getContainer()->get('doctrine')->getRepository(Category::class)->find(1);
        $this->assertSame($categoryTitle, $category->getName());
    }

    /*
    * function to generate random text for given length
    */
    private function generateRandomString(int $length)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return mb_substr(str_shuffle(str_repeat($chars, ceil($length / mb_strlen($chars)))), 1, $length);
    }
}
