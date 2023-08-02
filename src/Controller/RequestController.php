<?php

namespace App\Controller;

use App\Entity\Question;
use App\Entity\Answer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bridge\Attribute\MapRequestPayload;
use Symfony\Bridge\Attribute\MapQueryString;
use Doctrine\ORM\EntityManagerInterface;

class RequestController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/questions', name: 'add_question', methods: ['POST'])]
    #[MapRequestPayload]
    public function addQuestion(Question $question, ValidatorInterface $validator): Response
    {
        $errors = $validator->validate($question);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new Response($errorsString);
        }

        $this->entityManager->persist($question);
        $this->entityManager->flush();

        return new Response('Saved new question with id '.$question->getId());
    }

    #[Route('/questions/{id}/answers', name:'add_answer', methods:['POST'])]
    #[MapRequestPayload]
    public function addAnswer(Question $question, Answer $answer, ValidatorInterface $validator): Response
    {
        $errors = $validator->validate($answer);

        if (count($errors) > 0) {
            $errorsString = (string) $errors;
            return new Response($errorsString);
        }

        $answer->setQuestion($question);

        $this->entityManager->persist($answer);
        $this->entityManager->flush();

        return new Response('Saved new answer with id '.$answer->getId());
    }

    #[Route('/questions/{id}', name:'get_question', methods: ['GET'])]
    public function getQuestion(Question $question): Response
    {
        return $this->json($question);
    }

    #[Route('/questions', name:'get_questions', methods: ['GET'])]
    public function getQuestions(): Response
    {
        $questions = $this->entityManager->getRepository(Question::class)->findAll();
        return $this->json($questions);
    }
}
