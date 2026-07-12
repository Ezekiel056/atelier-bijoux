<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AdminSetupType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/admin/login', name: 'app_admin_login', methods: ['GET'])]
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_admin');
        }

        if (0 === $userRepository->count([])) {
            return $this->redirectToRoute('app_admin_setup');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/admin.login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/admin/setup', name: 'app_admin_setup')]
    public function setup(
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
    ): Response {
        // Only allow bootstrapping the very first admin account. Once one exists, this route is dead.
        if ($userRepository->count([]) > 0) {
            return $this->redirectToRoute('app_admin_login');
        }

        $user = new User();
        $form = $this->createForm(AdminSetupType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_ADMIN']);
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Compte administrateur créé, vous pouvez vous connecter.');

            return $this->redirectToRoute('app_admin_login');
        }

        return $this->render('security/admin_setup.html.twig', [
            'setupForm' => $form,
        ]);
    }

    #[Route(path: '/admin/login', name: 'app_admin_login_check', methods: ['POST'])]
    public function loginCheck(): Response
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the form_login authenticator.');
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}