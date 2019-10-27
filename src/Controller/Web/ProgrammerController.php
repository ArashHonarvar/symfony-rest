<?php

namespace App\Controller\Web;

use App\Battle\PowerManager;
use App\Entity\Programmer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use App\Controller\BaseController;

class ProgrammerController extends BaseController
{
    /**
     * @Route("/programmers/new", name="programmer_new", methods={"GET"})
     */
    public function newAction()
    {
        $programmer = new Programmer();

        return $this->render('programmer/new.twig', array('programmer' => $programmer));
    }

    /**
     * @Route("/programmers/new", name="programmer_new_handle", methods={"POST"})
     */
    public function handleNewAction(Request $request)
    {
        $programmer = new Programmer();

        $errors = array();
        $data = $this->getAndValidateData($request, $errors);
        $programmer->setNickname($data['nickname']);
        $programmer->setAvatarNumber($data['avatarNumber']);
        $programmer->setTagLine($data['tagLine']);
        $programmer->setUser($this->getUser());

        if ($errors) {
            return $this->render('programmer/new.twig', array('programmer' => $programmer, 'errors' => $errors));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($programmer);
        $em->flush();

        $this->addFlash(sprintf('%s has been compiled and is ready for battle!', $programmer->getNickname()));
        return $this->redirect($this->generateUrl('programmer_show', array('nickname' => $programmer->getNickname())));
    }

    /**
     * @Route("/programmers/choose", name="programmer_choose" , methods={"GET"})
     */
    public function chooseAction()
    {
        $programmers = $this->getProgrammerRepository()->findAllForUser($this->getUser());

        return $this->render('programmer/choose.twig', array('programmers' => $programmers));
    }

    /**
     * @Route("/programmers/{nickname}", name="programmer_show", methods={"GET"})
     */
    public function showAction($nickname)
    {
        $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);
        if (!$programmer) {
            throw new NotFoundHttpException();
        }

        $projects = $this->getProjectRepository()->findRandom(3);

        return $this->render('programmer/show.twig', array(
            'programmer' => $programmer,
            'projects' => $projects,
        ));
    }

    /**
     * @Route("/programmers/{nickname}/power/up", name="programmer_powerup", methods={"POST"})
     */
    public function powerUpAction($nickname , PowerManager $powerManager)
    {
        /** @var Programmer $programmer */
        $programmer = $this->getProgrammerRepository()->findOneByNickname($nickname);

        if ($programmer->getUser() != $this->getUser()) {
            throw new AccessDeniedException;
        }

        $powerupDetails = $powerManager->powerUp($programmer);

        $this->addFlash(
            $powerupDetails['message'],
            $powerupDetails['powerChange'] > 0
        );

        return $this->redirect($this->generateUrl('programmer_show', array('nickname' => $programmer->getNickname())));
    }

    /**
     * @param Request $request
     * @param array $errors Array that will be filled with errors (I hate
     *                      passing things by reference, but it makes this simple)
     * @return array
     */
    private function getAndValidateData(Request $request, &$errors)
    {
        $nickname = $request->request->get('nickname');
        $avatarNumber = $request->request->get('avatarNumber');
        $tagLine = $request->request->get('tagLine');

        $errors = array();
        if (!$nickname) {
            $errors[] = 'Give your programmer a nickname!';
        }
        if (!$avatarNumber) {
            $errors[] = 'Choose an awesome avatar bro!';
        }

        $existingProgrammer = $this->getProgrammerRepository()->findOneByNickname($nickname);
        if ($existingProgrammer) {
            $errors[] = 'Looks like that programmer already exists - try a different nickname';
        }

        return array(
            'nickname' => $nickname,
            'avatarNumber' => $avatarNumber,
            'tagLine' => $tagLine,
        );
    }
}
