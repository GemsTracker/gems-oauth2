<?php

namespace Gems\OAuth2\AuthorizationValidators;

use DateInterval;
use DateTimeZone;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

class BearerTokenValidator extends \League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator
{
    use CryptTrait;

    protected $publicKey;

    private Configuration $jwtConfiguration;
    public function __construct(
        protected readonly AccessTokenRepositoryInterface $accessTokenRepository,
        protected readonly DateInterval|null $jwtValidAtDateLeeway = null
    ) {
        parent::__construct($accessTokenRepository, $jwtValidAtDateLeeway);
    }

    /**
     * Set the public key
     *
     * @param CryptKey $key
     */
    public function setPublicKey(CryptKey $key): void
    {
        $this->publicKey = $key;

        $this->initJwtConfiguration();
    }

    /**
     * Initialise the JWT configuration.
     */
    private function initJwtConfiguration(): void
    {
        $this->jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('empty', 'empty')
        );

        $clock = new SystemClock(new DateTimeZone(\date_default_timezone_get()));
        $this->jwtConfiguration->setValidationConstraints(
            new LooseValidAt($clock, $this->jwtValidAtDateLeeway),
            new SignedWith(
                new Sha256(),
                InMemory::plainText($this->publicKey->getKeyContents(), $this->publicKey->getPassPhrase() ?? '')
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthorization(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request->hasHeader('authorization') === false) {
            throw OAuthServerException::accessDenied('Missing "Authorization" header');
        }

        $header = $request->getHeader('authorization');
        $jwt = \trim((string) \preg_replace('/^\s*Bearer\s/', '', $header[0]));

        try {
            // Attempt to parse the JWT
            /**
             * @var Plain $token
             */
            $token = $this->jwtConfiguration->parser()->parse($jwt);
        } catch (\Lcobucci\JWT\Exception $exception) {
            throw OAuthServerException::accessDenied($exception->getMessage(), null, $exception);
        }

        try {
            // Attempt to validate the JWT
            $constraints = $this->jwtConfiguration->validationConstraints();
            $this->jwtConfiguration->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated $exception) {
            throw OAuthServerException::accessDenied('Access token could not be verified');
        }

        $claims = $token->claims();

        // Check if token has been revoked
        if ($this->accessTokenRepository->isAccessTokenRevoked($claims->get('jti'))) {
            throw OAuthServerException::accessDenied('Access token has been revoked');
        }

        $request = $request
            ->withAttribute('oauth_access_token_id', $claims->get('jti'))
            ->withAttribute('oauth_client_id', $this->convertSingleRecordAudToString($claims->get('aud')))
            ->withAttribute('oauth_user_id', $claims->get('sub'))
            ->withAttribute('oauth_scopes', $claims->get('scopes'))
            ->withAttribute('user_role', $claims->get('role'));


        // Return the request with additional attributes
        return $this->getAdditionalAttributesForRequest(
            $request,
            $token,
        );
    }

    /**
     * Convert single record arrays into strings to ensure backwards compatibility between v4 and v3.x of lcobucci/jwt
     *
     * @param array|string $aud
     *
     * @return array|string
     */
    private function convertSingleRecordAudToString(array|string $aud): array|string
    {
        return \is_array($aud) && \count($aud) === 1 ? $aud[0] : $aud;
    }

    protected function getAdditionalAttributesForRequest(ServerRequestInterface $request, Plain $token): ServerRequestInterface
    {
        $claims = $token->claims();

        return $request->withAttribute('currentUserId', $claims->get('user_id'));
    }
}
