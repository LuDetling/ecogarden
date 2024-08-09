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
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{

    public function __construct(
        private UserRepository $userRepository,
        private EntityManagerInterface $entityManager,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route('/user', name: 'app_user', methods: ['POST'])]
    public function index(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $user->getPassword()
        );
        $user->setPassword($hashedPassword);

        $errors = $this->validator->validate($user);
        if ($errors->count() > 0) {
            return $this->json($errors);
        }
        
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->json($user);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/user/{id}', name: "update_user", methods: ["PUT"])]
    public function updateUser(User $user, Request $request): Response
    {
        if (!$user) return $this->json('Pas de utilisateur trouvé à cet id', 200);

        $updatedUser = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
        );

        $errors = $this->validator->validate($updatedUser);
        if($errors->count() > 0){
            return $this->json($errors);
        }

        $this->entityManager->persist($updatedUser);
        $this->entityManager->flush();
        return $this->json([
            "message" => "L'utilisateur a bien été modifié."
        ], 204);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/user/{id}', name: "delete_user", methods: ["DELETE"])]
    public function deleteUser(int $id): Response
    {
        $user = $this->userRepository->find($id);
        if (!$user) return $this->json('Aucun utilisateur n\'a cet id.');
        $this->entityManager->remove($user);
        $this->entityManager->flush();

        return $this->json('Le user a été supprimé');
    }
}
