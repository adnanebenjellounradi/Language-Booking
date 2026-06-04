<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    #[Route('/api/register', name: 'app_register', methods: ['POST'])]
    public function register(
        Request $request, 
        DocumentManager $dm, 
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['name']) || !isset($data['email']) || !isset($data['password'])) {
            return $this->json(['message' => 'Name, email and password are required'], 400);
        }
        
        $existingUser = $dm->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        if ($existingUser) {
            return $this->json(['message' => 'User already exists'], 409);
        }
        
        $user = new User();
        $user->setName($data['name']);
        $user->setEmail($data['email']);
        
        // Hasher le mot de passe
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);
        
        $dm->persist($user);
        $dm->flush();
        
        return $this->json(['message' => 'User created successfully'], 201);
    }
    
    #[Route('/api/logincheck', name: 'app_login', methods: ['POST'])]
    public function login(
        Request $request,
        DocumentManager $dm,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ): JsonResponse {
        $data = json_decode($request->getContent(), true);
        
        if (!isset($data['email']) || !isset($data['password'])) {
            return $this->json(['message' => 'Email and password are required'], 400);
        }
        
        $user = $dm->getRepository(User::class)->findOneBy(['email' => $data['email']]);
        
        if (!$user) {
            return $this->json(['message' => 'Invalid credentials'], 401);
        }
        
        if (!$passwordHasher->isPasswordValid($user, $data['password'])) {
            return $this->json(['message' => 'Invalid credentials'], 401);
        }
        
        $token = $jwtManager->create($user);
        
        return $this->json([
            'token' => $token,
            'user' => [
                'id' => $user->getId(),
                'name' => $user->getName(),
                'email' => $user->getEmail()
            ]
        ]);
    }
}