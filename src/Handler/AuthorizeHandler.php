<?php

declare(strict_types=1);

namespace Gems\OAuth2\Handler;

use Gems\OAuth2\Entity\User;
use Gems\OAuth2\Exception\AuthException;
use Gems\OAuth2\Exception\ThrottleException;
use Gems\OAuth2\Repository\UserRepository;
use Laminas\Diactoros\Response;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Entities\UserEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\RequestTypes\AuthorizationRequest;
use Mezzio\Flash\FlashMessageMiddleware;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthorizeHandler implements RequestHandlerInterface
{

    /**
     * @var string Flash message key
     */
    protected string $flashName = 'authenticatedUser';

    /**
     * @var string Flash message time key
     */
    protected string $flashTimeName = 'authenticatedTime';


    /**
     * @var AuthorizationServer
     */
    protected AuthorizationServer $server;
    /**
     * @var UserRepository
     */
    protected UserRepository $userRepository;
    /**
     * @var TemplateRendererInterface
     */
    protected TemplateRendererInterface $template;

    public function __construct(AuthorizationServer $server, UserRepository $userRepository, TemplateRendererInterface $template)
    {
        $this->server = $server;
        $this->userRepository = $userRepository;
        $this->template = $template;
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $templateData = [];

        $group = $request->getAttribute('group');

        $templateData['group'] = $group;

        if ($request->getMethod() === 'POST') {
            $authRequest = $this->server->validateAuthorizationRequest($request);
            $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);

            $data = $request->getParsedBody();

            if (isset($data['otp']) && $userIdentifier = $flashMessages->getFlash($this->flashName)) {
                $user = $this->userRepository->getUserByIdentifier($userIdentifier);
                try {
                    if ($user instanceof User && $this->userRepository->verifyOtp($user, $data['otp'])) {
                        return $this->approveRequest($authRequest, $user);
                    }
                } catch (ThrottleException $e) {
                    $templateData['errors'][] = $e->getMessage();
                }

                // Check auth time
                $templateData['errors'] = ['The Code you have entered is invalid'];
                $templateData['otp_method'] = $this->userRepository->get2faMethod($user);
                $flashMessages->prolongFlash();

                return new Response\HtmlResponse($this->template->render('auth::login_otp', $templateData));
            } elseif (isset($data['username'], $data['password'])) {
                $combinedUsername = $data['username'] . $this->userRepository->getDelimiter() . $group;
                try {
                    $user = $this->userRepository->getUserEntityByUserCredentials(
                        $combinedUsername,
                        $data['password'],
                        $authRequest->getGrantTypeId(),
                        $authRequest->getClient(),
                    );
                    if ($user instanceof UserEntityInterface) {
                        $flashMessages = $request->getAttribute(FlashMessageMiddleware::FLASH_ATTRIBUTE);
                        $flashMessages->flash($this->flashName, $user->getIdentifier());
                        $flashMessages->flash($this->flashTimeName, new \DateTimeImmutable());

                        try {
                            $this->userRepository->sendOtp($user);
                        } catch (ThrottleException $e) {
                            $templateData['errors'][] = $e->getMessage();
                        } catch (AuthException $e) {
                            $templateData['errors'][] = $e->getMessage();
                        }

                        $templateData['otp_method'] = $this->userRepository->get2faMethod($user);

                        return new Response\HtmlResponse($this->template->render('auth::login_otp', $templateData));
                    }

                } catch(AuthException $e) {
                    $templateData['errors'] = [$e->getMessage()];
                } catch(OAuthServerException $e) {
                    $templateData['errors'] = [$e->getHint()];
                }
            }
        }
        return new Response\HtmlResponse($this->template->render('auth::login', $templateData));
    }

    /**
     * Approve the current request
     *
     * @param AuthorizationRequest $authRequest
     * @param UserEntityInterface $user
     * @return ResponseInterface
     */
    protected function approveRequest(AuthorizationRequest $authRequest, UserEntityInterface $user): ResponseInterface
    {
        $authRequest->setUser($user);
        $authRequest->setAuthorizationApproved(true);

        return $this->server->completeAuthorizationRequest($authRequest, new Response());
    }
}
