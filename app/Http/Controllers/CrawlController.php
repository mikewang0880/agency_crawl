<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Crawler\Crawler;

class CrawlController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $crawler = new Crawler();
        $crawler->startCrawl();

        return view('crawl.index', [
            'number_of_pages' => $crawler->number_of_pages,
            'number_of_images' => count($crawler->images),
            'number_of_internal_links' => count($crawler->internal_links),
            'number_of_external_links' => count($crawler->external_links),
            'avg_page_load' => $crawler->avg_page_load,
            'avg_word_count' => $crawler->avg_word_count,
            'avg_title_length' => $crawler->avg_title_length,
            'pages' => $crawler->pages
        ]);
    }
}
