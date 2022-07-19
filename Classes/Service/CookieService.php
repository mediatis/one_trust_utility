<?php

namespace Mediatis\OneTrustUtility\Service;

class CookieService implements CookieServiceInterface
{
    public function getCookie(string $name, ?string $default = null): ?string
    {
        if (!isset($_COOKIE[$name])) {
            return $default;
        }
        return $_COOKIE[$name];
    }

    public function checkCookie(string $name): bool
    {
        return $this->getCookie($name) !== null;
    }
}
