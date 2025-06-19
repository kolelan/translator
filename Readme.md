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