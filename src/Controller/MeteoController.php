<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MeteoController extends AbstractController
{
    public function __construct(
        private HttpClientInterface $client,
    ) {
    }

    public function fetchWeatherApi(string $city): array
    {

        $response = $this->client->request(
            'GET',
            'https://api.openweathermap.org/data/2.5/weather?q=' . $city . '&appid=' . $this->getParameter('app.weather_api')
        );

        if ($response->getStatusCode() === 404) return [
            'error' => 'Ville non trouvée'
        ];

        return $response->toArray();
    }

    #[Route('/meteo/{city}', name: 'app_meteo_city')]
    public function meteoCity(string $city): Response
    {

        $meteo = $this->fetchWeatherApi($city);

        return $this->json(data: [
            'meteo' => $meteo,
        ]);
    }
    #[Route('/meteo', name: 'app_meteo')]
    public function meteo(): Response
    {   
        /**@var $user User */
        $user = $this->getUser();
        $city = $user->getCountry();
        $meteo = $this->fetchWeatherApi($city);

        return $this->json(data: [
            'meteo' => $meteo,
        ]);
    }
}
