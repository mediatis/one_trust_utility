<?php

namespace Mediatis\OneTrustUtility\ExpressionLanguage;

use TYPO3\CMS\Core\ExpressionLanguage\AbstractProvider;

class TypoScriptConditionProvider extends AbstractProvider
{
    public function __construct(TypoScriptOneTrust $typoScriptOneTrust)
    {
        $this->expressionLanguageVariables = [
            'oneTrust' => $typoScriptOneTrust,
        ];
    }
}
