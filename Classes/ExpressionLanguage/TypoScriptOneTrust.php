<?php

namespace Mediatis\OneTrustUtility\ExpressionLanguage;

use Mediatis\OneTrustUtility\Service\ConsentManager;

class TypoScriptOneTrust
{
    public function __construct(
        protected ConsentManager $consentManager,
    ) {}

    public function checkConsent(string $level, bool $default = false): bool
    {
        return $this->consentManager->checkConsent($level, $default);
    }
}
