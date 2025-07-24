<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Repository\FeedbackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Response;

#[Route('/feedback')]
class FeedbackController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private FeedbackRepository     $feedbackRepository,
        private SerializerInterface    $serializer,
        private ValidatorInterface     $validator
    )
    {
    }

    /**
     * Handles POST /feedback to submit new feedback.
     */
    #[Route('', name: 'feedback_submit', methods: ['POST'])]
    public function submit(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($data === null) {
            return new JsonResponse(['error' => 'Invalid JSON body'], Response::HTTP_BAD_REQUEST);
        }

        $feedback = new Feedback();
        $feedback->setName($data['name'] ?? null);
        $feedback->setEmail($data['email'] ?? null);
        $feedback->setMessage($data['message'] ?? null);

        // Validate the Feedback entity
        $errors = $this->validator->validate($feedback);

        if (count($errors) > 0) {
            $errorMessages = [];
            foreach ($errors as $error) {
                $errorMessages[$error->getPropertyPath()] = $error->getMessage();
            }
            return new JsonResponse(['errors' => $errorMessages], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->persist($feedback);
        $this->entityManager->flush();

        // Return a success response with the created feedback's ID
        return new JsonResponse(
            ['status' => 'success', 'message' => 'Feedback submitted successfully', 'id' => $feedback->getId()],
            Response::HTTP_CREATED
        );
    }

    /**
     * Handles GET /feedback to retrieve all feedback submissions.
     */
    #[Route('', name: 'feedback_list', methods: ['GET'])]
    public function list(): JsonResponse
    {
        $feedbacks = $this->feedbackRepository->findAll();

        // Serialize the feedback objects to JSON using 'feedback_list' group
        $jsonContent = $this->serializer->serialize($feedbacks, 'json', ['groups' => 'feedback_list']);

        // Return the JSON response
        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true); // true for raw JSON string
    }
}
