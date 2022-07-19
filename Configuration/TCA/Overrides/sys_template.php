<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('one_trust_utility', 'Configuration/TypoScript', 'OneTrust Utility');
