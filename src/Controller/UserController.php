<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security;

final class UserController extends AbstractController
{
    #[Route('/mes-ateliers', name: 'app_user_ateliers', methods: ['GET'])]
    public function voirMesAteliers(Security $security): Response
    {
        $user = $security->getUser();

        if (!$user) {
            throw new AccessDeniedException("Vous devez être connecté.");
        }

        return $this->render('user/ateliers.html.twig', [
            'ateliers' => $user->getAteliersInscrits(),
        ]);
    }
}
