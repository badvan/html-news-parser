<x-layouts.app>
<div>
    <div class="p-6 bg-white shadow rounded-xl space-y-4">
        <div class="grid md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Шаблон</label>
                <select wire:model="templateName" class="mt-1 w-full rounded border-gray-300">
                    <option value="">-- выберите --</option>
                    @foreach($templates as $tpl)
                        <option value="{{ $tpl }}">{{ $tpl }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Базовый URL (пример: https://example.com/news?page=)</label>
                <input type="url" wire:model.lazy="baseUrl" class="mt-1 w-full rounded border-gray-300"/>
            </div>

            <div>
                <label class="block text-sm font-medium">От страницы</label>
                <input type="number" wire:model="fromPage" class="mt-1 w-full rounded border-gray-300" min="1"/>
            </div>

            <div>
                <label class="block text-sm font-medium">До страницы</label>
                <input type="number" wire:model="toPage" class="mt-1 w-full rounded border-gray-300" min="1"/>
            </div>
        </div>

        <button wire:click="parseRange" class="px-4 py-2 bg-blue-600 text-white rounded">Парсить</button>
    </div>

    @if($loading)
        <div class="mt-6 flex items-center">
            <svg class="animate-spin h-5 w-5 mr-3" viewBox="0 0 24 24"></svg>
            <span>Парсинг...</span>
        </div>
    @endif

    <div class="mt-6 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($stories as $idx => $story)
            <div class="bg-white p-4 rounded-xl shadow space-y-2">
                <h3 class="text-lg font-semibold">{{ $story['title'] }}</h3>
                <div class="prose max-h-40 overflow-auto">{!! $story['short_story'] !!}</div>
                <button wire:click="saveStory({{ $idx }})" class="px-4 py-2 bg-green-600 text-white rounded">Сохранить</button>
            </div>
        @endforeach
    </div>
</div>
</x-layouts.app>
