<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ConseilController extends AbstractController
{

    public function __construct(
        private HttpClientInterface $client,
    ) {
    }
    public function fetchWeatherApi(int $zip): array
    {

        //cacher app id dans env
        $latLong = $this->client->request(
            'GET',
            'https://api.openweathermap.org/geo/1.0/zip?zip=' . $zip . ',FR&appid=' . $this->getParameter('app.weather_api')
        );

        // $statusCode = $response->getStatusCode();
        // $contentType = $response->getHeaders()['content-type'][0];
        // $content = $response->getContent();
        $content = $latLong->toArray();
        $lat = $content["lat"];
        $lon = $content["lon"];

        $response = $this->client->request(
            'GET',
            'https://api.openweathermap.org/data/2.5/weather?lat=' . $lat . '&lon=' . $lon . '&appid=' . $this->getParameter('app.weather_api')
        );

        return $response->toArray();
    }

    #[Route('/conseil', name: 'app_conseil')]
    public function index(): Response
    {
        $month = date('m');
        // a changer le zipcode
        $this->fetchWeatherApi(37100);

        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/ConseilController.php',
        ]);
    }
}
