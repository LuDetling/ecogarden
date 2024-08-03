<?php

namespace App\Controller;

use App\Entity\Conseil;
use App\Form\ConseilType;
use App\Repository\ConseilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\SerializerInterface;

#[IsGranted("ROLE_USER")]
class ConseilController extends AbstractController
{

    public function __construct(
        private ConseilRepository $conseilRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer
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
        $conseil = $this->serializer->deserialize($request->getContent(), Conseil::class, 'json');
        /**@var $user User */
        $user = $this->getUser();
        $conseil->setUser($user);

        $this->entityManager->persist($conseil);
        $this->entityManager->flush();

        return $this->json(data: [
            'message' => 'Le conseil a été ajouté',
            'conseil' => $conseil
        ], context: ['groups' => ['admin_conseil']]);
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
        $test = $this->serializer->deserialize($request->getContent(), Conseil::class, 'json');

        $form = $this->createForm(ConseilType::class, $conseil);
        $form->handleRequest($request);
        dd($test);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->entityManager->flush();

            return $this->json('Le conseil a été modifié');
        }
        return $this->json([
            'conseil' => $conseil
        ], context: [
            'groups' => ['admin_conseil']
        ]);

        //formtype meme en api

        $content = $request->toArray();
        if (isset($content["description"])) {
            $conseil->setDescription($content["description"]);
        }
        if (isset($content["month"])) {
            $conseil->setMonth($content["month"]);
        }
        if (isset($content["city"])) {
            $conseil->setCity($content["city"]);
        }
        $this->entityManager->flush();

        return $this->json(data: [
            'message' => "Vos données ont bien été modifié",
            'content' => $content
        ], context: ['groups' => ['admin_conseil']]);
    }
}
