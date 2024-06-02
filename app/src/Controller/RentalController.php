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
    public function __construct(private readonly RentalServiceInterface $rentalService, private readonly TranslatorInterface $translator)
    {

    }//end __construct()


    /**
     * @param Request $request
     * @param Book $book
     * @return Response
     */
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

}//end class