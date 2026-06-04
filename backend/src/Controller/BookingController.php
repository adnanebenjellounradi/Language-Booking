<?php

namespace App\Controller;

use App\Document\Booking;
use App\Document\Session;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use DateTime;

class BookingController extends AbstractController
{
    #[Route('/api/bookings', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getUserBookings(DocumentManager $dm): JsonResponse
    {
        $bookings = $dm->getRepository(Booking::class)
            ->findBy(['userId' => $this->getUser()->getId()]);

        $data = [];
        foreach ($bookings as $booking) {
            $session = $dm->getRepository(Session::class)->find($booking->getSessionId());
            if ($session) {
                $data[] = [
                    'id' => $booking->getId(),
                    'sessionId' => $session->getId(),
                    'language' => $session->getLanguage(),
                    'date' => $session->getDate()->format('Y-m-d'),
                    'time' => $session->getTime(),
                    'location' => $session->getLocation(),
                    'bookingDate' => $booking->getBookingDate()->format('Y-m-d H:i:s')
                ];
            }
        }

        return $this->json($data);
    }

    #[Route('/api/sessions/{id}/book', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function bookSession(string $id, DocumentManager $dm): JsonResponse
    {
        $session = $dm->getRepository(Session::class)->find($id);
        
        if (!$session) {
            return $this->json(['message' => 'Session not found'], Response::HTTP_NOT_FOUND);
        }

        if ($session->getAvailableSeats() <= 0) {
            return $this->json(['message' => 'No available seats'], Response::HTTP_BAD_REQUEST);
        }

        $existingBooking = $dm->getRepository(Booking::class)
            ->findOneBy([
                'sessionId' => $id,
                'userId' => $this->getUser()->getId()
            ]);

        if ($existingBooking) {
            return $this->json(['message' => 'You have already booked this session'], Response::HTTP_CONFLICT);
        }

        $booking = new Booking();
        $booking->setSessionId($id);
        $booking->setUserId($this->getUser()->getId());
        $booking->setBookingDate(new DateTime());

        $session->setAvailableSeats($session->getAvailableSeats() - 1);

        $dm->persist($booking);
        $dm->flush();

        return $this->json(['message' => 'Session booked successfully'], Response::HTTP_CREATED);
    }

    #[Route('/api/bookings/{id}', methods: ['DELETE'])]
    #[IsGranted('ROLE_USER')]
    public function cancelBooking(string $id, DocumentManager $dm): JsonResponse
    {
        $booking = $dm->getRepository(Booking::class)->find($id);

        if (!$booking) {
            return $this->json(['message' => 'Booking not found'], Response::HTTP_NOT_FOUND);
        }

        if ($booking->getUserId() !== $this->getUser()->getId()) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_FORBIDDEN);
        }

        $session = $dm->getRepository(Session::class)->find($booking->getSessionId());
        if ($session) {
            $session->setAvailableSeats($session->getAvailableSeats() + 1);
        }

        $dm->remove($booking);
        $dm->flush();

        return $this->json(['message' => 'Booking cancelled successfully']);
    }
}