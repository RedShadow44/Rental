<?php
/**
 * Rental controller.
 */

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rental;
use App\Service\BookServiceInterface;
use App\Service\RentalServiceInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class Rental Controller.
 */
class RentalController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param RentalServiceInterface $rentalService Rental service
     * @param BookServiceInterface   $bookService   Book service
     * @param TranslatorInterface    $translator    Translator
     */
    public function __construct(private readonly RentalServiceInterface $rentalService, private readonly BookServiceInterface $bookService, private readonly TranslatorInterface $translator)
    {
    }// end __construct()

    /**
     * Rent action.
     *
     * @param Request $request HTTP request
     * @param Book    $book    Book entity
     *
     * @return Response HTTP response
     */
    #[IsGranted('RENT', subject: 'book')]
    #[Route('/{id}/rent', name: 'rent', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function rent(Request $request, Book $book): Response
    {
        $user = $this->getUser();
        $this->rentalService->rentBook($book, $user);

        $this->addFlash(
            'success',
            $this->translator->trans('message.rented_successfully')
        );

        $id = $request->get('id');

        return $this->redirectToRoute(
            'book_show',
            [
                'id' => $id,
            ]
        );
    }// end rent()

    /**
     * Index action.
     *
     * @param int $page Page number
     *
     * @return Response HTTP response
     */
    #[Route('/rent_index', name: 'rent_index', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('ROLE_ADMIN')]
    public function index(#[MapQueryParameter] int $page = 1): Response
    {
        $pagination = $this->rentalService->getPaginatedByStatus($page);

        return $this->render('rental/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Approve rental.
     *
     * @param Request                $request       HTTP request
     * @param Rental                 $rental        Rental entity
     * @param TranslatorInterface    $translator    Translator
     * @param RentalServiceInterface $rentalService Rental service
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/rent_approve', name: 'rent_approve', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Request $request, Rental $rental, TranslatorInterface $translator, RentalServiceInterface $rentalService): Response
    {
        $form = $this->createFormBuilder()
            ->setMethod(Request::METHOD_PUT)
            ->add('approve', SubmitType::class, [
                'label' => $translator->trans('action.rent'),
                'attr' => ['class' => 'btn btn-outline-primary w-100'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $rentalService->approveRental($rental);

            $this->addFlash(
                'success',
                $this->translator->trans('message.rental_approved')
            );

            return $this->redirectToRoute('rent_index');
        }

        return $this->render('rental/approve.html.twig', [
            'form' => $form->createView(),
            'rental' => $rental,
        ]);
    }

    /**
     * Deny rental.
     *
     * @param Request             $request    HTTP request
     * @param Rental              $rental     Rental entity
     * @param TranslatorInterface $translator Translator
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/rent_deny', name: 'rent_deny', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('ROLE_ADMIN')]
    public function deny(Request $request, Rental $rental, TranslatorInterface $translator): Response
    {
        $form = $this->createFormBuilder()
            ->setMethod(Request::METHOD_PUT)
            ->add('deny', SubmitType::class, [
                'label' => $translator->trans('action.deny'),
                'attr' => ['class' => 'btn btn-outline-primary w-100'],
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book = $rental->getBook();
            $this->bookService->setAvailable($book, true);
            $this->rentalService->delete($rental);
            $this->bookService->save($book);

            $this->addFlash(
                'success',
                $this->translator->trans('message.rental_denied')
            );

            return $this->redirectToRoute('rent_index');
        }

        return $this->render('rental/deny.html.twig', [
            'form' => $form->createView(),
            'rental' => $rental,
        ]);
    }

    /**
     * Return a rented book.
     *
     * @param Request $request HTTP Request
     * @param Rental  $rental  Rental entity
     *
     * @return Response HTTP Response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     */
    #[Route('/{id}/return', name: 'return', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    public function return(Request $request, Rental $rental): Response
    {
        $form = $this->createForm(
            FormType::class,
            $rental,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('return', ['id' => $rental->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->setAvailable($rental->getBook(), true);
            $this->rentalService->delete($rental);

            $this->addFlash(
                'success',
                $this->translator->trans('message.returned_successfully')
            );

            return $this->redirectToRoute(
                'book_index',
                //                [
                //                    'id' => $this->getUser()->getId(),
                //                ]
            );
        }

        return $this->render(
            'rental/return.html.twig',
            [
                'form' => $form->createView(),
                'rental' => $rental,
                //                'user_id' => $this->getUser()->getId(),
            ]
        );
    }
}// end class
