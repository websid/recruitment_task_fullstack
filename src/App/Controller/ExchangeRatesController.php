<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\NbpClient;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRatesController extends AbstractController
{
    /** @var NbpClient */
    private $client;

    public function __construct(NbpClient $client)
    {
        $this->client = $client;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $response = $this->client->getRates(
                $request->query->get('date', date('Y-m-d'))
            );
        } catch (Exception $e) {
            return new JsonResponse(
                ['error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return new JsonResponse($response);
    }
}
