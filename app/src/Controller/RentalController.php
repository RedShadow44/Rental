<?php
/**
 * Rental controller.
 */

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Rental;
use App\Form\Type\BookType;
use App\Service\BookServiceInterface;
use App\Service\RentalServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
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
     */
    public function __construct(private readonly RentalServiceInterface $rentalService,private readonly BookServiceInterface $bookService, private readonly TranslatorInterface $translator)
    {

    }//end __construct()


    /**
     * @param Request $request
     * @param Book $book
     * @return Response
     */
    #[IsGranted('RENT', subject: 'book')]
    #[Route('/{id}/rent', name: 'rent', requirements: ['id' => '[1-9]\d*'], methods: 'GET|POST')]
    #[IsGranted('ROLE_USER')]
    public function rent(Request $request, Book $book): Response
    {
        $rental = new Rental();

        $user = $this->getUser();
        $rental->setOwner($user);

        $rental->setBook($book);
        $rental->setStatus(false);

        $book->setAvailable(false);

        $this->rentalService->save($rental);
        $this->bookService->save($book);

        $this->addFlash(
            'success',
            $this->translator->trans('message.rented_successfully')
        );

        $id= $request->get('id');

        return $this->redirectToRoute(
            'book_show',
            [
                'id'=>$id
            ]
        );

    }//end rent()


    /**
     * Index action.
     *
     *  @param integer $page Page number
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
     * Approve action.
     *
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/rent_approve', name: 'rent_approve', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[IsGranted('ROLE_ADMIN')]
    public function approve(Request $request, Rental $rental): Response
    {

        $user = $this->getUser();

        $rental->setStatus(true);

        $book = $rental->getBook();
        $book->setOwner($user);

        $this->rentalService->save($rental);
        $this->bookService->save($book);

        $this->addFlash(
            'success',
            $this->translator->trans('message.rental_approved')
        );

        return $this->redirectToRoute('rent_index');
    }
}//end class