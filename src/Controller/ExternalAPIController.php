<?php

namespace App\Controller;

use App\Form\ExternalAPIType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ExternalAPIController extends AbstractController
{
    #[Route('/externalapi', name: 'app_external_api')]
    public function index(Request $request, HttpClientInterface $httpClient): Response
    {
        $form = $this->createForm(ExternalAPIType::class, [
            'latitude' => '',
            'longitude' => '',
        ]);
        $form->handleRequest($request);

        $weatherData = null;
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $apiUrl = sprintf(
                'https://api.open-meteo.com/v1/forecast?latitude=%s&longitude=%s&daily=temperature_2m_max,temperature_2m_min,precipitation_sum&timezone=UTC',
                $data['latitude'],
                $data['longitude'],
            );

            $weatherData = $httpClient->request('GET', $apiUrl)->toArray();
        }

        return $this->render('external_api/index.html.twig', [
            'form' => $form,
            'weather_data' => $weatherData,
        ]);
    }
}
