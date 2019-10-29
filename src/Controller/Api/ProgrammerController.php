<?php

namespace App\Controller\Api;

use App\Battle\PowerManager;
use App\Entity\Programmer;
use App\Form\ProgrammerType;
use Symfony\Component\HttpFoundation\JsonResponse;
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
        $programmer = new Programmer();
        $form = $this->createForm(ProgrammerType::class, $programmer);
        $form->submit($data);
        $programmer->setUser($this->findUserByUsername("arash"));
        $this->getEm()->persist($programmer);
        $this->getEm()->flush();
        $location = $this->generateUrl('app_api_programmers_show', [
            'nickname' => $programmer->getNickname()
        ]);
        $data = $this->serializeProgrammer($programmer);
        $response = New JsonResponse($data, 201);
        $response->headers->set('location', $location);
        return $response;
    }

    /**
     * @Route("/programmers/{nickname}" , name="app_api_programmers_show" , methods={"GET"})
     */
    public function showAction($nickname)
    {
        $programmer = $this->getEm()->getRepository(Programmer::class)->findOneByNickname($nickname);

        if (!$programmer) {
            throw $this->createNotFoundException('No programmer with username ' . $nickname);
        }
        $data = $this->serializeProgrammer($programmer);
        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/programmers" , methods={"GET"})
     */
    public function listAction()
    {
        $programmers = $this->getEm()->getRepository(Programmer::class)->findAll();
        $data = ["programmers" => []];

        foreach ($programmers as $programmer) {
            $data['programmers'][] = $this->serializeProgrammer($programmer);
        }
        $response = new JsonResponse($data);
        return $response;
    }


    private function serializeProgrammer(Programmer $programmer)
    {
        return [
            'nickname' => $programmer->getNickname(),
            'avatarNumber' => $programmer->getAvatarNumber(),
            'powerLevel' => $programmer->getPowerLevel(),
            'tagLine' => $programmer->getTagLine()
        ];
    }

}
