<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use App\Models\NewsSite;
use App\Models\NewsItem;
use App\Parsers\ParserService;
use App\Services\HtmlCleanerService;
use Illuminate\Support\Facades\Storage;

class NewsParser extends Component
{
    public $siteId;
    public $news = [];

    public function parse()
    {
        $site = NewsSite::findOrFail($this->siteId);
        $template = json_decode(Storage::get('parsers/' . $site->parser_template), true);
        $html = Http::get($site->base_url)->body();

        $parser = new ParserService();
        $cleaner = new HtmlCleanerService();
        $items = $parser->parseListPage($html, $template);

        foreach ($items as $item) {
            $fullHtml = Http::get($item['link'])->body();
            $item['short_story'] = $cleaner->clean($item['short_story']);
            $item['full_story'] = $cleaner->clean($parser->parseFullStory($fullHtml, $template));
            $item = array_merge(['news_site_id' => $site->id], $item);

            $this->news[] = $item;
            //NewsItem::create();
        }

        //$this->news = NewsItem::where('news_site_id', $site->id)->latest()->get();
    }

    public function render()
    {
        return view('livewire.news-parser', [
            'sites' => NewsSite::all(),
        ])->layout('components.layouts.app');
    }

}
