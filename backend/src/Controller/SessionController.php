<?php

namespace App\Controller;

use App\Document\Session;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use DateTime;

class SessionController extends AbstractController
{
    #[Route('/api/sessions', methods: ['GET'])]
    public function getSessions(Request $request, DocumentManager $dm): JsonResponse
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);
        $skip = ($page - 1) * $limit;

        $sessions = $dm->getRepository(Session::class)
            ->createQueryBuilder()
            ->skip($skip)
            ->limit($limit)
            ->getQuery()
            ->execute();

        $total = $dm->getRepository(Session::class)->createQueryBuilder()
            ->getQuery()
            ->execute()
            ->count();

        $data = [];
        foreach ($sessions as $session) {
            $data[] = [
                'id' => $session->getId(),
                'language' => $session->getLanguage(),
                'date' => $session->getDate()->format('Y-m-d'),
                'time' => $session->getTime(),
                'location' => $session->getLocation(),
                'availableSeats' => $session->getAvailableSeats()
            ];
        }

        return $this->json([
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => ceil($total / $limit)
        ]);
    }

    #[Route('/api/sessions', methods: ['POST'])]
    public function createSession(Request $request, DocumentManager $dm): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $session = new Session();
        $session->setLanguage($data['language']);
        $session->setDate(new DateTime($data['date']));
        $session->setTime($data['time']);
        $session->setLocation($data['location']);
        $session->setAvailableSeats($data['availableSeats']);

        $dm->persist($session);
        $dm->flush();

        return $this->json(['message' => 'Session created successfully'], Response::HTTP_CREATED);
    }
}