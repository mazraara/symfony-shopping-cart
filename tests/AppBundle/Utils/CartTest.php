<?php
/**
 * Test case used for unit testing of shopping cart functionality.
 *
 * @author Azraar Azward <mazraara@gmail.com>
 */

namespace Tests\AppBundle\Utils;

use AppBundle\Utils\Cart;
use AppBundle\Utils\CartItem;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use PHPUnit\Framework\TestCase;

class CartTest extends TestCase
{
    protected $session;

    protected $cart;

    /**
     * To suppress headers already sent issue
     */
    function setUp()
    {
        @session_start();
        parent::setUp();
    }

    /**
     * The constructor
     */
    public function __construct()
    {
        $storage = new NativeSessionStorage();
        $attributes = new NamespacedAttributeBag();
        $this->session = new Session($storage, $attributes);
        $this->cart = new Cart($this->session);
    }

    /**
     *  Cart add function
     */
    public function testAdd()
    {
        // test data to mimic product catalog
        $books[1] = ['id' => '1', 'name' => 'Test book one', 'price' => '10.00'];
        $books[2] = ['id' => '2', 'name' => 'Test book two', 'price' => '100.00'];
        $books[3] = ['id' => '3', 'name' => 'Test book three', 'price' => '50.57'];
        $books[4] = ['id' => '4', 'name' => 'Test book four', 'price' => '0.25'];
        $books[5] = ['id' => '5', 'name' => 'Test book five', 'price' => '756.00'];

        $id = 1;
        $item = new CartItem($books[$id]);
        $item->setQuantity(1); // defaults to 1
        $item->setCategoryId(Cart::CATEGORY_CHILD_BOOK); // add child category the book to Cart
        $this->cart->addItem($item);

        // assert that item added to the cart
        $this->assertEquals(1, count($this->cart));
    }

    /**
     *  Cart get total function
     */
    public function testGetTotal()
    {
        $total = $this->cart->getTotal();

        $this->assertGreaterThan(9, $total);
        $this->assertEquals(10.00, $total, '', 0.2);
    }

    /**
     *  Cart get discount function for child books more than 5
     */
    public function testBookDiscount()
    {
        $total = $this->cart->getTotal(); // total is 10

        // applying 5% discount
        $discountPrice = $this->cart->getDiscountPrice(Cart::CATEGORY_CHILD_BOOK, Cart::CHILD_BOOK_MAX_COUNT, $this->cart->getDiscount());
        $discountTotal = $total - $discountPrice;

        $this->assertLessThan($total, $discountPrice);
        $this->assertNotEquals(0.50, $discountPrice, '', 0.2);
        $this->assertNotEquals(9.50, $discountTotal, '', 0.2);
    }

    /**
     *  Cart get line discount function for each category more than 10 item on total bill
     */
    public function testBookLineDiscount()
    {
        $total = $this->cart->getTotal(); // total is 10
        $discountTotal = 0;
        $categoryCount = ['1' => 10, '2' => 10, '3' => 10];

        if (min($categoryCount) >= Cart::EACH_CATEGORY_MAX_COUNT) {
            $discountTotal = $total - ($total * ($this->cart->getLineDiscount() / 100)); // line discount is 5%
        }

        $this->assertEquals(9.50, $discountTotal, '', 0.2);
    }

    /**
     *  Cart get coupon discount on total bill
     */
    public function testBookCouponDiscount()
    {
        $total = $this->cart->getTotal(); // total is 10

        $this->cart->setCoupon('testCoupon'); // 15% discount is added

        $discountTotal = $this->cart->getCoupon() === null ? $total : $total - ($total * ($this->cart->getCouponDiscount() / 100));

        $this->assertEquals(8.50, $discountTotal, '', 0.2);
    }

    /**
     *  Remove item from cart
     */
    public function testRemove()
    {
        $this->cart->removeItem(1); //remove book from cart where id is

        $this->assertEquals(0, $this->cart->count());
    }

    /**
     *  Cart remove all function
     */
    public function testClear()
    {
        $this->cart->clear();

        $this->assertEquals(0, $this->cart->count());
    }
}