<?php

namespace App\Controller;

use App\Form\TextInputType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TextInputController extends AbstractController
{
    #[Route('/text/input', name: 'app_text_input')]
    public function index(Request $request): Response
    {
        $form = $this->createForm(TextInputType::class, ['text' => '']);
        $form->handleRequest($request);

        $submittedText = '';
        if ($form->isSubmitted() && $form->isValid()) {
            $submittedText = $form->getData()['text'];
        }

        return $this->render('text_input/index.html.twig', [
            'form' => $form,
            'submittedText' => $submittedText,
        ]);
    }
}
