<?php

namespace App\Controller\Web;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\BaseController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Guard\AuthenticatorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class UserController extends BaseController
{
    /**
     * @Route("/register", name="user_register", methods={"GET"})
     */
    public function registerAction()
    {
        if ($this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('user/register.twig', array('user' => new User()));
    }

    /**
     * @Route("/register", name="user_register_handle", methods={"POST"})
     */
    public function registerHandleAction(Request $request , UserPasswordEncoderInterface $passwordEncoder)
    {
        $errors = array();
        if (!$email = $request->request->get('email')) {
            $errors[] = '"email" is required';
        }
        if (!$plainPassword = $request->request->get('plainPassword')) {
            $errors[] = '"password" is required';
        }
        if (!$username = $request->request->get('username')) {
            $errors[] = '"username" is required';
        }

        $userRepository = $this->getUserRepository();

        // make sure we don't already have this user!
        if ($existingUser = $userRepository->findUserByEmail($email)) {
            $errors[] = 'A user with this email is already registered!';
        }

        // make sure we don't already have this user!
        if ($existingUser = $userRepository->findUserByUsername($username)) {
            $errors[] = 'A user with this username is already registered!';
        }

        $user = new User();
        $user->setEmail($email);
        $user->setUsername($username);
        $encodedPassword = $passwordEncoder
            ->encodePassword($user, $plainPassword);
        $user->setPassword($encodedPassword);

        // errors? Show them!
        if (count($errors) > 0) {
            return $this->render('user\register.twig', array('errors' => $errors, 'user' => $user));
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $this->loginUser($user);

        return $this->redirect($this->generateUrl('homepage'));
    }

    /**
     * @Route("/login", name="security_login_form")
     */
    public function loginAction(Request $request, AuthenticationUtils $authenticationUtils)
    {
        if ($this->isUserLoggedIn()) {
            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render('user/login.twig', array(
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'last_username' => $authenticationUtils->getLastUserName()
        ));
    }

    /**
     * @Route("/login_check", name="security_login_check")
     */
    public function loginCheckAction()
    {
        throw new \Exception('Should not get here - this should be handled magically by the security system!');
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logoutAction()
    {
        throw new \Exception('Should not get here - this should be handled magically by the security system!');
    }
}
