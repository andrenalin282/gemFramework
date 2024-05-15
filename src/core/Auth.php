<?php

namespace GemFramework\Core;

use GemLibrary\Http\JWTToken;
use GemLibrary\Http\Request;

/**
 * @function success()
 * @function authorize(array<sting> $roles)
 */
class Auth
{
    private Request $request;
    public ?JWTToken $token;
    public bool $isAuthenticated;
    public int $user_id;
    public ?string $error;
    public function __construct(Request $request)
    {
        $this->isAuthenticated = false;
        $this->user_id = 0;
        $this->request = $request;
        $this->token = null;
        $this->error = null;
        $this->authenticate($request);
    }

    /**
     * determine if request is successfully Authenticated
     * @return bool
     */
    public function success(): bool
    {
        return $this->isAuthenticated;
    }

    /**
     * @param array<string> $roles
     * @return bool
     */
    public function authorize(array $roles): bool
    {
        if (!$this->isAuthenticated || !$this->token) {
            return false;
        }
        if (!in_array($this->token->role, $roles)) {
            return false;
        }
        return true;
    }

    private function checkExistedProcessedRequest(): bool
    {
        if (!isset($this->request->token) || ! $this->request->token instanceof JWTToken) {
            return false;
        }
        return true;
    }

    private function authenticate(Request $request): bool
    {
        $jwt = new JWTToken();
        if (!$this->checkExistedProcessedRequest()) {
            
            if(!$jwt->extractToken($request))
            {
                return false;
            }
            if (!$jwt->verify()) {
                return false;
            }
            $this->token = $jwt;
            $this->isAuthenticated = true;
            $request->__set('token', $jwt);
            $this->request = $request;
            $this->user_id = $jwt->user_id;
            return true;
        }

        if (!isset($this->request->token) || !$this->request->token->isTokenValid) {
            return false;
        }
        $this->token = $this->request->token;
        $this->isAuthenticated = true;
        $this->user_id = $this->request->token->user_id;
        return $this->isAuthenticated;
    }
}
