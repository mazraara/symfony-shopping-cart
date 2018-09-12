<?php
namespace AppBundle\DataFixtures;

use AppBundle\Entity\Book;
use AppBundle\Entity\Category;
use AppBundle\Entity\Orders;
use AppBundle\Entity\OrderBook;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class AppFixtures extends Fixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }


    public function load(ObjectManager $manager)
    {

        $userManager = $this->container->get('fos_user.user_manager');

        // Create a new user
        $user = $userManager->createUser();
        $user->setUsername('admin');
        $user->setEmail('azraar@gmail.com');
        $user->setPlainPassword('p@ssword');
        $user->setEnabled(true);
        $manager->persist($user);
        $manager->flush();

        // Create 5 categories with books, orders, order book details
        for ($i = 0; $i < 5; $i++) {

            $category = new Category();
            $category->setName('category '.$i);
            $category->setDescription('category desc '.$i);
            $manager->persist($category);
            $manager->flush();

            $book = new Book();
            $book->setName('book '.$i);
            $book->setPrice(rand(1,100));
            $book->setAuthor('book author '.$i);
            // relate this product to the category
            $book->setCategory($category);
            $manager->persist($book);
            $manager->flush();

            $order = new Orders();
            $order->setUserId($user->getId());
            $order->setTotal(rand(1,100));
            $order->setCreatedAt(new \DateTime('now'));
            $manager->persist($order);
            $manager->flush();

            $orderBook = new OrderBook();
            $orderBook->setTotal(rand(1,100));
            $orderBook->setBookId($book->getId());
            $orderBook->setOrderId($order->getId());
            $orderBook->setQty(rand(1,10));
            $manager->persist($orderBook);
            $manager->flush();

        }

    }
}