<?php

namespace BelVGGeoIp\Service;
/**
 *
 */
class IpAddress
{
    /**
     * @return string
     */
    public static function getIpAddress(): string
    {
        $clientIP = $_SERVER['HTTP_CLIENT_IP']
            ?? $_SERVER["HTTP_CF_CONNECTING_IP"] # when behind cloudflare
            ?? $_SERVER['HTTP_X_FORWARDED']
            ?? $_SERVER['HTTP_X_FORWARDED_FOR']
            ?? $_SERVER['HTTP_FORWARDED']
            ?? $_SERVER['HTTP_FORWARDED_FOR']
            ?? $_SERVER['REMOTE_ADDR']
            ?? '0.0.0.0';
        $clientIP = '45.136.153.58';//todo remove;
        $ipArr = explode(',', $clientIP);

        return filter_var(array_shift($ipArr), FILTER_VALIDATE_IP);
    }
}