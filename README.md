# CC Symfony | Framework Web 2

## Membres du groupe

- **Maximilien Goujon** <maximilien.goujon@etu.univ-orleans.fr>
- **Nathan Nicolessi** <nathan.nicolessi@etu.univ-orleans.fr>
- **Lisa Blavot** <lisa.blavot@etu.univ-orleans.fr>
- **Theo Cretel** <theo.cretel@etu.univ-orleans.fr>

## Utilisateurs de l'applications

- **Admin**
  - **Username:** admin
  - **Password:** secret
- **Instructeur**
  - **Username:** instructeur
  - **Password:** secret
- **Apprenti n°$i** _(avec $i de 1 à 5)_
  - **Username:** apprenti1 à apprenti5
  - **Password:** secret


## Question 1

- `symfony new cc_symfony --webapp`
- `symfony composer require symfony/webpack-encore-bundle`
- `npm install`
- `npm install bootstrap bootstrap-icons`
- **Modification des fichiers** `assets/app.js` **et** `config/package/twig.yaml` **pour integrer bootstrap**
- `npm run dev`

## Question 2

- `symfony console make:entity Atelier`
- `symfony console doctrine:database:create`
- `symfony console make:migration`
- `symfony console doctrine:migrations:migrate`

## Question 3

- `symfony composer require orm-fixtures --dev`
- `symfony composer require fakerphp/faker`
- `symfony console make:fixtures AtelierFixtures`
- Modification du fichier `src/DataFixtures/AtelierFixtures.php` _(pour ajouter les données faker)_
- `symfony console doctrine:fixtures:load`

## Question 4

- `symfony console make:crud Atelier`

## Question 5

- **Ajout d'une barre de navigation dans** `templates/layers/_navbar.html.twig`
- **Personnalisation des templates CRUD**
  - `templates/atelier/index.html.twig`
  - `templates/atelier/show.html.twig`
  - `templates/atelier/new.html.twig`
  - `templates/atelier/edit.html.twig`

## Question 6

- `symfony composer require cebe/markdown`
- **Ajouter dans** `config/services.yaml`**, le service :** `cebe\markdown\Markdown: ~`
- `symfony console make:twig-extension`
- **Modification des fichiers MarkdownExtension.php, MarkdownExtensionRuntime.php**
  ```php
  new TwigFilter('filter_name', [MarkdownExtensionRuntime::class, 'markdownToHtml']),
  ```
  ```php
  new TwigFunction('function_name', [MarkdownExtensionRuntime::class, 'markdownToHtml']),
  ```
- **Modification des fixtures et des templates**

## Question 7

- `symfony console make:user`
- **Modification de la fonction `getRoles()` pour que le role de base soit le role instructeur**
  ```php
  if (empty($roles)) {
            $roles[] = 'ROLE_INSTRUCTEUR';
        }
  ```
- `symfony console m:mig`
- `symfony console d:m:m`
- `symfony console make:auth`
- **Modification de la fonction `onAuthenticationSuccess`dans `Security/AppAuthenticator.php`**
  ```php
  return new RedirectResponse($this->urlGenerator->generate('app_atelier_index'));
  ```
- `symfony console make:registration-form`
- **Modification de la fonction `register`dans `Controller/RegistrationController.php`**
  ```php
  public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, Security $security, EntityManagerInterface $entityManager): Response
  ```
  ```php
  return $userAuthenticator->authenticateUser(
      $user,
      $authenticator,
      $request
  );
  ```

## Question 8

- `symfony console make:entity Atelier` _(Ajout de la relation ManyToOne vers User)_
- `symfony console m:mig`
- `symfony console d:m:m`
- **Modification des fixtures en ajoutant un instructeur par défaut pour les cours dans `DataFixtures/AtelierFixtures.php`**
  ```php
  $user = new User();
  $user->setUsername('instructeur')
      ->setPassword($this->passwordHasher->hashPassword(
          $user,'secret'
      ))
  ->setRoles(['ROLE_INSTRUCTEUR']);
  $manager->persist($user);
  ```
- `symfony console d:f:l`

## Question 9

- **Dans `security.yaml`, ajout de la règle suivante:**
  ```yaml
  { path: ^/atelier/new, roles: ROLE_INSTRUCTEUR }
  ```
