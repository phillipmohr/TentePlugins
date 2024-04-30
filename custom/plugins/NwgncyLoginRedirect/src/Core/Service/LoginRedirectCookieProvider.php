<?php declare(strict_types=1);

namespace NwgncyLoginRedirect\Core\Service;

use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Framework\Cookie\CookieProviderInterface;

class LoginRedirectCookieProvider implements CookieProviderInterface
{
    public function __construct(
        private readonly CookieProviderInterface $originalService,
        private readonly SystemConfigService $systemConfigService
    )
    {
    }

    public function getCookieGroups(): array
    {
        $cookieGroups = $this->originalService->getCookieGroups();

        // $osmCookieConsent = $this->systemConfigService->get('MoorlFoundation.config.osmCookieConsent');
        // if (!$osmCookieConsent) {
        //     return $cookieGroups;
        // }

        foreach ($cookieGroups as $groupIndex => $cookieGroup) {
            if ($cookieGroup['snippet_name'] !== 'cookie.groupComfortFeatures') {
                continue;
            }

            $cookieGroups[$groupIndex]['entries'][] = [
                'snippet_name' => 'nwgncy-login-redirect.cookieName',
                'snippet_description' => 'nwgncy-login-redirect.cookieDescription',
                'cookie' => 'nwgncy-login-redirect',
                'value' => '1',
            ];
        }

        return $cookieGroups;
    }
}
