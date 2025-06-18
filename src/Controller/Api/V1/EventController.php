<?php
namespace App\Controller\Api\V1;

use App\Dto\EventRegistrationResponseDto;
use App\Dto\EventResponseDto;
use App\Dto\RegisterEventRequest;
use App\Entity\Event;
use App\Entity\EventRegistration;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Annotation\Route;

// TODO: Api documentation
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
        $orderBy = [];
        if (!empty($_GET['sorts']) && is_array($_GET['sorts'])) {
            foreach ($_GET['sorts'] as $sort) {
                $desc = $sort['desc'];
                switch (true) {
                    case is_string($desc):
                        $desc = strtolower($desc) === 'true';
                        break;
                    case is_bool($desc):
                        break;
                    case is_null($desc):
                        $desc = false;
                        break;
                    case is_numeric($desc):
                        $desc = (bool)$desc;
                        break;
                    default:
                        $desc = false;
                }

                $orderBy[$sort['id']] = $desc ? 'DESC' : 'ASC';
            }
        }

        $events = $this
            ->em
            ->getRepository(Event::class)
            ->findAvailable($orderBy, limit: $limit, offset: ($page - 1) * $limit, search: $_GET['search'] ?? null);

        $data = EventResponseDto::fromCollection($events);

        return $this->json([
            'items' => $data,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $this->em->getRepository(Event::class)->countAvailable(),
            ],
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

        $data = EventRegistrationResponseDto::make($registration);

        return $this->json($data, 201);
    }
}
