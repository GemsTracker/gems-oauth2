<?php

declare(strict_types=1);


namespace Gems\OAuth2\Grant;

use Gems\OAuth2\Entity\User;
use Gems\OAuth2\Exception\AuthException;
use Gems\OAuth2\Exception\ThrottleException;
use Gems\OAuth2\Repository\UserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestEvent;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ServerRequestInterface;

class MfaCodeGrant extends PasswordGrant
{
    /**
     * Get the current grant identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return 'mfa_code';
    }

    /**
     * {@inheritdoc}
     */
    public function respondToAccessTokenRequest(
        ServerRequestInterface $request,
        ResponseTypeInterface $responseType,
        \DateInterval $accessTokenTTL
    ) {

        if (!$this->userRepository instanceof UserRepository) {
            throw OAuthServerException::serverError('Incorrect user repository');
        }

        // Validate request
        $client = $this->validateClient($request);

        $encryptedMfaCode = $this->getRequestParameter('mfa_token', $request, null);

        if ($encryptedMfaCode === null) {
            throw OAuthServerException::invalidRequest('mfa_token');
        }

        try {
            $mfaCodePayload = \json_decode($this->decrypt($encryptedMfaCode));

            $userIdentification = $mfaCodePayload->user_id;
            if ($userIdentification === null) {
                throw OAuthServerException::invalidRequest('mfa_token', 'user not found in mfa_token');
            }
            $user = $this->userRepository->getUserByIdentifier($userIdentification);
            if (!$user instanceof User) {
                throw OAuthServerException::invalidRequest('mfa_token', 'user not found in mfa_token');
            }

            $this->validateMfaCode($mfaCodePayload, $client, $user);

            // Finalize the requested scopes
            $finalizedScopes = $this->scopeRepository->finalizeScopes(
                $this->validateScopes($mfaCodePayload->scopes),
                $this->getIdentifier(),
                $client,
                $mfaCodePayload->user_id
            );
        } catch (\LogicException $e) {
            throw OAuthServerException::invalidRequest('maf_token', 'Cannot decrypt the mfa code', $e);
        }

        // Verify OTP
        $otp = $this->getRequestParameter('otp', $request, null);

        if ($otp === null) {
            throw OAuthServerException::invalidRequest('otp');
        }

        // TODO: Add verify for OTP
        /*try {

            //$this->userRepository->verifyOtp($user, $otp);
        } catch (ThrottleException $e) {
            throw new OAuthServerException($e->getMessage(), 41, 'throttle', 429);
        } catch (AuthException $e) {
            throw OAuthServerException::accessDenied($e->getMessage());
        }*/

        /*if (!$this->userRepository->verifyOtp($user, $otp)) {
            throw OAuthServerException::accessDenied('otp code not valid');
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
     * Validate the current MFA code
     *
     * @param object $mfaCodePayload
     * @param ClientEntityInterface $client
     * @param UserEntityInterface $user
     * @throws OAuthServerException
     */
    protected function validateMfaCode(object $mfaCodePayload, ClientEntityInterface $client, UserEntityInterface $user)
    {
        if (!\property_exists($mfaCodePayload, 'mfa_code_id')) {
            throw OAuthServerException::invalidRequest('mfa_token', 'Mfa token malformed');
        }

        if (!\property_exists($mfaCodePayload,'expire_time') || \time() > $mfaCodePayload->expire_time) {
            throw OAuthServerException::invalidRequest('mfa_token', 'Mfa token has expired');
        }

        if ($this->mfaCodeRepository->isMfaCodeRevoked($mfaCodePayload->mfa_code_id) === true) {
            throw OAuthServerException::invalidRequest('mfa_token', 'Mfa token has been revoked');
        }

        if (!\property_exists($mfaCodePayload,'client_id') || $mfaCodePayload->client_id !== $client->getIdentifier()) {
            throw OAuthServerException::invalidRequest('mfa_token', 'Mfa token was not issued to this client');
        }
    }
}
