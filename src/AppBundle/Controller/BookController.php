<?php

/**
 * Controller used to manage books.
 *
 * @author Azraar Azward <mazraara@gmail.com>
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BookController
 *
 * @package AppBundle\Controller
 */
class BookController extends Controller
{
    /**
     * Lists all book entities.
     *
     * @Route("/dashboard/book", name="book_index", methods="GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository('AppBundle:Book')->findAllOrderedByName();

        return $this->render('dashboard/listBook.html.twig', ['books' => $books]);
    }

    /**
     * Creates a new book entity.
     *
     * @Route("/dashboard/book/new", name="book_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $bookModel = new Book();
        $form = $this->createForm('AppBundle\Form\AddBookType', $bookModel);

        $form->add('save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $book = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('book_index');
        }

        return $this->render('dashboard/addBook.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing Book entity.
     *
     * @Route("/dashboard/book/{id}/edit", name="book_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Book $bookModel)
    {
        $form = $this->createForm('AppBundle\Form\EditBookType', $bookModel);
        $form->add('save', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('book_index');
        }

        return $this->render('dashboard/addBook.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Deletes a book entity.
     *
     * @Route("/dashboard/book/{id}/delete/", name="book_delete", methods="GET")
     */
    public function deleteAction(Book $bookModel)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($bookModel);
            $em->flush();
            $this->addFlash('success', 'Your record was deleted!');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Error. Books are linked with this category.');
        }

        return $this->redirectToRoute('category_index');
    }

    /**
     * Finds and displays a book entity in home page.
     *
     * @Route("/book/{id}", name="book_show", requirements={"id": "\d+"}, methods="GET")
     */
    public function showAction(Book $bookModel)
    {
        return $this->render('default/singleBook.html.twig', [
            'book' => $bookModel,
        ]);
    }
}