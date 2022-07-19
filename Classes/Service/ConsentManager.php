<?php

namespace Mediatis\OneTrustUtility\Service;

class ConsentManager implements ConsentManagerInterface
{
    /** @var CookieServiceInterface */
    protected $cookieService;

    /** @var array<string,mixed>|null */
    protected $data;

    public function __construct(CookieServiceInterface $cookieService)
    {
        $this->cookieService = $cookieService;
    }

    /**
     * @param bool $reset
     * @return array<string,mixed>
     */
    protected function readConsentData(bool $reset = false): array
    {
        if (!isset($this->data) || $reset) {
            // example cookie:
            // isGpcEnabled=0&datestamp=Thu+Jul+14+2022+18%3A25%3A26+GMT%2B0200+(Central+European+Summer+Time)&version=6.38.0&isIABGlobal=false&hosts=&consentId=eaf2bd84-3c7a-40f3-ae86-b94c9dabeab2&interactionCount=8&landingPath=NotLandingPage&groups=C0001%3A1%2CC0002%3A1%2CC0003%3A0%2CC0004%3A1&AwaitingReconsent=false&geolocation=DE%3BHE
            $this->data = [];
            if ($this->cookieService->checkCookie(static::PERMISSION_COOKIE_NAME)) {
                $valueStrings = explode('&', (string)$this->cookieService->getCookie(static::PERMISSION_COOKIE_NAME));
                foreach ($valueStrings as $valueString) {
                    $pair = explode('=', $valueString);
                    if (count($pair) !== 2) {
                        throw new ConsentManagementException('Invalid consent data format, expected KEY=VALUE, found "' . $valueString . '".');
                    }
                    $key = $pair[0];
                    $value = urldecode($pair[1]);
                    if ($key === static::KEY_GROUPS) {
                        $groups = explode(',', $value);
                        $value = [];
                        foreach ($groups as $groupDataString) {
                            $groupData = explode(':', $groupDataString);
                            if (count($groupData) !== 2) {
                                throw new ConsentManagementException('Invalid consent group format, expected GROUP_ID:CONSENT_STATUS, found "' . $groupDataString . '".');
                            }
                            $value[$groupData[0]] = $groupData[1] === '1';
                        }
                    } elseif ($value === 'false') {
                        $value = false;
                    } elseif ($value === 'true') {
                        $value = true;
                    }
                    $this->data[$key] = $value;
                }
            }
        }
        return $this->data;
    }

    public function checkConsent(string $groupId, bool $default = false): bool
    {
        try {
            $data = $this->readConsentData(true);

            // if the data is not available, use the default
            if (!isset($data[static::KEY_DATESTAMP]) || !isset($data[static::KEY_AWAITING_RECONSENT]) || !isset($data[static::KEY_GROUPS])) {
                return $default;
            }

            // if the consent was not given or has to be given again, use the default
            if ($data[static::KEY_AWAITING_RECONSENT] || !$data[static::KEY_DATESTAMP]) {
                return $default;
            }

            if (!isset($data[static::KEY_GROUPS][$groupId])) {
                throw new ConsentManagementException('Group with ID "' . $groupId . '" not found.');
            }

            // if everything is available and up-to-date, use the consent data for that group
            return $data[static::KEY_GROUPS][$groupId];
        } catch (ConsentManagementException $e) {
            // TODO logging?
            // if something goes wrong, use the default
            return $default;
        }
    }
}
