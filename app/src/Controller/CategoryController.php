<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\Type\CategoryType;
use App\Service\BookServiceInterface;
use App\Service\CategoryServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class CategoryController.
 */
#[Route('/category')]
class CategoryController extends AbstractController
{


    /**
     * Constructor.
     *
     * @param CategoryServiceInterface $categoryService Category service
     * @param BookServiceInterface     $bookService     Book service
     * @param TranslatorInterface      $translator      Translator
     */
    public function __construct(private readonly CategoryServiceInterface $categoryService, private readonly BookServiceInterface $bookService, private readonly TranslatorInterface $translator)
    {

    }//end __construct()


    /**
     * Index action.
     *
     *  @param integer $page Page number
     *
     * @return Response HTTP response
     */
    #[Route(name: 'category_index', methods: 'GET')]
    public function index(#[MapQueryParameter] int $page=1): Response
    {
        $pagination = $this->categoryService->getPaginatedList($page);

        return $this->render('category/index.html.twig', ['pagination' => $pagination]);

    }//end index()


    /**
     * Show action.
     *
     * @param Category $category Category
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'category_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    public function show(Category $category): Response
    {
        $books = $this->bookService->findBooksForCategory($category);

        return $this->render('category/show.html.twig', ['category' => $category, 'books' => $books]);

    }//end show()


    /**
     * Show books for category action.
     *
     * @return Response HTTP response
     */

    // #[Route(
    // '/{id}/all',
    // name: 'category_all_books',
    // requirements: ['id' => '[1-9]\d*'],
    // methods: 'GET'
    // )]
    // public function showAllBooksForCategory(Category $category): Response
    // {
    // $books = $this->bookService->findBooksForCategory($category);
    //
    // return $this->render('category/showAllBooksForCategory.html.twig', ['books'=>$books]);
    // }


    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'category_create',
        methods: 'GET|POST',
    )]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form     = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );

    }//end create()


    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/edit',
        name: 'category_edit',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|PUT'
    )]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(
            CategoryType::class,
            $category,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('category_edit', ['id' => $category->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->save($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.created_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form'     => $form->createView(),
                'category' => $category,
            ]
        );

    }//end edit()


    /**
     * Delete action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}/delete',
        name: 'category_delete',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET|DELETE'
    )]
    public function delete(Request $request, Category $category): Response
    {
        if (!$this->categoryService->canBeDeleted($category)) {
            $this->addFlash(
                'warning',
                $this->translator->trans('message.category_contains_books')
            );

            return $this->redirectToRoute('book_index');
        }

        $form = $this->createForm(
            FormType::class,
            $category,
            [
                'method' => 'DELETE',
                'action' => $this->generateUrl('category_delete', ['id' => $category->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);

            $this->addFlash(
                'success',
                $this->translator->trans('message.deleted_successfully')
            );

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form'     => $form->createView(),
                'category' => $category,
            ]
        );

    }//end delete()


}//end class
