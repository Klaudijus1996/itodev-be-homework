<?php
namespace App\Controller\Api\V1;

use App\Dto\RegisterEventRequest;
use App\Entity\Event;
use App\Entity\EventRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/v1/events', name: 'api_events_')]
class EventController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $em,
    ) {
    }

    // TODO: this method now only returns available events.
    //  Available events should be returned by guest controller.
    //  Events returned in here should be able to be filtered, sorted, searched, etc.
    //  Good enough for now.
    #[Route("", name: "index", methods: ["GET"])]
    public function index(): JsonResponse
    {
        // TODO: Define a filter which would alter builder by url query params
        $page = 1; // Default page
        if (isset($_GET['page']) && is_numeric($_GET['page'])) {
            $page = (int)$_GET['page'];
        }
        $limit = 10; // Default limit
        if (isset($_GET['limit']) && is_numeric($_GET['limit'])) {
            $limit = (int)$_GET['limit'];
        }

        $events = $this
            ->em
            ->getRepository(Event::class)
            ->findAvailable(limit: $limit, offset: ($page - 1) * $limit);

        return $this->json([
            'data' => $events
        ], 200, [], ['groups' => ['event:index']]);
    }

    #[Route("/{id}/register", name: "register", methods: ["POST"])]
    public function register(
        int $id,
        #[MapRequestPayload] RegisterEventRequest $data,
    ): JsonResponse {
        // TODO: findOrFail method
        $event = $this->em->getRepository(Event::class)->find($id);
        if (!$event) {
            return $this->json(['message' => 'Event not found.'], 404);
        }

        if ($event->getAvailableSpots() <= 0) {
            return $this->json(['message' => 'No spots available.'], 409);
        }

        // 2) Check unique registration per email & event
        $already = $this->em
            ->getRepository(EventRegistration::class)
            ->findOneBy(['event' => $event, 'email' => $data->getEmail()]);

        if ($already) {
            return $this->json(['message' => 'You are already registered.'], 409);
        }

        $registration = new EventRegistration;
        $registration
            ->setEvent($event)
            ->setName($data->getName())
            ->setEmail($data->getEmail());

        $event->setAvailableSpots($event->getAvailableSpots() - 1);

        $this->em->persist($registration);
        $this->em->flush();

        return $this->json([
            'registration_id'  => $registration->getId(),
            'remaining_spots'  => $event->getAvailableSpots(),
        ], 201);
    }
}
