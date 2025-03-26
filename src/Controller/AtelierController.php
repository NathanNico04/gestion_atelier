<?php

namespace App\Controller;

use App\Entity\Atelier;
use App\Entity\AtelierSatisfaction;
use App\Form\AtelierSatisfactionType;
use App\Form\AtelierType;
use App\Repository\AtelierRepository;
use App\Repository\AtelierSatisfactionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;


#[Route('/atelier')]
final class AtelierController extends AbstractController
{
    #[Route(name: 'app_atelier_index', methods: ['GET'])]
    public function index(Request $request, AtelierRepository $atelierRepository, PaginatorInterface $paginator): Response
    {
        $pageAtelier = $atelierRepository->findAll();
        $pageAtelier = $paginator->paginate(
            $pageAtelier,
            $request->query->getInt('page', 1),
            5
        );
        return $this->render('atelier/index.html.twig', [
            'ateliers' => $pageAtelier,
        ]);
    }


    #[Route('/{id}/note', name: 'app_atelier_note', methods: ['GET', 'POST'])]
    public function note(Request $request, Atelier $atelier, EntityManagerInterface $entityManager, Security $security, AtelierSatisfactionRepository $satisfactionRepository): Response
    {
        // Vérifie que l'utilisateur est un apprenti
        if (!$security->isGranted('ROLE_APPRENTI')) {
            throw new AccessDeniedException('Seuls les apprentis peuvent noter un atelier.');
        }

        $user = $security->getUser();

        // Vérifie que l'apprenti est inscrit à l'atelier
        if (!$atelier->isParticipant($user)) {
            $this->addFlash('error', 'Vous devez être inscrit à l\'atelier pour pouvoir le noter.');
            return $this->redirectToRoute('app_atelier_show', ['id' => $atelier->getId()]);
        }

        // Vérifie si l'apprenti a déjà noté cet atelier
        $existingNote = $satisfactionRepository->findOneBy([
            'apprenti' => $user,
            'atelier' => $atelier
        ]);

        if ($existingNote) {
            $this->addFlash('error', 'Vous avez déjà noté cet atelier.');
            return $this->redirectToRoute('app_atelier_show', ['id' => $atelier->getId()]);
        }

        $satisfaction = new AtelierSatisfaction();
        $satisfaction->setApprenti($user);
        $satisfaction->setAtelier($atelier);

        $form = $this->createForm(AtelierSatisfactionType::class, $satisfaction);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($satisfaction);
            $entityManager->flush();

            $this->addFlash('success', 'Votre note a été enregistrée.');
            return $this->redirectToRoute('app_atelier_show', ['id' => $atelier->getId()]);
        }

        return $this->render('atelier/note.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/satisfaction', name: 'app_atelier_satisfaction', methods: ['GET'])]
    public function satisfaction(Atelier $atelier, AtelierSatisfactionRepository $repository, Security $security): Response
    {
        if (!$security->isGranted('ROLE_INSTRUCTEUR') || $atelier->getUser() !== $security->getUser()) {
            throw new AccessDeniedException('Accès réservé à l\'instructeur de cet atelier.');
        }

        // Récupération et comptage des notes
        $satisfactions = $repository->findBy(['atelier' => $atelier]);
        $notes = array_fill(0, 6, 0); // Initialise un tableau [0,0,0,0,0,0]

        foreach ($satisfactions as $satisfaction) {
            $notes[$satisfaction->getNote()]++;
        }

        return $this->render('atelier/satisfaction.html.twig', [
            'atelier' => $atelier,
            'notes' => $notes,
        ]);
    }


    #[Route('/new', name: 'app_atelier_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $atelier = new Atelier();
        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($atelier);
            $entityManager->flush();

            return $this->redirectToRoute('app_atelier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('atelier/new.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_atelier_show', methods: ['GET'])]
    public function show(Atelier $atelier): Response
    {
        return $this->render('atelier/show.html.twig', [
            'atelier' => $atelier,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_atelier_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Atelier $atelier, EntityManagerInterface $entityManager, Security $security): Response
    {

        if (!$security->isGranted('ROLE_INSTRUCTEUR') || $atelier->getUser() !== $security->getUser()) {
          throw new AccessDeniedException();
        }

        $form = $this->createForm(AtelierType::class, $atelier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_atelier_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('atelier/edit.html.twig', [
            'atelier' => $atelier,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_atelier_delete', methods: ['POST'])]
    public function delete(Request $request, Atelier $atelier, EntityManagerInterface $entityManager, Security $security): Response
    {
        if (!$security->isGranted('ROLE_INSTRUCTEUR') || $atelier->getUser() !== $security->getUser()) {
          throw new AccessDeniedException();
        }

        if ($this->isCsrfTokenValid('delete'.$atelier->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($atelier);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_atelier_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/inscription', name: 'app_atelier_inscription', methods: ['POST'])]
    public function inscription(Atelier $atelier, EntityManagerInterface $entityManager, Security $security, Request $request): Response
    {

        if (!$security->isGranted('ROLE_APPRENTI')) {
            throw new AccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('inscription'.$atelier->getId(), $request->getPayload()->getString('_token'))) {
            return $this->redirectToRoute('app_atelier_index');
        }

        $user = $security->getUser();

        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour vous inscrire.');
        }

        if (!$atelier->isParticipant($user)) {
            $atelier->addApprenti($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_atelier_show', ['id' => $atelier->getId()]);
    }

    #[Route('/{id}/desinscription', name: 'app_atelier_desinscription', methods: ['POST'])]
    public function desinscription(Atelier $atelier, EntityManagerInterface $entityManager, Security $security, Request $request): Response
    {
        if (!$security->isGranted('ROLE_APPRENTI')) {
            throw new AccessDeniedException();
        }

        if (!$this->isCsrfTokenValid('desinscription'.$atelier->getId(), $request->getPayload()->getString('_token'))) {
            return $this->redirectToRoute('app_atelier_index');
        }

        $user = $security->getUser();

        if (!$user) {
            throw new AccessDeniedException('Vous devez être connecté pour vous désinscrire.');
        }

        if ($atelier->isParticipant($user)) {
            $atelier->removeApprenti($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_atelier_show', ['id' => $atelier->getId()]);
    }

    #[Route('/{id}/apprentis', name: 'app_atelier_apprentis', methods: ['GET'])]
    public function voirApprentis(Atelier $atelier, Security $security): Response
    {
        if ($atelier->getUser() !== $security->getUser()) {
            throw new AccessDeniedException("Vous n'avez pas accès à cette liste.");
        }

        if (!$security->isGranted('ROLE_INSTRUCTEUR') || $atelier->getUser() !== $security->getUser()) {
            throw new AccessDeniedException();
        }

        return $this->render('atelier/apprentis.html.twig', [
            'atelier' => $atelier,
            'apprentis' => $atelier->getApprentis(),
        ]);
    }

}
