<div>
    <div class="mb-4">
        <label class="block mb-1 font-bold">Выберите сайт:</label>
        <select wire:model="siteId" class="border rounded p-2">
            <option value="">-- выберите --</option>
            @foreach ($sites as $site)
                <option value="{{ $site->id }}">{{ $site->name }}</option>
            @endforeach
        </select>
        <button wire:click="parse" class="ml-4 px-4 py-2 bg-blue-500 text-white rounded">Парсить</button>
    </div>

    <div>
        @foreach ($news as $item)
            <div class="border-b py-2">
                <h2 class="font-bold">{{ $item['title'] }}</h2>
                <div>{!! $item['short_story'] !!}</div>
                <a href="{{ $item['link'] }}" class="text-blue-600 underline" target="_blank">Ссылка</a>
            </div>
        @endforeach
    </div>
</div>
