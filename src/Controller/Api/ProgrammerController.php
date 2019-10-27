<?php

namespace App\Controller\Api;

use App\Battle\PowerManager;
use App\Entity\Programmer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Controller\BaseController;

/**
 * Class ProgrammerController
 * @package App\Controller\Web
 * @Route("/api")
 */
class ProgrammerController extends BaseController
{
    /**
     * @Route("/programmers", name="app_api_programmers", methods={"POST"})
     */
    public function newAction(Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        $programmer = new Programmer($data['nickName'], $data['avatarNumber']);
        $programmer->setTagLine($data['tagLine']);
        $programmer->setUser($this->findUserByUsername("arash"));
        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();
        return New Response("It worked");
    }

}
