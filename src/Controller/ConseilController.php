<?php

namespace App\Controller;

use App\Repository\ConseilRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ConseilController extends AbstractController
{

    public function __construct(
        private ConseilRepository $conseilRepository,
    ) {
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

    #[Route('/conseil/{month}', name: 'app_conseil_month')]
    public function conseil(int $month): Response
    {
        $conseil = $this->conseilRepository->findBy([
            'month' => $month
        ]);

        return $this->json(data: [
            'conseil' => $conseil,
        ], context: ['groups' => ['list_conseil']]);
    }
}
