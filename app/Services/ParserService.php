<?php

namespace App\Services;

use Illuminate\Http\Client\Pool;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\DomCrawler\Crawler;
use HTMLPurifier;
use HTMLPurifier_Config;
use Illuminate\Support\Collection;

class ParserService
{
    protected HTMLPurifier $purifier;

    public function __construct()
    {
        $config = HTMLPurifier_Config::createDefault();
        $this->purifier = new HTMLPurifier($config);
    }

    public function parseRange(string $templateName, string $baseUrl, int $from, int $to): Collection
    {
        $template = $this->loadTemplate($templateName);

        $responses = Http::pool(fn (Pool $pool) =>
            collect(range($from, $to))->map(
                fn ($page) => $pool->as((string)$page)->get($baseUrl.$page)
            )
        );

        $stories = collect();

        foreach ($responses as $page => $response) {
            if ($response->successful()) {
                $stories = $stories->merge(
                    $this->parseListPage($response->body(), $template)
                );
            }
        }

        return $stories;
    }

    protected function parseListPage(string $html, array $template): Collection
    {
        $crawler = new Crawler($html);
        $stories = collect();

        $crawler->filter($template['list_item'] ?? 'body')->each(function (Crawler $node) use ($template, &$stories) {
            $titleNode = $node->filter($template['title']);
            if (!$titleNode->count()) {
                return;
            }

            $link = $titleNode->link()->getUri();
            $title = $titleNode->text();
            $shortHtml = $node->filter($template['short_story'] ?? '')->html('');

            $stories->push([
                'title' => $title,
                'slug' => Str::slug($title),
                'short_story' => $this->clean($shortHtml),
                'source_url' => $link,
                'template' => $template['name'] ?? '',
            ]);
        });

        return $stories;
    }

    public function parseFull(string $url, array $template): array
    {
        $body = Http::get($url)->body();
        $crawler = new Crawler($body);

        $fullNode = $crawler->filter($template['full_story'] ?? 'body');
        $images = $this->downloadImages($fullNode, $url);

        return [
            'full_story' => $this->clean($fullNode->html('')),
            'images' => $images,
        ];
    }

    protected function downloadImages(Crawler $node, string $pageUrl): array
    {
        $images = [];

        $node->filter('img')->each(function (Crawler $img) use (&$images, $pageUrl) {
            $src = $img->attr('src');
            $url = Str::startsWith($src, 'http') ? $src : Str::beforeLast($pageUrl, '/').'/'.$src;

            $content = Http::get($url)->body();
            $path = 'news/'.Str::uuid().'.'.pathinfo($url, PATHINFO_EXTENSION);

            Storage::put($path, $content);
            $img->getNode(0)->setAttribute('src', Storage::url($path));
            $images[] = $path;
        });

        return $images;
    }

    protected function loadTemplate(string $name): array
    {
        $path = storage_path("parser/templates/{$name}.json");
        if (!file_exists($path)) {
            throw new \RuntimeException("Template {$name} not found.");
        }

        return json_decode(file_get_contents($path), true);
    }

    protected function clean(?string $html): string
    {
        return $html ? $this->purifier->purify($html) : '';
    }
}
