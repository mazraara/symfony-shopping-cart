<?php

/**
 * Controller used to manage categories.
 *
 * @author Azraar Azward <mazraara@gmail.com>
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use AppBundle\Entity\Category;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CategoryController
 *
 * @package AppBundle\Controller
 *
 */
class CategoryController extends Controller
{
    /**
     * Lists all category entities.
     *
     * @Route("/dashboard/category", name="category_index", methods="GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:Category')->findAll();

        return $this->render('dashboard/listCategory.html.twig', ['categories' => $categories]);
    }

    /**
     * Creates a new category entity.
     *
     * @Route("/dashboard/category/new", name="category_new", methods={"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $categoryModel = new Category();
        $form = $this->createForm('AppBundle\Form\AddCategoryType', $categoryModel);

        $form->add('save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('dashboard/addCategory.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Displays a form to edit an existing category entity.
     *
     * @Route("/dashboard/category/{id}/edit", name="category_edit", methods={"GET", "POST"})
     */
    public function editAction(Request $request, Category $categoryModel)
    {
        $form = $this->createForm('AppBundle\Form\EditCategoryType', $categoryModel);
        $form->add('save', SubmitType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Your changes were saved!');

            return $this->redirectToRoute('category_index');
        }

        return $this->render('dashboard/addCategory.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Deletes a category entity.
     *
     * @Route("/dashboard/category/{id}/delete/", name="category_delete", methods="GET")
     */
    public function deleteAction(Category $categoryModel)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categoryModel);
            $em->flush();
            $this->addFlash('success', 'Your record was deleted!');
        } catch (\Exception $exception) {
            $this->addFlash('danger', 'Error. Books are linked with this category.');
        }

        return $this->redirectToRoute('category_index');
    }

    /**
     * Finds and displays all category entities in home page.
     *
     * @Route("/category", name="category_list", methods="GET")
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();
        $categories = $em->getRepository('AppBundle:Category')->findAll();

        return $this->render('default/category.html.twig', ['categories' => $categories]);
    }

    /**
     * Finds and displays a category entity in home page.
     *
     * @Route("/category/{id}", name="category_show", requirements={"id": "\d+"}, methods="GET")
     */
    public function showAction(Category $categoryModel)
    {
        $repositoryBooks = $this->getDoctrine()->getRepository(Book::class);

        return $this->render('default/singleCategory.html.twig', [
            'category' => $categoryModel,
            'books' => $repositoryBooks->findByCategory($categoryModel->getId()),
        ]);
    }
}
