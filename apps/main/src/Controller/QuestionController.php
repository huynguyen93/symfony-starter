<?php

namespace App\Controller;

use App\Enum\LanguageCodes;
use App\Repository\QuestionRepository;
use App\Security\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QuestionController extends AbstractController
{
    public function __construct(
        private Security $security,
        private QuestionRepository $questionRepository
    ) {
    }

    #[Route(path: '/questions', name: 'app.question.list')]
    public function listQuestions(Request $request): Response
    {
        $selectedTargetLanguage = $request->query->get('targetLanguageCode');
        $selectedExpectedLanguages = $request->get('expectedLanguageCodes', []);

        $questions = $this->questionRepository->buildSearchQuery(
            $selectedTargetLanguage,
            $selectedExpectedLanguages,
        );

        return $this->render('question/list.html.twig', [
            'questions' => $questions,
            'languageCodes' => LanguageCodes::getAll(),
            'selectedTargetLanguage' => $selectedTargetLanguage,
            'selectedExpectedLanguages' => $selectedExpectedLanguages,
        ]);
    }

    public function createQuestion(): RedirectResponse|Response
    {
        if (!$this->security->getUser()) {
            return $this->redirectToRoute('app.security.login');
        }

        return $this->render('question/create.html.twig');
    }
}