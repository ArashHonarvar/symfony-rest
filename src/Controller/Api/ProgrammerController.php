<?php

namespace App\Controller\Api;

use App\Api\ApiProblem;
use App\Api\ApiProblemException;
use App\Battle\PowerManager;
use App\Entity\Programmer;
use App\Form\ProgrammerType;
use App\Form\UpdateProgrammerType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Controller\BaseController;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * Class ProgrammerController
 * @package App\Controller\Web
 * @Route("/api" , defaults={"_format" : "JSON"})
 */
class ProgrammerController extends BaseController
{
    /**
     * @Route("/programmers", name="app_api_programmers", methods={"POST"})
     */
    public function newAction(Request $request)
    {
        $programmer = new Programmer();
        $form = $this->createForm(ProgrammerType::class, $programmer);
        $this->processForm($request, $form);
        if (!$form->isValid()) {
             $this->throwApiProblemValidationException($form);
        }

        $programmer->setUser($this->findUserByUsername("arash"));
        $this->getEm()->persist($programmer);
        $this->getEm()->flush();
        $location = $this->generateUrl('app_api_programmers_show', [
            'nickname' => $programmer->getNickname()
        ]);
        $response = $this->createApiResponse($programmer, 201);
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
        $response = $this->createApiResponse($programmer);
        return $response;
    }

    /**
     * @Route("/programmers" , methods={"GET"})
     */
    public function listAction()
    {
        $programmers = $this->getEm()->getRepository(Programmer::class)->findAll();
        $data = ["programmers" => $programmers];
        $response = $this->createApiResponse($data);
        return $response;
    }

    /**
     * @Route("/programmers/{nickname}" , name="app_api_programmers_update" , methods={"PUT"})
     */
    public function updateAction($nickname, Request $request)
    {
        $programmer = $this->getEm()->getRepository(Programmer::class)->findOneByNickname($nickname);

        if (!$programmer) {
            throw $this->createNotFoundException('No programmer with username ' . $nickname);
        }

        $form = $this->createForm(UpdateProgrammerType::class, $programmer);
        $this->processForm($request, $form);
        if (!$form->isValid()) {
             $this->throwApiProblemValidationException($form);
        }
        $this->getEm()->persist($programmer);
        $this->getEm()->flush();
        $response = $this->createApiResponse($programmer);
        return $response;
    }

    /**
     * @Route("/programmers/{nickname}" , methods={"DELETE"})
     */
    public function deleteAction($nickname)
    {
        $programmer = $this->getEm()->getRepository(Programmer::class)->findOneByNickname($nickname);

        if ($programmer) {
            $this->getEm()->remove($programmer);
            $this->getEm()->flush();
        }

        return new Response(null, 204);
    }

    private function processForm(Request $request, FormInterface $form)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        if (null === $data) {
            $apiProblem = new ApiProblem(
                400,
                ApiProblem::TYPE_INVALID_REQUEST_BODY_FORMAT
            );

            throw new ApiProblemException($apiProblem);
        }
        $clearMissing = $request->getMethod() != 'PATCH';
        $form->submit($data, $clearMissing);
    }


    private function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }

        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    private function throwApiProblemValidationException(FormInterface $form)
    {
        $errors = $this->getErrorsFromForm($form);
        $apiProblem = new ApiProblem(
            400,
            ApiProblem::TYPE_VALIDATION_ERROR
        );

        $apiProblem->set("errors", $errors);
        throw new ApiProblemException($apiProblem);
    }


}
