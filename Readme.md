# Приложение для перевода фраз через Яндекс.API
Использует PHP и PostgreSQL


## Структура базы данных
Перед использованием создать таблицу 

```sql
CREATE TABLE translations (
                              id SERIAL PRIMARY KEY,
                              source_text TEXT NOT NULL,
                              source_language VARCHAR(10) NOT NULL,
                              target_language VARCHAR(10) NOT NULL,
                              translated_text TEXT,
                              created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                              updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
Как получить токен авторизации (**YANDEX_OAUTH_TOKEN**): Заходим под своим аккаунтом в яндекс и проходим по ссылке - https://oauth.yandex.ru/verification_code 

Как получить IAM токен (**YANDEX_IAM_TOKEN**): 
Подставить YANDEX_OAUTH_TOKEN в bin/iam.php и выполнить
```shell
make iam
```

Без **YANDEX_FOLDER_ID** тоже ничего работать не будет, как его получить: Переходим в Яндекс консоль и копируем его оттуда
![YANDEX_FOLDER_ID.png](YANDEX_FOLDER_ID.png)

Для работы сервиса так же необходимо назначить пользователю роль **ai.translate.user**
Иначе будет ошибка **Permission denied**