<?php

declare(strict_types=1);

namespace Ekyna\Component\User\Service\OAuth;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Ekyna\Component\Resource\Factory\ResourceFactoryInterface;
use Ekyna\Component\Resource\Manager\ResourceManagerInterface;
use Ekyna\Component\User\Model\OAuthTokenInterface;
use Ekyna\Component\User\Model\UserInterface;
use Ekyna\Component\User\Repository\OAuthTokenRepositoryInterface;
use Ekyna\Component\User\Repository\UserRepositoryInterface;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use League\OAuth2\Client\Provider\GoogleUser;
use League\OAuth2\Client\Provider\ResourceOwnerInterface;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

/**
 * Class OAuthPassportGenerator
 * @package Ekyna\Component\User\Service\OAuth
 * @author  Étienne Dauvergne <contact@ekyna.com>
 */
class OAuthPassportGenerator
{
    private UserRepositoryInterface       $userRepository;
    private ResourceManagerInterface      $userManager;
    private ResourceFactoryInterface      $userFactory;
    private OAuthTokenRepositoryInterface $oAuthTokenRepository;
    private EntityManagerInterface        $oAuthTokenManager;

    public function __construct(
        UserRepositoryInterface       $userRepository,
        ResourceManagerInterface      $userManager,
        ResourceFactoryInterface      $userFactory,
        OAuthTokenRepositoryInterface $oAuthTokenRepository,
        EntityManagerInterface        $oAuthTokenManager
    ) {
        $this->userRepository = $userRepository;
        $this->userManager = $userManager;
        $this->userFactory = $userFactory;
        $this->oAuthTokenRepository = $oAuthTokenRepository;
        $this->oAuthTokenManager = $oAuthTokenManager;
    }

    public function generate(
        AccessToken           $oAuthToken,
        OAuth2ClientInterface $client,
        string                $clientName,
        bool                  $createUser
    ): Passport {
        $loader = function () use ($oAuthToken, $client, $clientName, $createUser) {
            $oAuthUser = $client->fetchUserFromToken($oAuthToken);

            $id = $oAuthUser->getId();

            // 1) have they logged in before?
            if ($token = $this->findToken($id, $clientName)) {
                return $token->getUser();
            }

            // 2) do we have a matching user by email?
            $email = $this->getEmail($oAuthUser);
            $user = $this->findUser($email, $createUser);

            $tokenClass = $this->oAuthTokenRepository->getClassName();
            /** @var OAuthTokenInterface $token */
            $token = new $tokenClass();
            $token
                ->setUser($user)
                ->setOwner($clientName)
                ->setIdentifier($id)
                ->setHash($oAuthToken->getToken())
                ->setExpiresAt((new DateTime())->setTimestamp($oAuthToken->getExpires()));

            $this->oAuthTokenManager->persist($token);
            $this->oAuthTokenManager->flush();

            return $user;
        };

        return new SelfValidatingPassport(
            new UserBadge($oAuthToken->getToken(), $loader)
        );
    }

    private function findToken(string $id, string $clientName): ?OAuthTokenInterface
    {
        return $this
            ->oAuthTokenRepository
            ->findOneByIdentifier($id, $clientName);
    }

    private function findUser(string $email, bool $create): UserInterface
    {
        if ($user = $this->userRepository->findOneByEmail($email)) {
            return $user;
        }

        if (!$create) {
            throw new UnsupportedUserException('User not found.');
        }

        /** @var UserInterface $user */
        $user = $this->userFactory->create();
        $user
            ->setEmail($email)
            ->setEnabled(true);

        $event = $this->userManager->create($user);
        if ($event->hasErrors()) {
            throw new AuthenticationServiceException('Failed to create user.');
        }

        return $user;
    }

    private function getEmail(ResourceOwnerInterface $oAuthUser): string
    {
        if ($oAuthUser instanceof GoogleUser) {
            return $oAuthUser->getEmail();
        }

        throw new UnsupportedUserException();
    }
}
