<?php

namespace App\Controller;

use App\Entity\Conseil;
use App\Repository\ConseilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Annotation\Groups;

#[IsGranted("ROLE_USER")]
class ConseilController extends AbstractController
{

    public function __construct(
        private ConseilRepository $conseilRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/conseil', name: 'conseil', methods: 'GET')]
    public function conseils(): Response
    {
        $month = date('m');
        $conseil = $this->conseilRepository->findBy([
            'month' => $month
        ]);

        return $this->json(data: [
            'conseil' => $conseil,
        ], context: ['groups' => ['list_conseil']]);
    }

    #[Route('/conseil/{month}', name: 'app_conseil_month', methods: 'GET')]
    public function conseil(int $month): Response
    {
        $conseil = $this->conseilRepository->findBy([
            'month' => $month
        ]);

        return $this->json(data: [
            'conseil' => $conseil,
        ], context: ['groups' => ['list_conseil']]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/conseil', name: 'create_conseil', methods: 'POST')]
    public function createConseil(Request $request): Response
    {
        $conseil = new Conseil();
        $user = $this->getUser();
        $conseil->setCity($request->query->get('city'))
            ->setDescription($request->query->get('description'))
            ->setMonth($request->query->get('month'))
            ->setUser($user);

        $this->entityManager->persist($conseil);
        $this->entityManager->flush();

        return $this->json(data: ['conseil' => $conseil], context: ['groups' => ['admin_conseil']]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/conseil/{id}', name: 'delete_conseil', methods: 'DELETE')]
    public function deleteConseil(int $id): Response
    {
        $conseil = $this->conseilRepository->find($id);
        if (!$conseil) return $this->json('Pas de conseil trouvé à cet id');

        $this->entityManager->remove($conseil);
        $this->entityManager->flush();

        return $this->json(data: [
            'message' => 'Le conseil a été supprimé',
            'conseil' => $conseil
        ], context: ['groups' => ['admin_conseil']]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/conseil/{id}', name: 'update_conseil', methods: 'PUT')]
    public function updateConseil(int $id, Request $request): Response
    {
        $conseil = $this->conseilRepository->find($id);
        if (!$conseil) return $this->json('Pas de conseil trouvé à cet id');
        
        $content = $request->toArray();
        dd($content);

        return $this->json(data: ['conseil' => $conseil], context: ['groups' => ['admin_conseil']]);
    }
}
