<?php


namespace App\Controller\api;


use App\Entity\Programmer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api")
 */
class ApiController extends AbstractController
{
    /**
     * @Route("/programmers/list", name="app_api_programmers", methods={"get"})
     */
    public function programmers(Request $request)
    {
        $programmers = $this->getDoctrine()->getManager()->getRepository(Programmer::class)->findAll();
        $data['programmers'] = [];

        foreach ($programmers as $programmer) {
            $data['programmers'][] = $this->serilizeProgrammer($programmer);
        }
        $response = new JsonResponse($data);
        return $response;
    }

    /**
     * @Route("/programmers/add", name="app_api_add_programmer", methods={"post"})
     */
    public function addProgrammer(Request $request)
    {
        $data['nickName'] = $request->get('nickName');
        $data['avatarNumber'] = $request->get('avatarNumber');
        $data['tagLine'] = $request->get('tagLine');

        $programmer = new Programmer();

        $this->handleProgrammer($data, $programmer);


        $response = new JsonResponse($this->serilizeProgrammer($programmer), 201, ['location' => $this->generateUrl('app_api_show_programmer', ['nickName' => $programmer->getNickName()])]);

        return $response;
    }

    /**
     * @Route("/programmers/show/{nickName}", name="app_api_show_programmer", methods={"get"})
     */
    public function showProgrammer(Request $request, $nickName)
    {
        $programmer = $this->getDoctrine()->getManager()->getRepository(Programmer::class)->findOneBy(['nickName' => $nickName]);

        if (empty($programmer)) {
            throw new NotFoundHttpException();
        } else {
            $response = new JsonResponse($this->serilizeProgrammer($programmer));
            return $response;
        }

    }

    /**
     * @Route("/programmers/update/{nickName}", name="app_api_show_programmer", methods={"put"})
     */
    public function updateProgrammer(Request $request, $nickName)
    {
        $programmer = $this->getDoctrine()->getManager()->getRepository(Programmer::class)->findOneBy(['nickName' => $nickName]);

        if (empty($programmer)) {
            throw new NotFoundHttpException();
        } else {
            $data['nickName'] = $request->get('nickName');
            $data['avatarNumber'] = $request->get('avatarNumber');
            $data['tagLine'] = $request->get('tagLine');
            $this->handleProgrammer($data, $programmer);
            $response = new JsonResponse($this->serilizeProgrammer($programmer), 201, ['location' => $this->generateUrl('app_api_show_programmer', ['nickName' => $programmer->getNickName()])]);
            return $response;
        }

    }


    private function serilizeProgrammer(Programmer $programmer)
    {

        $data['nickName'] = $programmer->getNickName();
        $data['tagLine'] = $programmer->getTagLine();
        $data['avatarNumber'] = $programmer->getAvatarNumber();

        return $data;

    }


    private function handleProgrammer($data, Programmer $programmer)
    {
        $programmer->setAvatarNumber($data['avatarNumber']);
        $programmer->setNickName($data['nickName']);
        $programmer->setTagLine($data['tagLine']);

        $this->getDoctrine()->getManager()->persist($programmer);
        $this->getDoctrine()->getManager()->flush();

        return $programmer;
    }

}