<?php

declare(strict_types=1);


namespace Gems\OAuth2\Grant;

use Gems\OAuth2\Entity\MfaCodeEntityInterface;
use DateInterval;
use DateTimeImmutable;
use Gems\OAuth2\Entity\User;
use Gems\OAuth2\Repository\MfaCodeRepository;
use Gems\OAuth2\Repository\MfaCodeRepositoryInterface;
use Gems\OAuth2\Repository\RefreshTokenRepository;
use Gems\OAuth2\Repository\UserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Additions to the original password grant to support MFA
 *
 * Class PasswordGrant
 * @package Auth\Grant
 */
class PasswordGrant extends \League\OAuth2\Server\Grant\PasswordGrant
{

    public function __construct(UserRepository $userRepository, RefreshTokenRepository $refreshTokenRepository,
                                protected MfaCodeRepository $mfaCodeRepository, protected DateInterval $mfaCodeTTL)
    {
        parent::__construct($userRepository, $refreshTokenRepository);
    }

    /**
     * @return MfaCodeRepository
     */
    public function getMfaCodeRepository(): MfaCodeRepository
    {
        return $this->mfaCodeRepository;
    }

    /**
     * Issue an auth code.
     *
     * @param DateInterval           $mfaCodeTTL
     * @param ClientEntityInterface  $client
     * @param User                   $user
     * @param string            $challengeType
     * @param ScopeEntityInterface[] $scopes
     *
     * @throws OAuthServerException
     * @throws UniqueTokenIdentifierConstraintViolationException
     *
     * @return MfaCodeEntityInterface
     */
    protected function issueMfaCode(
        DateInterval $mfaCodeTTL,
        ClientEntityInterface $client,
        User $user,
        $challengeType,
        array $scopes = []
    ): ?MfaCodeEntityInterface
    {
        $maxGenerationAttempts = self::MAX_RANDOM_TOKEN_GENERATION_ATTEMPTS;

        $mfaCode = $this->mfaCodeRepository->getNewMfaCode();
        $mfaCode->setExpiryDateTime((new DateTimeImmutable())->add($mfaCodeTTL));
        $mfaCode->setClient($client);
        $mfaCode->setUser($user);
        $mfaCode->setAuthMethod($challengeType);

        foreach ($scopes as $scope) {
            $mfaCode->addScope($scope);
        }

        while ($maxGenerationAttempts-- > 0) {
            $mfaCode->setIdentifier($this->generateUniqueIdentifier());
            try {
                $this->mfaCodeRepository->persistNewMfaCode($mfaCode);

                return $mfaCode;
            } catch (UniqueTokenIdentifierConstraintViolationException $e) {
                if ($maxGenerationAttempts === 0) {
                    throw $e;
                }
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        DateInterval $accessTokenTTL
    ) {
        // Validate request
        $client = $this->validateClient($request);
        $scopes = $this->validateScopes($this->getRequestParameter('scope', $request, $this->defaultScope));
        $user = $this->validateUser($request, $client);

        // Finalize the requested scopes
        $finalizedScopes = $this->scopeRepository->finalizeScopes($scopes, $this->getIdentifier(), $client, $user->getIdentifier());

        /* if ($user->has2fa()) {
            // submit MFA

            try {
                $this->userRepository->sendOtp($user);
            } catch (ThrottleException $e) {
                throw new OAuthServerException($e->getMessage(), 41, 'throttle', 429);
            } catch (AuthException $e) {
                throw OAuthServerException::serverError('MFA token could not be sent');
            }

            $challengeType = $this->userRepository->get2faMethod($user);

            // Generate mfa-token
            $mfaCode = $this->issueMfaCode(
                $this->mfaCodeTTL,
                $client,
                $user,
                $challengeType,
                $finalizedScopes
            );

            $payload = [
                'client_id'             => $mfaCode->getClient()->getIdentifier(),
                'mfa_code_id'           => $mfaCode->getIdentifier(),
                'scopes'                => $mfaCode->getScopes(),
                'user_id'               => $mfaCode->getUserIdentifier(),
                'expire_time'           => (new DateTimeImmutable())->add($this->mfaCodeTTL)->getTimestamp(),
            ];

            $jsonPayload = \json_encode($payload);

            if ($jsonPayload === false) {
                throw new LogicException('An error was encountered when JSON encoding the mfa request response');
            }

            // send mfa-token and mfa-type as response
            $payload = [
                'error'                 => 'mfa_required',
                'error_description'     => 'Multifactor authentication required',
                'challenge_type'        => $challengeType,
                'mfa_token'             => $this->encrypt($jsonPayload),
                'expire_time'           => (new DateTimeImmutable())->add($this->mfaCodeTTL)->getTimestamp(),
            ];

            $response = new MfaResponse();
            $response->setResponseBody($payload);

            return $response;
        }*/

        // Issue and persist new access token
        $accessToken = $this->issueAccessToken($accessTokenTTL, $client, $user->getIdentifier(), $finalizedScopes);
        $this->getEmitter()->emit(new RequestEvent(RequestEvent::ACCESS_TOKEN_ISSUED, $request));
        $responseType->setAccessToken($accessToken);

        // Issue and persist new refresh token if given
        $refreshToken = $this->issueRefreshToken($accessToken);

        if ($refreshToken !== null) {
            $this->getEmitter()->emit(new RequestEvent(RequestEvent::REFRESH_TOKEN_ISSUED, $request));
            $responseType->setRefreshToken($refreshToken);
        }

        return $responseType;
    }

    /**
     * @param MfaCodeRepository $mfaCodeRepository
     */
    public function setMfaCodeRepository(MfaCodeRepository $mfaCodeRepository): void
    {
        $this->mfaCodeRepository = $mfaCodeRepository;
    }
}
