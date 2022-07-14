<?php

declare(strict_types=1);

namespace Gems\OAuth2\ResponseType;

use Laminas\Diactoros\Response\JsonResponse;
use League\OAuth2\Server\ResponseTypes\AbstractResponseType;
use League\OAuth2\Server\ResponseTypes\ResponseTypeInterface;
use Psr\Http\Message\ResponseInterface;

class MfaResponse extends AbstractResponseType implements ResponseTypeInterface
{
    const HTTP_CODE = 403;

    /**
     * @var array Body of the response
     */
    protected array $responseBody = [];

    /**
     * @return array
     */
    public function getResponseBody(): array
    {
        return $this->responseBody;
    }

    /**
     * @param array $responseBody
     */
    public function setResponseBody(array $responseBody): void
    {
        $this->responseBody = $responseBody;
    }

    /**
     * Generate a http response with the code and response body
     *
     * @param ResponseInterface $response
     * @return JsonResponse
     */
    public function generateHttpResponse(ResponseInterface $response): ResponseInterface
    {
        return new JsonResponse($this->getResponseBody(), static::HTTP_CODE);
    }
}
