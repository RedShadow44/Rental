<?php
/**
 * Book controller.
 */

namespace App\Controller;

use App\Dto\BookListInputFiltersDto;
use App\Entity\Book;
use App\Form\Type\BookType;
use App\Form\Type\SearchType;
use App\Resolver\BookListInputFiltersDtoResolver;
use App\Service\BookServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class BookController.
 */
#[Route('/book')]
class BookController extends AbstractController
{
    /**
     * Constructor.
     *
     * @param BookServiceInterface $bookService Book service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(private readonly BookServiceInterface $bookService, private readonly TranslatorInterface $translator)
    {
    }// end __construct()

    /**
     * Index action.
     *
     * @param Request                 $request HTTP request
     * @param BookListInputFiltersDto $filters Filters
     * @param int                     $page    Page number
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'book_index',
        methods: 'GET'
    )]
    public function index(Request $request, #[MapQueryString(resolver: BookListInputFiltersDtoResolver::class)] BookListInputFiltersDto $filters, #[MapQueryParameter] int $page = 1): Response
    {
        $form = $this->createForm(
            SearchType::class,
            [
                'method' => 'GET',
            ]
        );
        $form->handleRequest($request);

        $pagination = $this->bookService->getPaginatedList($page, $filters);

        return $this->render('book/index.html.twig', ['pagination' => $pagination, 'form' => $form->createView()]);
    }// end index()

    /**
     * Show action.
     *
     * @param Book $book Book entity
     *
     * @return Response HTTP response
     */
    #[IsGranted('VIEW', subject: 'book')]
    #[Route(
        '/{id}',
        name: 'book_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function show(Book $book): Response
    {
        return $this->render(
            'book/show.html.twig',
            ['book' => $book]
        );
    }// end show()

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route(
        '/create',
        name: 'book_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->save($book);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/create.html.twig',
            ['form' => $form->createView()]
        );
    }// end create()

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Book    $book    Book entity
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route(
        '/{id}/edit',
        name: 'book_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Book $book): Response
    {
        $form = $this->createForm(
            BookType::class,
            $book,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('book_edit', ['id' => $book->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->save($book);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/edit.html.twig',
            [
                'form' => $form->createView(),
                'book' => $book,
            ]
        );
    }// end edit()

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Book    $book    Book entity
     *
     * @return Response HTTP response
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route(
        '/{id}/delete',
        name: 'book_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE'
    )]
    public function delete(Request $request, Book $book): Response
    {
        $form = $this->createForm(
            FormType::class,
            $book,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('book_delete', ['id' => $book->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->bookService->delete($book);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('book_index');
        }

        return $this->render(
            'book/delete.html.twig',
            [
                'form' => $form->createView(),
                'book' => $book,
            ]
        );
    }// end delete()

    /*
     * Rent action
     */

    //    #[Route(
    //        '/{id}/rent',
    //        name: 'book_rent',
    //        requirements: ['id' => '[1-9]\d*'],
    //        methods: 'GET|PUT'
    //    )]
    //    public function rent(Request $request, Book $book): Response
    //    {
    //
    //
    //    }
}// end class
