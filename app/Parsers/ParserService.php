<?php

namespace App\Parsers;

use App\Services\AbstractSiteService;
use Symfony\Component\DomCrawler\Crawler;
use Illuminate\Support\Facades\Log;

class ParserService extends AbstractSiteService
{
    public array $errors = [];

    public function parseListPage(string $html, array $template): array
    {
        $crawler = new Crawler($html);
        $items = [];

        try {
            $crawler->filter($template['short_story'])->each(function (Crawler $node) use (&$items, $template) {
                try {
                    $titleNode = $node->filter($template['title']);
                    $linkNode = $node->filter($template['full_link']);

                    $items[] = [
                        'title' => $titleNode->count() ? $titleNode->text() : '[title not found]',
                        'short_story' => $node->html(),
                        'link' => $linkNode->count() ? $linkNode->attr('href') : '[link not found]',
                    ];
                } catch (\Exception $e) {
                    $this->errors[] = 'Error parsing item: ' . $e->getMessage();
                    Log::error('Parser item error', ['exception' => $e]);
                }
            });
        } catch (\Exception $e) {
            $this->errors[] = 'Error parsing list page: ' . $e->getMessage();
            Log::error('Parser list error', ['exception' => $e]);
        }

        return $items;
    }

    public function parseFullStory(string $html, array $template): string
    {
        try {
            $crawler = new Crawler($html);
            return $crawler->filter($template['full_story'])->html();
        } catch (\Exception $e) {
            $this->errors[] = 'Error parsing full story: ' . $e->getMessage();
            Log::error('Parser full story error', ['exception' => $e]);
            return '[Error parsing full story]';
        }
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
