<?php

declare(strict_types=1);

namespace Gems\OAuth2\Handler;

use Gems\Log\Loggers;
use Gems\OAuth2\ConfigProvider;
use Laminas\Diactoros\Response\JsonResponse;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class AccessTokenHandler implements RequestHandlerInterface
{
    protected readonly LoggerInterface $logger;
    public function __construct(
        protected readonly AuthorizationServer $server,
        Loggers $loggers,
    )
    {
        $this->logger = $loggers->getLogger(ConfigProvider::OAUTH_LOGGER);
    }

    /**
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $response = new JsonResponse(null);

        try {
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            // Log
            $this->logException($exception, $request);
            return $exception->generateHttpResponse($response);
        }
    }

    protected function logException(\Exception $exception, ServerRequestInterface $request): void
    {
        $body = $request->getParsedBody();
        $basicAuthCredentials = $this->getBasicAuthCredentials($request);
        $client = $body['client_id'] ?? $basicAuthCredentials[0] ?? null;
        $username = $body['username'] ?? null;
        $refreshToken = $this->getPartialRefreshTokenFromRequest($request);

        $context = array_filter([
            'client' => $client,
            'username' => $username,
            'refresh_token' => $refreshToken,
        ]);

        $this->logger->error(sprintf(
            'Access token retrieval failed with message: %s',
            $exception->getMessage(),
        ), $context);
    }

    /**
     * Retrieve HTTP Basic Auth credentials with the Authorization header
     * of a request. First index of the returned array is the username,
     * second is the password (so list() will work). If the header does
     * not exist, or is otherwise an invalid HTTP Basic header, return
     * [null, null].
     *
     * @param ServerRequestInterface $request
     *
     * @return string[]|null[]
     */
    protected function getBasicAuthCredentials(ServerRequestInterface $request): array
    {
        if (!$request->hasHeader('Authorization')) {
            return [null, null];
        }

        $header = $request->getHeader('Authorization')[0];
        if (\strpos($header, 'Basic ') !== 0) {
            return [null, null];
        }

        if (!($decoded = \base64_decode(\substr($header, 6)))) {
            return [null, null];
        }

        if (\strpos($decoded, ':') === false) {
            return [null, null]; // HTTP Basic header without colon isn't valid
        }

        return \explode(':', $decoded, 2);
    }

    protected function getPartialRefreshTokenFromRequest(ServerRequestInterface $request): string|null
    {
        $body = $request->getParsedBody();
        $refreshToken = $body['refresh_token'] ?? null;
        if ($refreshToken) {
            return '...' . substr($refreshToken, -10, 10);
        }
        return null;
    }
}
