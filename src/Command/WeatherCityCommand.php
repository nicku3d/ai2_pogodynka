<?php

namespace App\Command;

use App\Entity\Measurement;
use App\Service\WeatherUtil;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'weather:city',
    description: 'Display weather for a location by country code and city',
)]
class WeatherCityCommand
{
    public function __construct(private WeatherUtil $weatherUtil)
    {
    }

    public function __invoke(
        SymfonyStyle $io,
        #[Argument('Country code')] string $country,
        #[Argument('City name')] string $city,
    ): int {
        $measurements = $this->weatherUtil->getWeatherForCountryAndCity($country, $city);
        $io->writeln(sprintf('Location: %s, %s', $city, $country));
        $io->table(['Date', 'Temperature'], array_map(static fn (Measurement $measurement) => [
            $measurement->getDate()->format('Y-m-d'),
            $measurement->getCelsius(),
        ], $measurements));

        return Command::SUCCESS;
    }
}
