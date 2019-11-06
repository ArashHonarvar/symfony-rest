<?php

namespace App\Controller;

use App\Battle\BattleManager;
use App\Repository\ProgrammerRepository;
use App\Repository\UserRepository;
use App\Repository\ProjectRepository;
use App\Repository\BattleRepository;
use App\Repository\ApiTokenRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Prophecy\Argument\Token\TokenInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;

abstract class BaseController extends AbstractController
{
    /**
     * Is the current user logged in?
     *
     * @return boolean
     */
    public function isUserLoggedIn()
    {
        return $this->container->get('security.authorization_checker')
            ->isGranted('IS_AUTHENTICATED_FULLY');
    }

    /**
     * Logs this user into the system
     *
     * @param User $user
     */
    public function loginUser(User $user)
    {
        $token = new UsernamePasswordToken($user, $user->getPassword(), 'main', $user->getRoles());

        $this->container->get('security.token_storage')->setToken($token);
    }

//    public function addFlash($message, $positiveNotice = true)
//    {
//        /** @var Request $request */
//        $request = $this->container->get('request_stack')->getCurrentRequest();
//        $noticeKey = $positiveNotice ? 'notice_happy' : 'notice_sad';
//
//        $request->getSession()->getFlashbag()->add($noticeKey, $message);
//    }

    /**
     * Used to find the fixtures user - I use it to cheat in the beginning
     *
     * @param $username
     * @return User
     */
    public function findUserByUsername($username)
    {
        return $this->getUserRepository()->findUserByUsername($username);
    }

    /**
     * @return UserRepository
     */
    protected function getUserRepository()
    {
        return $this->getDoctrine()
            ->getRepository('App:User');
    }

    /**
     * @return ProgrammerRepository
     */
    protected function getProgrammerRepository()
    {
        return $this->getDoctrine()
            ->getRepository('App:Programmer');
    }

    /**
     * @return ProjectRepository
     */
    protected function getProjectRepository()
    {
        return $this->getDoctrine()
            ->getRepository('App:Project');
    }

    /**
     * @return BattleRepository
     */
    protected function getBattleRepository()
    {
        return $this->getDoctrine()
            ->getRepository('App:Battle');
    }

    /**
     * @return \App\Battle\BattleManager
     */
    protected function getBattleManager()
    {
        return new BattleManager($this->getDoctrine()->getManager());
    }

    /**
     * @return ApiTokenRepository
     */
    protected function getApiTokenRepository()
    {
        return $this->getDoctrine()
            ->getRepository('App:ApiToken');
    }

    /**
     * @return ObjectManager
     */
    protected function getEm()
    {
        return $this->getDoctrine()->getManager();
    }
}
