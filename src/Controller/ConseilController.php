<?php

namespace App\Controller;

use App\Repository\ConseilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ConseilController extends AbstractController
{

    public function __construct(
        private HttpClientInterface $client,
        private ConseilRepository $conseilRepository,
    ) {
    }

    public function fetchWeatherApi(int $zip): array
    {

        //cacher app id dans env
        $latLong = $this->client->request(
            'GET',
            'https://api.openweathermap.org/geo/1.0/zip?zip=' . $zip . ',FR&appid=' . $this->getParameter('app.weather_api')
        );

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
    public function conseils(): Response
    {
        $month = date('m');
        // // a changer le zipcode
        // $this->fetchWeatherApi(37100);

        $conseil = $this->conseilRepository->findBy([
            'month' => $month
        ]);

        return $this->json(data: [
            'conseil' => $conseil,
        ], context: ['groups' => ['list_conseil']]);
    }
}
