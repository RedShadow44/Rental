<?php

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Form\Type\UserType;
use App\Repository\BookRepository;
use App\Service\RentalServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Core\Exception\LockedException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    /**
     * Constructor.
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $passwordHasher, private readonly RentalServiceInterface $rentalService)
    {
    }
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }


        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }


    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(
        '/register',
        name: 'register',
        methods: 'GET|POST',
    )]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

            $user->setRoles([UserRole::ROLE_USER->value]);
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('app_login');
        }

        return $this->render(
            'security/register.html.twig',
            ['form' => $form->createView()]
        );

    }
    /**
     * Index action.
     *
     *  @param integer $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        '/user',
        name: 'user_index',
        methods: 'GET'
    )]
    public function index(#[MapQueryParameter] int $page=1): Response
    {
        $pagination = $this->userService->getPaginatedList($page);

        return $this->render('security/index.html.twig', ['pagination' => $pagination]);

    }//end index()
    /**
     * Show action.
     *
     * @param User $user User
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'user_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(User $user): Response
    {

        return $this->render('security/show.html.twig', ['user' => $user]);

    }//end show()



    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        'user/{id}/edit',
        name: 'user_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

            $user->setRoles([UserRole::ROLE_USER->value]);
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'security/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user
            ]
        );

    }//end edit()

    /**
     * Profile action.
    */

    #[Route(
        'profile/{id}',
        name: 'user_profile',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function profile (User $user,  #[MapQueryParameter] int $page = 1):Response{

        // Check if the current user can view the profile
        $this->denyAccessUnlessGranted('view_profile', $user);

        // Fetch the books owned by the user
        //$books = $bookRepository->findBy(['owner' => $user]);
        $pagination = $this->rentalService->getPaginatedByStatus($page, $user);


        return $this->render(
            'profile/index.html.twig',
            [
                'user' => $user,
                'pagination' => $pagination,

            ]
        );
    }
    /**
     * Change action.
     *
     * @param Request  $request  HTTP request
     * @param User $user User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        'change/{id}',
        name: 'user_change_data',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function change(Request $request, User $user): Response
    {
        $form = $this->createForm(
            UserType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_change_data', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));

            $user->setRoles([UserRole::ROLE_USER->value]);
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'security/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user
            ]
        );

    }
    #[Route(
        '/user/{id}/set_admin',
        requirements: ['id' => '[1-9]\d*'],
        name: 'set_admin',
        methods: 'GET|PUT'
    )]
    public function setAdmin (User $user):Response{

        $user->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        $this->userService->save($user);

        $this->addFlash(
            'success',
            $this->translator->trans('message.created_successfully')
        );

        return $this->redirectToRoute('user_index');
    }
    #[Route(
        '/user/{id}/revoke_admin',
        requirements: ['id' => '[1-9]\d*'],
        name: 'revoke_admin',
        methods: ['GET', 'PUT']
    )]
    public function revokeAdmin(User $user): Response
    {
        if ($this->userService->isLastAdmin($user)) {
            $this->addFlash(
                'error',
                $this->translator->trans('message.cannot_revoke_last_admin')
            );
            return $this->redirectToRoute('user_index');
        }
        $roles = $user->getRoles();
        $updatedRoles = array_diff($roles, [UserRole::ROLE_ADMIN->value]);
        $user->setRoles($updatedRoles);
        $this->userService->save($user);

        $this->addFlash(
            'success',
            $this->translator->trans('message.role_revoked_successfully')
        );

        return $this->redirectToRoute('user_index');
    }

    #[Route('/user/{id}/block', name: 'user_block')]
    public function blockUser(User $user): Response
    {
        $user->setBlocked(true);
        $this->userService->save($user);

        $this->addFlash(
            'success',
            $this->translator->trans('message.user_blocked_successfully')
        );

        return $this->redirectToRoute('user_index');
    }

    #[Route('/user/{id}/unblock', name: 'user_unblock')]
    public function unblockUser(User $user): Response
    {
        $user->setBlocked(false);
        $this->userService->save($user);

        $this->addFlash(
            'success',
            $this->translator->trans('message.user_unblocked_successfully')
        );

        return $this->redirectToRoute('user_index');
    }


}
