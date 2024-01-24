<?php declare(strict_types=1);

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
//        $clientIP = '45.136.153.58';//germany todo remove;
//        $clientIP = '105.77.130.1';//morocco  todo remove;
//        $clientIP = '103.103.131.255';//thaiwan  todo remove;
//        $clientIP = '168.121.131.255';//Panama  todo remove;
//        $clientIP = '104.146.213.255';//Brazil  todo remove;
//        $clientIP = '103.10.126.7';//australia  todo remove;
        $ipArr = explode(',', $clientIP);

        return filter_var(array_shift($ipArr), FILTER_VALIDATE_IP);
    }
}