# Разворот проекта

## Копируем env файл

```bash
cp .env.example .env
```

## Устанавливаем переменные окружения
### Указываем свободные порты для следующих директив:
```
APP_PORT=
FORWARD_DB_PORT=
FORWARD_REDIS_PORT=
```

## Устанавливаем бекэнд зависимости
```bash
./vendor/bin/sail composer install
```


## Запускаем docker контейнеры
```bash
./vendor/bin/sail up -d 
```

## Выполняем команду
```bash
./vendor/bin/sail php artisan key:generate &&
./vendor/bin/sail php artisan storage:link &&
./vendor/bin/sail php artisan migrate --seed
```

## Запуск статического анализа кода
### Phpstan
```bash
./vendor/bin/sail composer analyse
```
### Cs-fixer
```bash
./vendor/bin/sail composer cs-fixer:check
```
```bash
./vendor/bin/sail composer cs-fixer:fix
```

### Запуск тестов
```bash
./vendor/bin/sail artisan test
```
