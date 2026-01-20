# crm_translate
Переводчики

## Установка и запуск:

1. Клонируем Репозиторий `git clone git@github.com:boriscom123/crm_translate.git`
2. Подготавливаем конфигурационый файл `.env` на основе образца `.env.example`
3. Запускаем сборку докер файла `docker compoae up --build`
4. Загружаем зависимости Yii2 `docke exec -w /var/app -it {APP_NAME}-php composer install` где APP_NAME = указанному в .env
5. Добавляем необходимые изменения для подключения к БД в файл конфигурации `/app/common/main-local.php`
6. Запускаем установку миграций и первичного наполнения БД (сидерование) `docker exec -w /var/app -it {APP_NAME}-php php yii migrate --interactive=0`
7. В файл hosts добавить необходимые записи: `127.0.0.1  frontend.test` и `127.0.0.1  backend.test` на основе которых сделана конфигшурация nginx

## Проверка работоспособности

1. Основной проект доступен по адресу `http://frontend.test`
- главная страница осталось без изменений для перехода на страницу переводчиков можно воспользоваться навигацией или перейти по ссылке: `http://frontend.test/translators`
- на странице отображается список переводчиков доступных на указанную дату. Дату можно изменять в календаре.
2. Сервис предоставления доступных переводчиков доступен по адресу `http://localhost:5000/translator`
- в сервис дополнительно можно передать необходимую дату (дата должна быть в формате `yyyy-mm-dd`) например: `http://localhost:5000/translator?date=2026-01-26`
- сервис возвращает JSON ответ с датой запроса, проверкой на доступность переводчиков, их количеством и их список

### Техническая реализация (по задачам для разработки)
1. Подготовить сборку докер с сервисами nginx, hp, mysql при необходимости phpmyadmin используя docker-compose.yml
2. Установить фреймворк Yii2 advanced и настроить доступность (конфигурация nginx)
3. Создать миграции для добавления полей занятости в таблицу переводчиков  и первчное наполнение тестовыми данными (сидер)
4. Реализовать логику для записи данных о занятости переводчиков
5. Реализовать логику для выбора переводчиков в зависимости от дня (будни/выходные)
6. Создать представление для отображения доступных переводчиков с использованием Vue.js
7. Создать C# сервис, который по API возвращает статус доступности переводчиков
8. Обновить docker-compose.yml для включения C# сервиса
9. Тестирование работоспособности
10. Добавить инструкции по работе с Git для локального и удаленного репозитория
11. Добавить/обновить документацию по реализации
12. Добавить автоматическое тестирование (при необходимости)

### SQL-запросы для работы с данными

#### INSERT запросы для добавления данных о занятости переводчиков:

# Добавление переводчиков, которые работают в будни
```sql
INSERT INTO translators (name, email, weekday_availability, weekend_availability) VALUES
('Иванов Иван', 'ivanov@example.com', TRUE, FALSE),
('Петров Петр', 'petrov@example.com', TRUE, FALSE),
('Сидоров Алексей', 'sidorov@example.com', TRUE, FALSE);
```

# Аналогичный запрос с использованием Yii2:
```php
$translators = [
    ['name' => 'Иванов Иван', 'email' => 'ivanov@example.com', 'weekday_availability' => true, 'weekend_availability' => false],
    ['name' => 'Петров Петр', 'email' => 'petrov@example.com', 'weekday_availability' => true, 'weekend_availability' => false],
    ['name' => 'Сидоров Алексей', 'email' => 'sidorov@example.com', 'weekday_availability' => true, 'weekend_availability' => false],
];

foreach ($translators as $data) {
    $translator = new Translator();
    $translator->name = $data['name'];
    $translator->email = $data['email'];
    $translator->weekday_availability = $data['weekday_availability'];
    $translator->weekend_availability = $data['weekend_availability'];
    $translator->save();
}
```

# Добавление переводчиков, которые работают в выходные
```sql
INSERT INTO translators (name, email, weekday_availability, weekend_availability) VALUES
('Кузнецов Сергей', 'kuznetsov@example.com', FALSE, TRUE),
('Волков Дмитрий', 'volkov@example.com', FALSE, TRUE);
```

