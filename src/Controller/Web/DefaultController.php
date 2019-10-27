<?php

namespace App\Controller\Web;

use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends BaseController
{
    /**
     * @Route("/", name="homepage")
     */
    public function homepageAction()
    {
        return $this->render('homepage.twig');
    }
}
