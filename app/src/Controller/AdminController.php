<?php

namespace App\Controller;

use App\Entity\Creation;
use App\Form\CreationType;
use App\Repository\CreationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

final class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(CreationRepository $creationRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'activeCreations' => $creationRepository->findBy(['active' => true], ['id' => 'DESC']),
            'archivedCreations' => $creationRepository->findBy(['active' => false], ['id' => 'DESC']),
        ]);
    }

    #[Route('/admin/creations/new', name: 'app_admin_creation_new')]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $creation = new Creation();
        $creation->setActive(true);
        $creation->setCaroussel(false);

        $form = $this->createForm(CreationType::class, $creation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('imageFile')->getData();

            $safeFilename = $slugger->slug(pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME));
            $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

            $imageFile->move($this->getParameter('creations_directory'), $newFilename);
            $creation->setImage($newFilename);

            $entityManager->persist($creation);
            $entityManager->flush();

            $this->addFlash('success', 'Création ajoutée.');

            return $this->redirectToRoute('app_admin');
        }

        return $this->render('admin/creation_new.html.twig', [
            'creationForm' => $form,
        ]);
    }

    #[Route('/admin/creations/{id}/toggle-active', name: 'app_admin_creation_toggle_active', methods: ['POST'])]
    public function toggleActive(Request $request, Creation $creation, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($this->isCsrfTokenValid('toggle-active'.$creation->getId(), $request->request->get('_token'))) {
            $creation->setActive(!$creation->isActive());
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/creations/{id}/toggle-caroussel', name: 'app_admin_creation_toggle_caroussel', methods: ['POST'])]
    public function toggleCaroussel(Request $request, Creation $creation, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($this->isCsrfTokenValid('toggle-caroussel'.$creation->getId(), $request->request->get('_token'))) {
            $creation->setCaroussel(!$creation->isCaroussel());
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/creations/{id}/delete', name: 'app_admin_creation_delete', methods: ['POST'])]
    public function delete(Request $request, Creation $creation, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($this->isCsrfTokenValid('delete'.$creation->getId(), $request->request->get('_token'))) {
            $entityManager->remove($creation);
            $entityManager->flush();

            $this->addFlash('success', 'Création supprimée.');
        }

        return $this->redirectToRoute('app_admin');
    }
}