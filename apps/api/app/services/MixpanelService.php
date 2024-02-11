<?php

namespace app\services;

use Illuminate\Http\Request;
use Mixpanel as BaseMixpanel;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Device;
use Sinergi\BrowserDetector\Os;

class MixpanelService
{
    private BaseMixpanel $mixpanelInstance;

    public function __construct()
    {
        $this->mixpanelInstance = BaseMixpanel::getInstance(config('services.mixpanel.token'), [
            'host' => config('services.mixpanel.host'),
            'consumer' => config('services.mixpanel.consumer', 'socket'),
            'connect_timeout' => config('services.mixpanel.connect-timeout', 2),
            'timeout' => config('services.mixpanel.timeout', 2),
        ]);
    }

    public static function getBaseData(Request $request): array
    {
        $browserInfo = new Browser();
        $osInfo = new Os();
        $deviceInfo = new Device();
        $browserVersion = trim(str_replace('unknown', '', $browserInfo->getName() . ' ' . $browserInfo->getVersion()));
        $osVersion = trim(str_replace('unknown', '', $osInfo->getName() . ' ' . $osInfo->getVersion()));
        $hardwareVersion = trim(str_replace('unknown', '', $deviceInfo->getName()));
        $browserName = $browserInfo->getName();

        $data = [
            '$current_url' => $request->getUri(),
            '$os' => $osVersion,
            'Hardware' => $hardwareVersion,
            '$browser_version' => $browserVersion,
            '$browser' => $browserName,
            '$referrer' => $request->header('referer'),
            '$referring_domain' => $request->header('referer')
                ? parse_url($request->header('referer'))['host']
                : null,
            '$ip' => $request->ip(),
        ];

        if ((!array_key_exists('$browser', $data)) && $browserInfo->isRobot()) {
            $data['$browser'] = 'Robot';
        }

        return array_filter($data);
    }

    public function identify(string $email): void
    {
        $this->mixpanelInstance->identify($email);
    }

    public function track($event, $properties = []): void
    {
        $data = array_filter($properties);

        $this->mixpanelInstance->track($event, $data);
    }
}
