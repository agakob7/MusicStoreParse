<?php

class WebScrap
{


    protected $url;
    protected $initial = true;
    protected $domain;

    function __construct()
    {

    }

    function getWebsite($url, $request = 'GET', $post = array(), $referrer = null, $initial = true)
    {

        $this->initial = $initial;

        //  $user_agent = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.89 Safari/537.36';
        $timeout = 60;
        $ch = curl_init();

        if ($request == 'POST') {
            $this->initial = false;
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $cookie_file = __ROOT__ . '/' . md5($_SERVER['REMOTE_ADDR']) . '.txt';


        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
        // curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_REFERER, isset($referrer) ? $referrer : 'http://google.pl');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        if ($this->initial) {


            @unlink($cookie_file);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file);
            curl_setopt($ch, CURLOPT_COOKIESESSION, true);

        } else {

            curl_setopt($ch, CURLOPT_COOKIE, $cookie_file);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        }
        // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $request);

        if ($request == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        }
        $content = curl_exec($ch);

        if (curl_errno($ch)) {
            echo $url;
            throw new \Exception(curl_errno($ch));
        }

        curl_close($ch);
        return $content;
    }
}

