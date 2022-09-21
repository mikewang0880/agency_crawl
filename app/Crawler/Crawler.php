<?php

namespace App\Crawler;

use GuzzleHttp\Client as HttpClient;

/**
 * Crawl agencyanalytics website and parse, calculate several result value.
 * Entry method is startCrawl().
 *
 * Entry URL parameter is get from .env file
 */
class Crawler
{
    private $baseUrl;

    const MAX_CRAWL = 6;

    public $internalLinks = [];

    public $externalLinks = [];

    public $images = [];

    public $numberOfPages;

    public $avgPageLoad;

    public $avgWordCount;

    public $avgTitleLength;

    public $pages;

    public function __construct()
    {
        $this->baseUrl = env('CRAWL_URL');
    }

    // Entry Point Method
    public function startCrawl()
    {
        $pages = [];
        $url = '/';

        // crawl children pages until reach out to value of MAX_CRAWL
        for ($l = 0; $l < self::MAX_CRAWL; $l++) {
            $pages = $this->crawlPage($url, $pages);

            for ($i = 0; $i < count($this->internalLinks); $i++) {
                $url = $this->internalLinks[$i];
                if (! array_key_exists($url, $pages)) {
                    break;
                }
            }
        }

        $urls = array_keys($pages);
        $this->numberOfPages = count($urls);

        // Calculate several result value and save to member variable.
        $this->avgPageLoad = array_sum(array_map(function ($url) use ($pages) {
            return $pages[$url]['load_time'];
        }, $urls)) / $this->numberOfPages;
        $this->avgWordCount = array_sum(array_map(function ($url) use ($pages) {
            return $pages[$url]['word_count'];
        }, $urls)) / $this->numberOfPages;
        $this->avgTitleLength = array_sum(array_map(function ($url) use ($pages) {
            return strlen($pages[$url]['title']);
        }, $urls)) / $this->numberOfPages;
        $this->pages = $pages;
    }

    /**
     * Crawling one page.
     *
     * @param [string] $url
     * @param [array] $pages
     * @return void
     */
    private function crawlPage($url, $pages)
    {
        $client = new HttpClient();
        $start = time();
        $res = $client->get($this->baseUrl.$url);

        $status = $res->getStatusCode(); // 200
        $body = $res->getBody();

        $page = $this->parseBody($url, $body);
        $end = time();
        $page['load_time'] = $end - $start;

        $this->externalLinks = array_unique(array_merge($this->externalLinks, $page['externalLinks']));
        $this->internalLinks = array_unique(array_merge($this->internalLinks, $page['internalLinks']));
        $this->images = array_unique(array_merge($this->images, $page['images']));

        $pages[$url] = [
            'status' => $status,
            'title' => $page['title'],
            'word_count' => $page['word_count'],
            'load_time' => $page['load_time'],
        ];

        return $pages;
    }

    /**
     * parse HTML response and extract information from it.
     *
     * @param [type] $url
     * @param [type] $body
     * @return void
     */
    private function parseBody($url, $body)
    {
        $page = [
            'internalLinks' => [],
            'externalLinks' => [],
            'images' => [],
            'title' => '',
            'word_count' => 0,
            'load_time' => 0,
        ];
        $exp_link = "/<a[^>]+href=['\"]([^'\"]+)['\"]/i";
        preg_match_all($exp_link, $body, $matches);

        if (count($matches) > 0) {
            $links = array_map(function ($link) {
                $link = preg_replace('/#.+$/i', '', $link);
                if ($link == '') {
                    $link = '/';
                }

                return $link;
            }, $matches[1]);
            $links = array_unique($links);
            $page['internalLinks'] = array_filter($links, function ($link, $v) use ($url) {
                return preg_match('/^http/i', $link) == 0 && $link != $url;
            }, ARRAY_FILTER_USE_BOTH);

            $page['externalLinks'] = array_filter($links, function ($link, $v) use ($url) {
                return preg_match('/^http/i', $link) > 0 && $link != $url;
            }, ARRAY_FILTER_USE_BOTH);
        }

        $exp_img = "/<img[^>]+src=['\"]([^'\"]+)['\"]/i";
        preg_match_all($exp_img, $body, $matches);
        if (count($matches) > 0) {
            $images = array_unique($matches[1]);
            $page['images'] = array_filter($images, function ($img, $v) {
                return preg_match('/^data-image/i', $img) == 0;
            }, ARRAY_FILTER_USE_BOTH);
        }
        $exp_title = "/<title[^>]*>(.+)<\/title>/i";
        preg_match($exp_title, $body, $matches);
        if (count($matches) > 0) {
            $page['title'] = $matches[1];
        }

        $options = [
            'ignore_errors' => true,
            'drop_links' => true,
        ];
        $text = \Soundasleep\Html2Text::convert($body, $options);
        $page['word_count'] = str_word_count($text);

        return $page;
    }
}
