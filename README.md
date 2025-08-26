## Установка и запуск

### 1. Клонирование репозитория

```bash
git clone <repository-url>
cd test29
```

### 2. Настройка окружения

```bash
# Копирование конфигурации
cp .env.example .env

# Настройка .env
APP_NAME="Car Management API"
APP_URL=http://localhost:8080

DB_CONNECTION=mysql
DB_HOST=database
DB_DATABASE=car_management
DB_USERNAME=car_user
DB_PASSWORD=car_password

REDIS_HOST=redis
QUEUE_CONNECTION=database
```

### 3. Запуск через Docker

```bash
# Сборка и запуск контейнеров
docker-compose up -d --build

# Генерация ключа приложения
docker-compose exec app php artisan key:generate

# Выполнение миграций
docker-compose exec app php artisan migrate

# Заполнение тестовыми данными
docker-compose exec app php artisan db:seed
```

### 4. Проверка работоспособности

```bash
# API доступно по адресу
curl http://localhost:8080/api/v1/brands

# Swagger документация
open http://localhost:8080/api/documentation

# Adminer для управления БД
open http://localhost:8081
```

## 📋 Структура API

### Модели данных

**Марка автомобиля**

- Название

**Модель автомобиля**

- Название
- Марка

**Автомобиль**

- Марка
- Модель
- Год выпуска (опциональный)
- Пробег (опциональный)
- Цвет (опциональный)
- Пользователь (дополнительное задание)

### Эндпоинты

**Публичные:**

- `GET /api/v1/brands` - Список марок автомобилей
- `GET /api/v1/car-models` - Список моделей автомобилей

**Требующие аутентификации:**

- `GET /api/v1/cars` - Список автомобилей пользователя
- `POST /api/v1/cars` - Создание автомобиля
- `GET /api/v1/cars/{id}` - Получение автомобиля
- `PUT /api/v1/cars/{id}` - Обновление автомобиля
- `DELETE /api/v1/cars/{id}` - Удаление автомобиля

**Аутентификация:**

- `POST /api/v1/register` - Регистрация
- `POST /api/v1/login` - Авторизация
- `POST /api/v1/logout` - Выход
- `GET /api/v1/profile` - Профиль пользователя

## 🛠️ Команды разработки

```bash
# Запуск тестов
docker-compose exec app php artisan test

# Генерация Swagger документации
docker-compose exec app php artisan l5-swagger:generate

# Миграции
docker-compose exec app php artisan migrate
docker-compose exec app php artisan migrate:fresh --seed

# Очереди (для асинхронных операций)
docker-compose exec app php artisan queue:work

# Просмотр логов
docker-compose logs -f app
```

## 🐳 Docker сервисы

- **app** - PHP 8.3 FPM + Laravel 12.x
- **nginx** - Веб-сервер (порт 8080)
- **database** - MariaDB 10.11 (порт 3308)
- **redis** - Redis 7 для кэша и очередей
- **adminer** - Управление БД (порт 8081)

## 📚 Документация

API полностью задокументирован с помощью Swagger/OpenAPI 3.0:

- **URL:** http://localhost:8080/api/documentation
- **JSON:** http://localhost:8080/api/docs.json

---
