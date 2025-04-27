#!/bin/bash

# Имя архива
ARCHIVE_NAME="laravel_parser_project_$(date +%Y%m%d_%H%M%S).zip"

# Временная директория для сбора
TMP_DIR="__collect_tmp__"

# Очистка предыдущей сборки
rm -rf "$TMP_DIR"
mkdir "$TMP_DIR"

# Создание структуры
mkdir -p "$TMP_DIR"/{app/Parsers,resources/templates,config,scripts}
mkdir -p "$TMP_DIR"/routes
mkdir -p "$TMP_DIR"/resources/views
mkdir -p "$TMP_DIR"/public

# Копируем нужные файлы
cp -r app/Parsers "$TMP_DIR/app/"
cp -r resources/templates "$TMP_DIR/resources/"
cp -r resources/views "$TMP_DIR/resources/views"
cp -r routes/web.php "$TMP_DIR/routes/"
cp -r config/* "$TMP_DIR/config/" 2>/dev/null
cp -r scripts "$TMP_DIR/" 2>/dev/null

# Копируем корневые важные файлы
cp .env.example "$TMP_DIR/" 2>/dev/null
cp composer.json "$TMP_DIR/"
cp vite.config.js "$TMP_DIR/" 2>/dev/null
cp tailwind.config.js "$TMP_DIR/" 2>/dev/null
cp package.json "$TMP_DIR/" 2>/dev/null
cp artisan "$TMP_DIR/" 2>/dev/null

# Архивация
cd "$TMP_DIR"
zip -r "../$ARCHIVE_NAME" .
cd ..

# Удаляем временную директорию
rm -rf "$TMP_DIR"

echo "✅ Архив создан: $ARCHIVE_NAME"