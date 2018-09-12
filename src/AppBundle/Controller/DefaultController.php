<?php

/**
 * Controller used to display books in home page.
 *
 * @author Azraar Azward <mazraara@gmail.com>
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * Method to display books in home page
     *
     * @param Request $request request object
     * @param BookLogic $bookLogic Books business logic
     *
     * @return Response
     *
     * @Route("/", name="homepage")
     */
    public function index(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $books = $em->getRepository('AppBundle:Book')->findAllOrderedByName();

        return $this->render('default/index.html.twig', ['books' => $books]);
    }
}
