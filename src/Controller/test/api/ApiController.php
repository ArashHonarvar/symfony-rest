<?php


namespace App\Controller\test\api;


use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/test/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/programmers", name="app_test_api_programmers", methods={"post" , "get"})
     */
    public function testPost(Request $request)
    {
        $client = new Client();

    }

}