- **Ajout de `is_granted('ROLE_INSTRUCTEUR')` dans les templates contenant un bouton _Creer un nouvel atelier_**
  ```twig
  {% if is_granted('ROLE_INSTRUCTEUR') %}
    <a href="{{ path('app_atelier_new') }}" class="btn btn-success mt-3">Créer un nouvel atelier</a>
  {% else %}
    <a href="#" class="btn btn-secondary mt-3">Connectez-vous pour creer un atelier</a>
  {% endif %}
  ```

## Question 10

- **Restriction de l'acces à la modification de atelier dans `AtelierController.php`**
- **Ajout de `is_granted('ROLE_INSTRUCTEUR')` dans les templates contenant un bouton _Modifier_**
  ```twig
  {% if is_granted('ROLE_INSTRUCTEUR') %}
    <a href="{{ path('app_atelier_show', {'id': atelier.id}) }}" class="btn btn-info btn-lg me-2 align-self-center">Voir</a>
    <a href="{{ path('app_atelier_edit', {'id': atelier.id}) }}" class="btn btn-warning btn-lg align-self-center">Modifier</a>
  {% else %}
    <a href="{{ path('app_atelier_show', {'id': atelier.id}) }}" class="btn btn-info btn-lg me-2 align-self-center">Voir</a>
    <a href="#" class="btn btn-secondary btn-sm align-self-center">Non Modifiable</a>
  {% endif %}
  ```

## Question 11

- **Modification de la navbar `layers/_navbar.html.twig`**
- **Modification des templates suivants:**
  - `templates/security/login.html.twig`
  - `templates/registration/register.html.twig`

## Question 12

- `symfony console make:entity Atelier` _(Ajout de la relation ManyToMany vers User)_
  - `private Collection $apprentis;` _(Dans Atelier.php)_
  - `private Collection $ateliersInscrits;` _(Dans User.php)_
- **Ajout d'une méthode `isParticipant` pour voir si un utilisateur est inscrit à l'atelier**
- **Ajout de 2 méthodes inscription/desinscription dans `AtelierController.php`**
- **Modification des templates `show.html.twig`:**
- `symfony console make:migration`
- `symfony console doctrine:migrations:migrate`

## Question 13

- **Création d'une méthode `voirApprentis` dans `AtelierController.php` pour l'instructeur**
- **Création d'un template associé `templates/atelier/apprentis.html.twig`**
- `symfony console make:controller User`
- **Création d'une méthode `voirMesAteliers` dans `UserController.php` pour l'apprenti**
- **Création d'un template associé `templates/user/ateliers.html.twig`**

## Question 14

- **Modification de la permission de suppression uniquement pour les instructeurs**
- **Amélioration de l'esthétique des boutons**
- **Mise à jour des templates selon les nouvelles permissions**
- **Ajout d'une page d'accueil**
- `symfony console make:controller Pages`

## Question 15

- `symfony composer require admin`
- `symfony console make:admin:dashboard`
- **Ajout d'un fichier `config/routes/easyadmin.yaml`**
  ```yaml
  easyadmin:
    resource: .
    type: easyadmin.routes
  ```
- **Modification du fichier `security.yaml`**
  ```yaml
  - { path: ^/admin, roles: ROLE_ADMIN }
  ```
- **Création d'un admin dans la fixtures et 2 apprentis**
- **Ajout d'une fonction `promoteAdmin` dans `UserCrudController`**

## Question 16

- **Modification des droits d'accès et de la visibilité pour les templates suivants**
  - `templates/layers/_navbar.html.twig`
  - `templates/atelier/index.html.twig`
  - `templates/atelier/show.html.twig`

## Question 17

- `symfony composer require symfony/ux-chartjs`
- `npm install --force`
- `npm run dev`
- `symfony console make:entity AtelierSatisfaction`
- **Ajout des champs `apprenti`, `atelier` et `note` dans l'entité `AtelierSatisfaction`**
- `symfony console make:mig`
- `symfony console d:m:m`
- `symfony console make:form AtelierSatisfactionType`
- **Ajout des actions nécessaires dans `AtelierController`**
- **Création des templates `note.html.twig` et `satisfaction.html.twig`**

## Question 18

- **Modification de la fixture pour le texte de la description**
- `symfony composer require knplabs/knp-paginator-bundle` _(pour la pagination)_
- **Modification de la fonction `index` dans `AtelierController.php`**
- **Ajout d'un fichier `layers/pagination.html.twig`**
- **Ajout de paginator dans le fichier `atelier/index.html.twig`**
