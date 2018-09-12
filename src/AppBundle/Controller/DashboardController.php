<?php

namespace AppBundle\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DashboardController
 *
 * @package AppBundle\Controller
 */
class DashboardController extends Controller
{
    /**
     * Display dashboard view
     *
     * @Route("/dashboard", name="dashboard")
     */
    public function indexAction(Request $request)
    {
        return $this->render('dashboard/index.html.twig');
    }
}
