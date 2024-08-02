<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class UserController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager
    ) {
    }

    #[Route('/user', name: 'app_user', methods: ['POST'])]
    public function index(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = new User();
        $user->setFirstname($request->request->get('firstname'))
            ->setLastname($request->request->get('lastname'))
            ->setEmail($request->request->get('email'))
            ->setAddress($request->request->get('address'))
            ->setPostcode($request->request->get('postcode'))
            ->setCountry($request->request->get('country'));

        // hash the password (based on the security.yaml config for the $user class)
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $request->request->get('password')
        );
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user);
    }

    // #[IsGranted('ROLE_ADMIN')]
    // #[Route('/user/{id}', name: "update_user", methods: ["PUT"])]
    // public function updateUser(int $id)
    // {
    // }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/user/{id}', name: "delete_user", methods: ["DELETE"])]
    public function deleteUser(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if (!$user) return $this->json('Aucun user n\'a cet id.');
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json('Le user a été supprimé');
    }
}
