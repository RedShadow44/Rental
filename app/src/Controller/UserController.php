<?php
/**
 * User controller.
 */

namespace App\Controller;

use App\Entity\Enum\UserRole;
use App\Entity\User;
use App\Form\Type\PasswdType;
use App\Form\Type\UserType;
use App\Form\Type\RegisterType;
use App\Service\RentalServiceInterface;
use App\Service\UserServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * Class UserController.
 */
class UserController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param UserServiceInterface        $userService    User service
     * @param TranslatorInterface         $translator     Translator
     * @param UserPasswordHasherInterface $passwordHasher Password hasher
     * @param RentalServiceInterface      $rentalService  Rental service
     */
    public function __construct(private readonly UserServiceInterface $userService, private readonly TranslatorInterface $translator, private readonly UserPasswordHasherInterface $passwordHasher, private readonly RentalServiceInterface $rentalService)
    {
    }

    /**
     * Register action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/register',
        name: 'register',
        methods: 'GET|POST',
    )]
    public function register(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
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
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        '/user',
        name: 'user_index',
        methods: 'GET'
    )]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->userService->getPaginatedList($page);

        return $this->render('security/index.html.twig', ['pagination' => $pagination]);
    }

    // end index()
    /**
     * Show action.
     *
     * @param User $user User
     *
     * @return Response HTTP response
     */
    #[Route(
        '/user/{id}',
        name: 'user_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(User $user): Response
    {
        return $this->render('security/show.html.twig', ['user' => $user]);
    }// end show()

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
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
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.changed_successfully')
            );

            $id = $request->get('id');

            return $this->redirectToRoute(
                'user_index',
                [
                    'id' => $id,
                ]
            );
        }

        return $this->render(
            'security/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }// end edit()

    /**
     * Edit password action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        'user/{id}/edit/pass',
        name: 'user_edit_pass',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function editPass(Request $request, User $user): Response
    {
        $form = $this->createForm(
            PasswdType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_edit_pass', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $form->get('plain_password')->getNormData()));

            // $user->setRoles([UserRole::ROLE_USER->value]);
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.changed_successfully')
            );

            $id = $request->get('id');

            return $this->redirectToRoute(
                'user_index',
                [
                    'id' => $id,
                ]
            );
        }

        return $this->render(
            'security/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Profile.
     *
     * @param User $user User entity
     * @param int  $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        'profile/{id}',
        name: 'user_profile',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function profile(User $user, #[MapQueryParameter] int $page = 1): Response
    {
        $this->denyAccessUnlessGranted('view_profile', $user);

        $owner = $this->getUser()->getId();
        $pagination = $this->rentalService->getPaginatedByOwner($page, $owner);

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
     * @param Request $request HTTP request
     * @param User    $user    User entity
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
            // $user->setRoles([UserRole::ROLE_USER->value]);
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.changed_successfully')
            );

            $id = $request->get('id');

            return $this->redirectToRoute(
                'user_profile',
                [
                    'id' => $id,
                ]
            );
        }

        return $this->render(
            'security/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Change password action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        'change/pass/{id}',
        name: 'user_change_pass',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function changePass(Request $request, User $user): Response
    {
        $form = $this->createForm(
            PasswdType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_change_pass', ['id' => $user->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $form->get('plain_password')->getNormData()));

            // $user->setRoles([UserRole::ROLE_USER->value]);
            $this->userService->save($user);

            $this->addFlash(
                'success',
                $this->translator->trans('message.changed_successfully')
            );

            $id = $request->get('id');

            return $this->redirectToRoute(
                'user_profile',
                [
                    'id' => $id,
                ]
            );
        }

        return $this->render(
            'security/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Set admin action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/user/{id}/set_admin',
        requirements: ['id' => '[1-9]\d*'],
        name: 'set_admin',
        methods: 'GET|PUT'
    )]
    public function setAdmin(Request $request, User $user): Response
    {
        //        $user->setRoles([UserRole::ROLE_USER->value, UserRole::ROLE_ADMIN->value]);
        //        $this->userService->save($user);
        //
        //        $this->addFlash(
        //            'success',
        //            $this->translator->trans('message.created_successfully')
        //        );
        //
        //        return $this->redirectToRoute('user_index');
        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('set_admin', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/promote.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Revoke admin action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/user/{id}/revoke_admin',
        requirements: ['id' => '[1-9]\d*'],
        name: 'revoke_admin',
        methods: ['GET', 'PUT']
    )]
    public function revokeAdmin(Request $request, User $user): Response
    {
        if ($this->userService->isLastAdmin($user)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.cannot_revoke_last_admin')
            );

            return $this->redirectToRoute('user_index');
        }
        //        $roles = $user->getRoles();
        //        $updatedRoles = array_diff($roles, [UserRole::ROLE_ADMIN->value]);
        //        $user->setRoles($updatedRoles);
        //        $this->userService->save($user);
        //
        //        $this->addFlash(
        //            'success',
        //            $this->translator->trans('message.role_revoked_successfully')
        //        );
        //
        //        return $this->redirectToRoute('user_index');
        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('revoke_admin', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setRoles(['ROLE_USER']);
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/demote.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Block user action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/user/{id}/block', name: 'user_block')]
    public function blockUser(Request $request, User $user): Response
    {
        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_block', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setBlocked(true);
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.user_blocked_successfully')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/block.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }

    /**
     * Unblock user action.
     *
     * @param Request $request HTTP request
     * @param User    $user    User entity
     *
     * @return Response HTTP response
     */
    #[Route('/user/{id}/unblock', name: 'user_unblock')]
    public function unblockUser(Request $request, User $user): Response
    {
        $form = $this->createForm(
            FormType::class,
            $user,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('user_unblock', ['id' => $user->getId()]),
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setBlocked(false);
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.user_unblocked_successfully')
            );

            return $this->redirectToRoute('user_show', ['id' => $user->getId()]);
        }

        return $this->render(
            'security/unblock.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}
