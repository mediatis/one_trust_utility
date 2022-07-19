<?php

namespace Mediatis\OneTrustUtility\Service;

interface ConsentManagerInterface
{
    public const PERMISSION_COOKIE_NAME = 'OptanonConsent';

    public const KEY_DATESTAMP = 'datestamp';
    public const KEY_AWAITING_RECONSENT = 'AwaitingReconsent';
    public const KEY_GROUPS = 'groups';

    public function checkConsent(string $groupId, bool $default = false): bool;
}
