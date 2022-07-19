<?php

namespace Mediatis\OneTrustUtility\Service;

interface CookieServiceInterface
{
    public function getCookie(string $name, ?string $default = null): ?string;
    public function checkCookie(string $name): bool;
}
