<?php


use Illuminate\Support\Str;

if (!function_exists('getDomainType')) {
//    $domainType =[
//        'https://www'=>4,
//        'http://www'=>3,
//        'https://'=>2,
//        'http://'=>1,
//    ];
    function getDomainType($url = null):null|array
    {
        if(empty($url)) return null;
        $url = strtolower($url);
        if(str::contains($url,'https://www')){
            $domain = explode('https://www',$url);
            return ['domain'=>$domain,'domain_type'=>4];
        }
        if(str::contains($url,'http://www')){
            $domain = explode('http://www',$url);
            return ['domain'=>$domain,'domain_type'=>3];
        }
        if(str::contains($url,'https://')){
            $domain = explode('https://',$url);
            return ['domain'=>$domain,'domain_type'=>2];
        }
        if(str::contains($url,'http://')){
            $domain = explode('http://',$url);
            return ['domain'=>$domain,'domain_type'=>1];
        }
        return ['domain'=>$url,'domain_type'=>null];
    }
}

if(!function_exists('getInbetweenString')){
    function getInbetweenString($str,$start= '{@@', $end='@@}' )
    {
        $matches = array();
        $regex = "/$start([a-zA-Z0-9_]*)$end/";
        preg_match_all($regex, $str, $matches);
        return $matches[1];
    }
}

if(!function_exists('swapData')){
    function getInbetweenStrings($str,$start= '{@@', $end='@@}' )
    {
        $matches = array();
        $regex = "/$start([a-zA-Z0-9_]*)$end/";
        preg_match_all($regex, $str, $matches);
        return $matches[1];
    }
}
