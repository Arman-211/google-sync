# 📊 Laravel Google Sheets Sync

Этот проект на **Laravel 8+** выполняет **синхронизацию данных** между базой данных и **Google Sheets**.

## ✅ Поддерживаемые функции
- ✔️ **CRUD-интерфейс** для управления записями
- ✔️ **Генерация 1000 записей** с равномерным распределением статусов
- ✔️ **Синхронизация** с Google Sheets (только записи со статусом `Allowed`)
- ✔️ **Автообновление** данных каждую минуту (Laravel Scheduler)
- ✔️ **Сохранение комментариев** из Google Sheets
- ✔️ **Удаление данных** из Google Sheets, если статус изменился
- ✔️ **Консольная команда** для получения комментариев
- ✔️ **Web-интерфейс** для управления и настройки

---

## 🚀 Установка и настройка

### 📥 1️⃣ Клонируем репозиторий
```sh
git clone <repo-url>
cd <repo-folder>
```

### 🔧 2️⃣ Устанавливаем зависимости
```sh
composer install
npm install && npm run dev
```

### 🛠️ 3️⃣ Настройка .env
```sh
cp .env.example .env
php artisan key:generate
```

Добавьте в `.env` файл параметры **БД** и **Google API**:
```ini
DB_DATABASE=your_db
DB_USERNAME=root
DB_PASSWORD=

GOOGLE_SERVICE_ACCOUNT_EMAIL=google-sync@your-project.iam.gserviceaccount.com
```

### 📊 4️⃣ Применяем миграции и сидеры
```sh
php artisan migrate --seed
```

---

## ⚙️ Настройка Google API

### 🌍 1️⃣ Включить API и создать сервисный аккаунт
1. Перейдите в [Google Cloud Console](https://console.cloud.google.com/)
2. Включите **Google Sheets API**
3. Создайте **Сервисный аккаунт** и скачайте `credentials.json`
4. Сохраните `credentials.json` в `storage/google-credentials.json`

### 🔑 2️⃣ Дать сервисному аккаунту права "Редактор"
1. Откройте **Google Sheets**
2. Нажмите **"Поделиться" (Share)**
3. Вставьте email **из `GOOGLE_SERVICE_ACCOUNT_EMAIL`**
4. Дайте ему **права "Редактор" (Editor)**

---

## 📌 Основные команды

### 🔄 Синхронизация данных
```sh
php artisan sync:google-sheet
```
> Загружает данные в Google Sheets (только `Allowed`)

### 📥 Получение комментариев
```sh
php artisan fetch:google-comments {count?}
```
> `{count}` - кол-во строк (по умолчанию все)

### ⏳ Автоматическая синхронизация
Добавьте в `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('sync:google-sheet')->everyMinute();
}
```
Запуск планировщика:
```sh
php artisan schedule:work
```

---

## 🌍 API и маршруты

| Маршрут               | Метод  | Описание |
|----------------------|--------|----------|
| `/`                  | `GET`  | Страница записей |
| `/records/generate`  | `POST` | Генерация 1000 записей |
| `/records/sync`      | `POST` | Синхронизация с Google Sheets |
| `/records/clear`     | `POST` | Очистка всех записей |
| `/settings`          | `GET`  | Настройки Google Sheets |
| `/fetch/{count?}`    | `GET`  | Выгрузка комментариев |

🎥 [Посмотреть видео](public/videos/demo.mp4)
