<?php

/**
 * Controller used to manage the shopping cart.
 *
 * @author Azraar Azward <mazraara@gmail.com>
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\OrderBook;
use AppBundle\Entity\Orders;
use AppBundle\Entity\User;
use AppBundle\Utils\Cart;
use AppBundle\Utils\CartItem;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Attribute\NamespacedAttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;

class CartController extends Controller
{
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    protected $cart;

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
     * Takes the user to the cart list
     * @Route("/cart/list", name="cart_index")
     */
    public function showCartAction()
    {
        $cart = $this->cart->getItems();

        return $this->render('default/cart.html.twig', ['cart' => $cart]);
    }

    /**
     * Clears the cart
     *
     * @Route("/cart/clear", name="cart_clear")
     */
    public function clearCartAction()
    {
        $this->cart->clear();

        $this->addFlash('success', 'Cart cleared');

        return $this->redirectToRoute('homepage');
    }

    /**
     * Adds coupon to the cart
     *
     * @Route("/cart/add-coupon", name="coupon_add")
     */
    public function addCouponAction(Request $request)
    {
        $coupon = $request->get('coupon', null);

        if (! empty($coupon)) {
            $this->cart->setCoupon($coupon);
            $this->addFlash('success', 'Coupon redeemed successfully.');
        } else {
            $this->addFlash('danger', 'Coupon code cannot be empty.');
        }

        return $this->redirectToRoute('cart_index');
    }

    /**
     *  Adds the book to cart list
     *
     * @Route("/cart/{id}", name="cart_add", requirements={"id": "\d+"}, methods="GET")
     */
    public function addToCartAction(Book $bookModel)
    {
        $item = new CartItem([
            'id' => $bookModel->getId(),
            'name' => $bookModel->getName(),
            'price' => $bookModel->getPrice(),
        ]);
        $item->setQuantity(1); // defaults to 1
        $item->setCategoryId($bookModel->getCategoryId());
        $this->cart->addItem($item);

        $this->addFlash('success', 'Book added to cart successfully.');

        return $this->redirectToRoute('homepage');
    }

    /**
     * Removes given book from the cart
     *
     * @Route("/cart/remove/{id}", name="cart_remove", requirements={"id": "\d+"})
     */
    public function removeCartAction(int $id)
    {
        $this->cart->removeItem($id);
        $this->addFlash('success', 'Book removed from the cart.');

        return $this->redirectToRoute('homepage');
    }

    /**
     * Checkout process of the cart
     *
     * @Route("/cart/checkout", name="cart_checkout")
     */
    public function checkOutAction()
    {
        $cartItems = $this->cart->getItems();
        $cartTotal = $this->cart->getDiscountTotal();
        $discount = $this->cart->getAppliedDiscount();

        $em = $this->getDoctrine()->getManager();
        $firstUser = $em->getRepository(User::class)->findOneBy([]); // current user id needs to be set after sign up
        $order = new Orders();

        try {
            $order->setUserId($firstUser->getId());
            $order->setTotal($cartTotal);
            $order->setCreatedAt(new \DateTime());
            $em->persist($order);
            $em->flush();

            foreach ($this->cart->getItems() as $item) {
                $orderBook = new OrderBook();
                $orderBook->setQty($item->getQuantity());
                $orderBook->setOrderId($order->getId());
                $orderBook->setBookId($item->getId());
                $orderBook->setTotal($item->getTotal());
                $em->persist($orderBook);
                $em->flush();
            }

            $this->addFlash('success', 'Checkout completed. Your order will be shipped soon.');
            $this->cart->clear();
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Error'); // need to log the exception details
        }

        return $this->render('default/invoice.html.twig', [
            'cart' => $cartItems,
            'total' => $cartTotal,
            'orderId' => $order->getId(),
            'discount'=> $discount,
        ]);
    }
}