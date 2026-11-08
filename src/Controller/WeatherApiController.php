<?php

namespace App\Controller;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;

final class WeatherApiController extends AbstractController
{
    #[Route('/api/v1/weather', name: 'app_weather_api')]
    public function index(
        #[MapQueryParameter]string $country,
        #[MapQueryParameter]string $city,
        WeatherUtil $util,
        #[MapQueryParameter] string $format = 'json',
        #[MapQueryParameter('twig')] bool $twig = false,
    ): Response
    {
        $measurements = $util->getWeatherForCountryAndCity($country, $city);
        $measurements = array_map(static fn (Measurement $measurement) => [
            'date' => $measurement->getDate()->format('Y-m-d'),
            'celsius' => $measurement->getCelsius(),
            'fahrenheit' => $measurement->getFahrenheit(),
        ], $measurements);

        if ($twig) {
            $template = sprintf('weather_api/index.%s.twig', $format);
            $contentType = $format === 'csv' ? 'text/plain; charset=UTF-8' : 'application/json';

            return $this->render($template, [
                'city' => $city,
                'country' => $country,
                'measurements' => $measurements,
            ], new Response(headers: ['Content-Type' => $contentType]));
        }

        if ($format === 'csv') {
            $lines = ['city,country,date,celsius,fahrenheit'];
            foreach ($measurements as $measurement) {
                $lines[] = implode(',', [
                    sprintf('"%s"', str_replace('"', '""', $city)),
                    sprintf('"%s"', str_replace('"', '""', $country)),
                    $measurement['date'],
                    $measurement['celsius'],
                    $measurement['fahrenheit'],
                ]);
            }

            return new Response(implode("\r\n", $lines)."\r\n", headers: ['Content-Type' => 'text/plain']);
        }

        return $this->json([
            'city' => $city,
            'country' => $country,
            'measurements' => $measurements,
        ]);
    }
}