# Аналогичный запрос с использованием Yii2:
```php
$translators = [
    ['name' => 'Кузнецов Сергей', 'email' => 'kuznetsov@example.com', 'weekday_availability' => false, 'weekend_availability' => true],
    ['name' => 'Волков Дмитрий', 'email' => 'volkov@example.com', 'weekday_availability' => false, 'weekend_availability' => true],
];

foreach ($translators as $data) {
    $translator = new Translator();
    $translator->name = $data['name'];
    $translator->email = $data['email'];
    $translator->weekday_availability = $data['weekday_availability'];
    $translator->weekend_availability = $data['weekend_availability'];
    $translator->save();
}
```

# Добавление переводчиков, которые работают и в будни, и в выходные
```sql
INSERT INTO translators (name, email, weekday_availability, weekend_availability) VALUES
('Смирнова Анна', 'smirnova@example.com', TRUE, TRUE),
('Попова Мария', 'popova@example.com', TRUE, TRUE);
```

# Аналогичный запрос с использованием Yii2:
```php
$translators = [
    ['name' => 'Смирнова Анна', 'email' => 'smirnova@example.com', 'weekday_availability' => true, 'weekend_availability' => true],
    ['name' => 'Попова Мария', 'email' => 'popova@example.com', 'weekday_availability' => true, 'weekend_availability' => true],
];

foreach ($translators as $data) {
    $translator = new Translator();
    $translator->name = $data['name'];
    $translator->email = $data['email'];
    $translator->weekday_availability = $data['weekday_availability'];
    $translator->weekend_availability = $data['weekend_availability'];
    $translator->save();
}
```

#### SELECT запросы для выбора переводчиков в зависимости от дня:

# Выбор переводчиков, доступных в будние дни
```sql
SELECT * FROM translators WHERE weekday_availability = TRUE;
```

# Аналогичный запрос с использованием Yii2:
```php
$translators = Translator::find()
    ->where(['weekday_availability' => true])
    ->all();
```

# Выбор переводчиков, доступных в выходные дни
```sql
SELECT * FROM translators WHERE weekend_availability = TRUE;
```

# Аналогичный запрос с использованием Yii2:
```php
$translators = Translator::find()
    ->where(['weekend_availability' => true])
    ->all();
```

# Выбор переводчиков, доступных и в будни, и в выходные
```sql
SELECT * FROM translators WHERE weekday_availability = TRUE AND weekend_availability = TRUE;
```

# Аналогичный запрос с использованием Yii2:
```php
$translators = Translator::find()
    ->where(['and',
        ['weekday_availability' => true],
        ['weekend_availability' => true]
    ])
    ->all();
```

# Выбор всех доступных переводчиков для конкретного дня (например, сегодня - будний день)
```sql
SELECT * FROM translators WHERE
(CASE
    WHEN DAYOFWEEK(NOW()) IN (1, 7) THEN weekend_availability = TRUE  -- воскресенье = 1, суббота = 7
    ELSE weekday_availability = TRUE
END);
```

# Аналогичный запрос с использованием Yii2:
```php
$dayOfWeek = date('w'); // 0 (для воскресенья) через 6 (для субботы)
$isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);

if ($isWeekend) {
    $translators = Translator::find()
        ->where(['weekend_availability' => true])
        ->all();
} else {
    $translators = Translator::find()
        ->where(['weekday_availability' => true])
        ->all();
}
```

### Команды Git для работы с локальным и удаленным репозиторием

#### Сохранение кода в локальный репозиторий:

# Проверка статуса изменений
`git status`

# Добавление всех изменений к следующему коммиту
`git add .`

# Создание коммита с сообщением
`git commit -m "Добавлен функционал управления доступностью переводчиков"`

# Просмотр истории коммитов
`git log --oneline`


#### Сохранение кода в удаленный репозиторий:

# Установка удаленного репозитория (если еще не установлен)
`git remote add origin <URL_УДАЛЕННОГО_РЕПОЗИТОРИЯ>`

# Отправка изменений в удаленный репозиторий
`git push origin main`

# Если ветка main не существует на удаленной стороне, возможно потребуется:
`git push -u origin main`


#### Дополнительные полезные команды:

# Просмотр изменений перед коммитом
`git diff`

# Создание новой ветки для разработки
`git checkout -b feature/translator-availability`

# Переключение между ветками
`git checkout main`

# Объединение веток
`git merge feature/translator-availability`

# Получение последних изменений из удаленного репозитория
`git pull origin main`
