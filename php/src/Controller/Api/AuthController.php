<?php

namespace App\Controller\Api;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AuthController extends ApiController {
    /**
     * @Route("/login", name="Login for API access", methods={"POST"})
     * @param UserInterface $user
     * @param JWTTokenManagerInterface $JWTManager
     *
     * @return JsonResponse
     */
    public function getTokenUser(UserInterface $user, JWTTokenManagerInterface $JWTManager) {
        return $this->json(['token' => $JWTManager->create($user)]);
    }
}