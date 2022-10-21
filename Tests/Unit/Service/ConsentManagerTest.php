<?php

namespace Mediatis\OneTrustUtility\Tests\Unit\Service;

use Mediatis\OneTrustUtility\Service\ConsentManager;
use Mediatis\OneTrustUtility\Service\ConsentManagerInterface;
use Mediatis\OneTrustUtility\Service\CookieServiceInterface;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ConsentManagerTest extends UnitTestCase
{
    protected const VALID_DATESTAMP = 'Thu+Jul+14+2022+18%3A25%3A26+GMT%2B0200+(Central+European+Summer+Time)';

    protected ConsentManager $subject;

    protected MockObject $cookieService;

    // example values within consent cookie:
    // datestamp=Thu+Jul+14+2022+18%3A25%3A26+GMT%2B0200+(Central+European+Summer+Time)
    // groups=C0001%3A1%2CC0002%3A1%2CC0003%3A0%2CC0004%3A1
    // AwaitingReconsent=false

    protected function setUp(): void
    {
        parent::setUp();
        $this->cookieService = $this->createMock(CookieServiceInterface::class);
        $this->subject = new ConsentManager($this->cookieService);
    }

    /** @test */
    public function missingCookieLeadsToNoConsent(): void
    {
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(false);
        $result = $this->subject->checkConsent('C0001');
        self::assertFalse($result);
    }

    /** @test */
    public function missingCookieWithDefaultLeadsToDefaultTrue(): void
    {
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(false);
        $result = $this->subject->checkConsent('C0001', true);
        self::assertTrue($result);
    }

    /** @test */
    public function missingCookieWithDefaultLeadsToDefaultFalse(): void
    {
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(false);
        $result = $this->subject->checkConsent('C0001', false);
        self::assertFalse($result);
    }

    /** @test */
    public function missingCookieDataLeadsToNoConsent(): void
    {
        $cookieValue = 'a=b&c=d';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertFalse($result);
    }

    /** @test */
    public function missingDatestampLeadsToNoConsent(): void
    {
        $cookieValue = 'groups=C0001%3A1&AwaitingReconsent=false';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertFalse($result);
    }

    /** @test */
    public function missingGroupsLeadsToNoConsent(): void
    {
        $cookieValue = 'datestamp=' . static::VALID_DATESTAMP . '&AwaitingReconsent=false';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertFalse($result);
    }

    /** @test */
    public function missingAwaitingReconsentLeadsToNoConsent(): void
    {
        $cookieValue = 'datestamp=' . static::VALID_DATESTAMP . '&groups=C0001%3A1';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertFalse($result);
    }

    /** @test */
    public function requestedConsentIsGiven(): void
    {
        $cookieValue = 'datestamp=' . static::VALID_DATESTAMP . '&AwaitingReconsent=false&groups=C0001%3A1';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertTrue($result);
    }

    /** @test */
    public function otherConsentIsGiven(): void
    {
        $cookieValue = 'datestamp=' . static::VALID_DATESTAMP . '&AwaitingReconsent=false&groups=C0002%3A1';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertFalse($result);
    }

    /** @test */
    public function requestedAndOtherConsentIsGiven(): void
    {
        $cookieValue = 'datestamp=' . static::VALID_DATESTAMP . '&AwaitingReconsent=false&groups=C0001%3A1%2CC0002%3A1';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertTrue($result);
    }

    /** @test */
    public function requestedConsentIsGivenButOutdated(): void
    {
        $cookieValue = 'datestamp=' . static::VALID_DATESTAMP . '&AwaitingReconsent=true&groups=C0001%3A1';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertFalse($result);
    }

    /** @test */
    public function requestedConsentIsGivenButTimestampIsMissing(): void
    {
        $cookieValue = 'AwaitingReconsent=true&groups=C0001%3A1';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertFalse($result);
    }

    /** @test */
    public function requestedConsentIsDenied(): void
    {
        $cookieValue = 'datestamp=' . static::VALID_DATESTAMP . '&AwaitingReconsent=false&groups=C0001%3A0';
        $this->cookieService->method('checkCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn(true);
        $this->cookieService->method('getCookie')->with(ConsentManagerInterface::PERMISSION_COOKIE_NAME)->willReturn($cookieValue);
        $result = $this->subject->checkConsent('C0001');
        self::assertFalse($result);
    }
}
