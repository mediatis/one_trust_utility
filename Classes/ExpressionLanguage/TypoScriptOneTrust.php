<?php

namespace Mediatis\OneTrustUtility\ExpressionLanguage;

use Mediatis\OneTrustUtility\Service\ConsentManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class TypoScriptOneTrust
{
    /** @var ConsentManager */
    protected $consentManager;

    public function __construct()
    {
        // TODO for TYPO3 11 use dependency injection
        $this->consentManager = GeneralUtility::makeInstance(ObjectManager::class)->get(ConsentManager::class);
    }

    public function checkConsent(string $level, bool $default = false): bool
    {
        return $this->consentManager->checkConsent($level, $default);
    }
}
