<?php
/**
 * Record controller.
 */

namespace App\Controller;

use App\Repository\RecordRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class RecordController.
 */
#[Route('/record')]
class RecordController extends AbstractController
{
    #[Route(
        name: 'record_index',
        methods: 'GET'
    )]
    /**
     * Index action.
     *
     * @param RecordRepository $repository Record repository
     *
     * @return Response HTTP response
     */
    public function index(RecordRepository $repository): Response
    {
        $records = $repository->findAll();

        return $this->render(
            'record/index.html.twig',
            ['records' => $records]
        );
    }

    #[Route(
        '/{id}',
        name: 'record_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET'
    )]
    /**
     * Show action.
     *
     * @param RecordRepository $repository Record repository
     * @param int              $id         Record identifier
     *
     * @return Response HTTP response
     */
    public function show(RecordRepository $repository, int $id): Response
    {
        $record = $repository->findOneById($id);

        return $this->render(
            'record/show.html.twig',
            ['record' => $record]
        );
    }
}
