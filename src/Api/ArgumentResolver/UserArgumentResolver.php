<?php

namespace App\Api\ArgumentResolver;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserArgumentResolver implements ArgumentValueResolverInterface
{
    private TokenStorageInterface $tokenStorage;

    private UserRepository $userRepository;

    /**
     * UserArgumentResolver constructor.
     */
    public function __construct(TokenStorageInterface $tokenStorage, UserRepository $userRepository)
    {
        $this->tokenStorage = $tokenStorage;
        $this->userRepository = $userRepository;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        if (User::class !== $argument->getType()) {
            return false;
        }

        $token = $this->tokenStorage->getToken();

        if (!$token instanceof TokenInterface) {
            return false;
        }

        return $token->getUser() instanceof User;
    }

    public function resolve(Request $request, ArgumentMetadata $argument): \Generator
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        yield $this->userRepository->findOneByEmailOrFail($user->getEmail());
    }
}
