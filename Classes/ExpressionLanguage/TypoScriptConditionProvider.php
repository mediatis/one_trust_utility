<?php

namespace Mediatis\OneTrustUtility\ExpressionLanguage;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TypoScriptConditionProvider extends AbstractProvider
{
    public function __construct()
    {
        $this->expressionLanguageVariables = [
            'oneTrust' => GeneralUtility::makeInstance(TypoScriptOneTrust::class),
        ];
    }
}
