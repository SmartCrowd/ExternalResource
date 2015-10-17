<?php

use Etechnika\IdnaConvert\IdnaConvert;

class ExternalResource
{
    
    private static $base_href_exception_domains = ['https://vk.com'];

    private static $curl_options = [
        CURLOPT_HEADER => 0,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3',
        CURLOPT_FRESH_CONNECT => 0,
        CURLOPT_FOLLOWLOCATION => 1,
        CURLOPT_RETURNTRANSFER => 1,
    ];

    public static function getResource($link, $rel2abs = true)
    {
        $link = static::instagramHook($link);

        try {
            $link = static::encodeUrl($link);
            $response = static::getContent($link);
        } catch (\Exception $e) {
            return $e->getMessage();
        }

        static::setContentTypeHeaders($response);

        $result = $rel2abs ? static::rel2abs($response['content'], $link) : $response['content'];

        return $result;
    }

    /**
     * Получение контента по ссылке
     *
     * @param $link
     * @return string
     * @throws Exception
     */
    public static function getContent($link)
    {
        $ch = curl_init($link);
        curl_setopt_array($ch, static::$curl_options);
        $content = curl_exec($ch);
        $result  = curl_getinfo($ch);
        $result['errno'] = curl_errno($ch);
        $result['error'] = curl_error($ch);
        $result['content'] = $content;
        curl_close($ch);

        if ($result['errno'] !== 0) {
            throw new \Exception("Не удалось загрузить страницу\n" . $link . ". Error: " . $result['errno']);
        }

        if ($result['http_code'] != 200) {
            throw new \Exception("Ссылка недоступна " . $result['http_code']);
        }

        return $result;
    }

    /**
     * Энкодинг url
     *
     * @param $link
     * @return string
     */
    public static function encodeUrl($link)
    {
        $domain = parse_url($link, PHP_URL_HOST);
        $encoded_domain = IdnaConvert::encodeString($domain);

        return str_replace($domain, $encoded_domain, $link);
    }

    /**
     * Задание базового URL для относительных URL
     *
     * @param $file
     * @param $url
     * @return mixed
     */
    public static function rel2abs($file, $url)
    {
        $full_domain = static::getHostFromUrl($url);
        $pattern = '#(<\s*((img)|(a)|(link))\s+[^>]*((src)|(href))\s*=\s*[\"\'])(?!\/\/)(?!http)([^\"\'>]+)([\"\'>]+)#';
        $file = preg_replace($pattern, '$1' . $full_domain . '$9$10', $file);
        if (!in_array($full_domain, static::$base_href_exception_domains)
            && !preg_match('/(<base[^>]* href="(.*)">)/', $file)) {
            $file = preg_replace('/(<head[^>]*>)/', '$1<base href="' . $full_domain . '"/>', $file);
        }

        return $file;
    }

    /**
     * @param  string $url
     * @param  bool   $with_scheme
     *
     * @return string
     */
    public static function getHostFromUrl($url, $with_scheme = true)
    {
        $host = parse_url($url);
        $host = $with_scheme ? $host['scheme'] . "://" . $host['host'] : $host['host'];

        return $host;
    }

    /**
     * Redirect all instagram links to captioned embed url
     *
     * @param $link
     *
     * @return mixed
     */
    public static function instagramHook($link)
    {
        $instagram_hosts = ['instagram.com'];
        $parsed_url = parse_url($link);
        $host = isset($parsed_url['host']) ? strtolower($parsed_url['host']) : "";
        if (in_array($host, $instagram_hosts)) {
            $link = preg_replace('/\/embed(\/captioned)?(\/)?$/i', '', $link);
            $link = $link . "/embed/captioned";
        }

        return $link;
    }

    public static function setContentTypeHeaders($response)
    {
        if (strpos($response['content_type'], 'charset')) {
            header('Content-type:' . $response['content_type']);
        } elseif (preg_match('/<meta[^>]+charset=[\']?(.*?)[\']?[\/\s>]/i', $response['content'], $matches)) {
            header('Content-type:' . $response['content_type'] . '; charset=' . $matches[1]);
        }
    }
    
}