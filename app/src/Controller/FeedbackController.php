<?php

namespace App\Controller;

use App\Entity\Feedback;
use App\Enum\FeedbackStatus;
use App\Repository\FeedbackRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\FeedbackAddType;

use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Request;

class FeedbackController extends AbstractController
{
    #[Route('/feedback', name: 'app_feedback')]
    public function add(Request $request, FeedbackRepository $feedbackRepository): Response
    {
        $feedback = new Feedback();

        $form = $this->createForm(FeedbackAddType::class, $feedback);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $feedback = $form->getData();

            $feedback->setSentAt(new \DateTimeImmutable);
            $feedback->setStatus(FeedbackStatus::UNPROCESSED->value);

            $feedbackRepository->add($feedback);

            return $this->redirectToRoute('app_feedback');
        }

        return new Response($this->renderForm('feedback/index.html.twig', [
            'feedbackForm' => $form,
        ]));
    }
}