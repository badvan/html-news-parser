<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\ParserService;
use App\Repositories\NewsRepository;
use Illuminate\Support\Collection;

class ParserDashboard extends Component
{
    public string $templateName = '';
    public string $baseUrl = '';
    public int $fromPage = 1;
    public int $toPage = 1;
    public bool $loading = false;

    public Collection $stories;

    public function mount(): void
    {
        $this->stories = collect();
    }

    public function parseRange(ParserService $parser): void
    {
        \Log::info("Запуск парсинга для: {$this->templateName}, {$this->baseUrl}, страницы: {$this->fromPage} - {$this->toPage}");

        $this->validate([
            'templateName' => 'required',
            'baseUrl' => 'required|url',
            'fromPage' => 'required|integer|min:1',
            'toPage' => 'required|integer|min:1|gte:fromPage',
        ]);

        \Log::info("Парсинг завершён успешно");

        $this->loading = true;

        try {
            $this->stories = $parser->parseRange(
                $this->templateName,
                $this->baseUrl,
                $this->fromPage,
                $this->toPage
            );
        } catch (\Exception $e) {
            // Логируем ошибку и выводим в консоль
            \Log::error("Ошибка парсинга: " . $e->getMessage());
            $this->dispatchBrowserEvent('notify-error', ['message' => 'Произошла ошибка при парсинге.']);
        } finally {
            $this->loading = false;
        }
    }

    public function saveStory(int $index, NewsRepository $repo): void
    {
        if ($story = $this->stories->get($index)) {
            $repo->store($story);
            $this->stories->forget($index);
        }
    }

    public function render()
    {
        $templates = collect(glob(storage_path('parser/templates/*.json')))
            ->map(fn ($f) => basename($f, '.json'));

        return view('livewire.parser-dashboard', [
            'templates' => $templates,
        ]);
    }
}
