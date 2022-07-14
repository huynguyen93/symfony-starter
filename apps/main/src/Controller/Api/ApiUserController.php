<?php

namespace App\Controller\Api;

use App\Entity\User;
use App\Model\FormSignUp;
use App\Repository\UserRepository;
use App\Security\LoginFormAuthenticator;
use App\Security\Security;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiUserController extends AbstractController
{
    public function __construct(
        private Security $security,
        private UserAuthenticatorInterface $userAuthenticator,
        private LoginFormAuthenticator $loginFormAuthenticator,
        private ObjectNormalizer $objectNormalizer,
        private EntityManagerInterface $entityManager,
        private UserRepository $userRepository,
        private ValidatorInterface $validator,
    ) {
    }

    #[Route('/api/check-email-exists', name: 'app.api.user.check_email_exists')]
    public function checkEmailExists(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $email = $data['email'] ?? null;

        $user = $this->userRepository->findOneByEmail($email);

        return new JsonResponse([
            'exists' => $user instanceof User,
        ]);
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/api/register', name: 'app.api.user.register')]
    public function register(Request $request): JsonResponse
    {
        /** @var FormSignUp $formSignUp */
        $formSignUp = $this->objectNormalizer->denormalize($request->toArray(), FormSignUp::class);

        $violations = $this->validator->validate($formSignUp);
        if ($violations->count()) {
            throw new BadRequestHttpException();
        }

        $existingUser = $this->userRepository->findOneByEmail($formSignUp->email);
        if ($existingUser) {
            throw new BadRequestHttpException();
        }

        $user = new User();
        $user->setDisplayName($formSignUp->displayName);
        $user->setEmail($formSignUp->email);
        $user->setPassword($formSignUp->password);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $this->userAuthenticator->authenticateUser(
            $user,
            $this->loginFormAuthenticator,
            $request,
            [new RememberMeBadge()]
        );

        return new JsonResponse();
    }

    #[Route('/api/change-password', name: 'app.api.user.change_password')]
    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->toArray();
        $newPassword = $data['newPassword'] ?? null;

        if (!$newPassword) {
            throw new BadRequestHttpException();
        }

        $user = $this->security->getUser();
        $user->setPassword($newPassword);

        $this->entityManager->flush();

        return new JsonResponse();
    }
}