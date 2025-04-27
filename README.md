# Laravel News Parser

Проект‑шаблон для асинхронного парсинга новостных сайтов.

## Быстрый старт

```bash
git clone <repo> parser
cd parser

# backend
composer install
cp .env.example .env
php artisan key:generate

# frontend
npm install
npm run build # или npm run dev

php artisan migrate
php artisan serve
```

Откройте `http://localhost:8000/parser` — интерфейс на Livewire.

### Основные технологии
* Laravel 10, PHP 8.2
* Livewire v3 + Tailwind CSS
* Symfony DomCrawler, Laravel HTTP Client (pool)  
* HTMLPurifier для очистки HTML  
* Jobs/Promises для асинхронных запросов  
* Service/Repository паттерн

### Шаблоны сайтов

JSON‑файлы лежат в `storage/parser/templates/*.json`.  
Пример:

```json
{
  "name": "example",
  "title": "h2 a",
  "list_item": "div.newsarea",
  "short_story": "div.newsarea",
  "full_story": "div.newsarea",
  "full_link": "h2 a",
  "images": "img",
  "remove": ["div.advert", "script"]
}
```

### Хранение картинок

Все медиа сохраняются в `storage/app/public/news/*`.  
Проверьте `config/filesystems.php` и выполните `php artisan storage:link`.
