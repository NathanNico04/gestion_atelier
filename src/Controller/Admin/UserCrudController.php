<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserCrudController extends AbstractCrudController
{

    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }
    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom'),
            TextField::new('prenom'),
            TextField::new('username'),
            TextField::new('password')->onlyOnForms(),
            ArrayField::new('roles')->hideOnForm(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->renderContentMaximized();
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            if (empty($entityInstance->getPassword())) {
                $defaultPassword = 'secret'; // Définir un mot de passe par défaut
                $hashedPassword = $this->passwordHasher->hashPassword($entityInstance, $defaultPassword);
                $entityInstance->setPassword($hashedPassword);
            }
            $entityInstance->setRoles(['ROLE_INSTRUCTEUR']);
        }
        parent::persistEntity($entityManager, $entityInstance);
    }

    #[Route('/admin/user/{id}/promote', name: 'admin_user_promote')]
    public function promoteToAdmin(int $id, EntityManagerInterface $entityManager): RedirectResponse
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('danger', 'Utilisateur introuvable.');
            return $this->redirectToRoute('admin');
        }

        $roles = $user->getRoles();

        // Vérifier que l'utilisateur est un instructeur
        if (!in_array('ROLE_INSTRUCTEUR', $roles, true)) {
            $this->addFlash('danger', 'Seuls les instructeurs peuvent être promus administrateurs.');
            return $this->redirectToRoute('admin');
        }

        // Vérifier que l'utilisateur n'est pas déjà admin
        if (in_array('ROLE_ADMIN', $roles, true)) {
            $this->addFlash('warning', 'Cet utilisateur est déjà administrateur.');
            return $this->redirectToRoute('admin');
        }

        // Promotion à administrateur
        $roles[] = 'ROLE_ADMIN';
        $user->setRoles($roles);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur promu administrateur !');

        return $this->redirectToRoute('admin');
    }




    public function configureActions(Actions $actions): Actions
    {

        $promoteAdmin = Action::new('promote', 'Promouvoir Admin', 'fas fa-user-shield')
            ->linkToRoute('admin_user_promote', fn (User $user) => ['id' => $user->getId()])
            ->addCssClass('btn btn-warning')
            ->displayIf(fn (User $user) => in_array('ROLE_INSTRUCTEUR', $user->getRoles()));

        return $actions
            ->add(Crud::PAGE_INDEX, $promoteAdmin)
            ->add(Crud::PAGE_DETAIL, $promoteAdmin)
            ->disable(Action::EDIT);
    }
}
