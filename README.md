#FakeMart
###Start Project
```
#install vendor file
composer install --ignore-platform-reqs
```

```
#migrate table and create sample row
php artisan migrate:fresh 
```

```
#start project in develoment mode
php artisan serve
```
### Project Name _FakeMartChain_

## Database Schema

This section documents the database schema, including all tables and their fields. The schema includes column names, data types, nullability, keys, and additional attributes for each table in the database.

### Table: branch

| Column Name     | Data Type | Nullable | Key | Extra           |
|-----------------|-----------|----------|-----|-----------------|
| id              | bigint    | NO       | PRI | auto_increment  |
| name            | varchar   | NO       |     |                 |
| location        | varchar   | NO       |     |                 |
| contact_number  | varchar   | NO       |     |                 |
| created_at      | timestamp | YES      |     |                 |
| updated_at      | timestamp | YES      |     |                 |
| deleted_at      | timestamp | YES      |     |                 |

### Table: cache

| Column Name | Data Type   | Nullable | Key | Extra |
|-------------|-------------|----------|-----|-------|
| key         | varchar     | NO       | PRI |       |
| value       | mediumtext  | NO       |     |       |
| expiration  | int         | NO       |     |       |

### Table: cache_locks

| Column Name | Data Type | Nullable | Key | Extra |
|-------------|-----------|----------|-----|-------|
| key         | varchar   | NO       | PRI |       |
| owner       | varchar   | NO       |     |       |
| expiration  | int       | NO       |     |       |

### Table: category

| Column Name | Data Type | Nullable | Key | Extra           |
|-------------|-----------|----------|-----|-----------------|
| id          | bigint    | NO       | PRI | auto_increment  |
| name        | varchar   | NO       |     |                 |
| description | text      | NO       |     |                 |
| created_at  | timestamp | YES      |     |                 |
| updated_at  | timestamp | YES      |     |                 |

### Table: failed_jobs

| Column Name | Data Type | Nullable | Key | Extra             |
|-------------|-----------|----------|-----|-------------------|
| id          | bigint    | NO       | PRI | auto_increment    |
| uuid        | varchar   | NO       | UNI |                   |
| connection  | text      | NO       |     |                   |
| queue       | text      | NO       |     |                   |
| payload     | longtext  | NO       |     |                   |
| exception   | longtext  | NO       |     |                   |
| failed_at   | timestamp | NO       |     | DEFAULT_GENERATED |

### Table: invoice

| Column Name | Data Type | Nullable | Key | Extra           |
|-------------|-----------|----------|-----|-----------------|
| id          | bigint    | NO       | PRI | auto_increment  |
| user_id     | bigint    | NO       |     |                 |
| total       | decimal   | NO       |     |                 |
| created_at  | timestamp | YES      |     |                 |
| updated_at  | timestamp | YES      |     |                 |
| deleted_at  | timestamp | YES      |     |                 |

### Table: invoice_item

| Column Name | Data Type | Nullable | Key | Extra           |
|-------------|-----------|----------|-----|-----------------|
| id          | bigint    | NO       | PRI | auto_increment  |
| invoice_id  | bigint    | NO       | MUL |                 |
| product_id  | bigint    | NO       | MUL |                 |
| qty         | int       | NO       |     |                 |
| price       | double    | NO       |     |                 |
| created_at  | timestamp | YES      |     |                 |
| updated_at  | timestamp | YES      |     |                 |
| deleted_at  | timestamp | YES      |     |                 |

### Table: job_batches

| Column Name     | Data Type | Nullable | Key | Extra |
|-----------------|-----------|----------|-----|-------|
| id              | varchar   | NO       | PRI |       |
| name            | varchar   | NO       |     |       |
| total_jobs      | int       | NO       |     |       |
| pending_jobs    | int       | NO       |     |       |
| failed_jobs     | int       | NO       |     |       |
| failed_job_ids  | longtext  | NO       |     |       |
| options         | mediumtext| YES      |     |       |
| cancelled_at    | int       | YES      |     |       |
| created_at      | int       | NO       |     |       |
| finished_at     | int       | YES      |     |       |

### Table: jobs

| Column Name  | Data Type | Nullable | Key | Extra           |
|--------------|-----------|----------|-----|-----------------|
| id           | bigint    | NO       | PRI | auto_increment  |
| queue        | varchar   | NO       | MUL |                 |
| payload      | longtext  | NO       |     |                 |
| attempts     | tinyint   | NO       |     |                 |
| reserved_at  | int       | YES      |     |                 |
| available_at | int       | NO       |     |                 |
| created_at   | int       | NO       |     |                 |

### Table: migrations

| Column Name | Data Type | Nullable | Key | Extra           |
|-------------|-----------|----------|-----|-----------------|
| id          | int       | NO       | PRI | auto_increment  |
| migration   | varchar   | NO       |     |                 |
| batch       | int       | NO       |     |                 |

### Table: password_reset_tokens

| Column Name | Data Type | Nullable | Key | Extra |
|-------------|-----------|----------|-----|-------|
| email       | varchar   | NO       | PRI |       |
| token       | varchar   | NO       |     |       |
| created_at  | timestamp | YES      |     |       |

### Table: personal_access_tokens

| Column Name     | Data Type | Nullable | Key | Extra           |
|-----------------|-----------|----------|-----|-----------------|
| id              | bigint    | NO       | PRI | auto_increment  |
| tokenable_type  | varchar   | NO       | MUL |                 |
| tokenable_id    | bigint    | NO       |     |                 |
| name            | varchar   | NO       |     |                 |
| token           | varchar   | NO       | UNI |                 |
| abilities       | text      | YES      |     |                 |
| last_used_at    | timestamp | YES      |     |                 |
| expires_at      | timestamp | YES      |     |                 |
| created_at      | timestamp | YES      |     |                 |
| updated_at      | timestamp | YES      |     |                 |

### Table: position

| Column Name  | Data Type | Nullable | Key | Extra           |
|--------------|-----------|----------|-----|-----------------|
| id           | bigint    | NO       | PRI | auto_increment  |
| branch_id    | bigint    | NO       | MUL |                 |
| name         | varchar   | NO       |     |                 |
| description  | text      | YES      |     |                 |
| created_at   | timestamp | YES      |     |                 |
| updated_at   | timestamp | YES      |     |                 |
| deleted_at   | timestamp | YES      |     |                 |

### Table: product

| Column Name  | Data Type | Nullable | Key | Extra           |
|--------------|-----------|----------|-----|-----------------|
| id           | bigint    | NO       | PRI | auto_increment  |
| name         | varchar   | NO       |     |                 |
| cost         | double    | NO       |     |                 |
| price        | double    | NO       |     |                 |
| image        | varchar   | YES      |     |                 |
| description  | text      | YES      |     |                 |
| category_id  | bigint    | NO       | MUL |                 |
| created_at   | timestamp | YES      |     |                 |
| updated_at   | timestamp | YES      |     |                 |
| deleted_at   | timestamp | YES      |     |                 |

### Table: refresh_tokens

| Column Name | Data Type | Nullable | Key | Extra           |
|-------------|-----------|----------|-----|-----------------|
| id          | bigint    | NO       | PRI | auto_increment  |
| user_id     | bigint    | NO       | MUL |                 |
| token       | varchar   | NO       | UNI |                 |
| expires_at  | timestamp | NO       |     |                 |
| created_at  | timestamp | YES      |     |                 |
| updated_at  | timestamp | YES      |     |                 |

### Table: sessions

| Column Name   | Data Type | Nullable | Key | Extra |
|---------------|-----------|----------|-----|-------|
| id            | varchar   | NO       | PRI |       |
| user_id       | bigint    | YES      | MUL |       |
| ip_address    | varchar   | YES      |     |       |
| user_agent    | text      | YES      |     |       |
| payload       | longtext  | NO       |     |       |
| last_activity | int       | NO       | MUL |       |

### Table: staff

| Column Name     | Data Type | Nullable | Key | Extra           |
|-----------------|-----------|----------|-----|-----------------|
| id              | bigint    | NO       | PRI | auto_increment  |
| position_id     | bigint    | NO       | MUL |                 |
| name            | varchar   | NO       |     |                 |
| gender          | varchar   | NO       |     |                 |
| dob             | date      | NO       |     |                 |
| pob             | varchar   | NO       |     |                 |
| address         | varchar   | NO       |     |                 |
| phone           | varchar   | NO       |     |                 |
| nation_id_card  | varchar   | NO       |     |                 |
| created_at      | timestamp | YES      |     |                 |
| updated_at      | timestamp | YES      |     |                 |
| deleted_at      | timestamp | YES      |     |                 |

### Table: users

| Column Name       | Data Type | Nullable | Key | Extra           |
|-------------------|-----------|----------|-----|-----------------|
| id                | bigint    | NO       | PRI | auto_increment  |
| name              | varchar   | NO       |     |                 |
| email             | varchar   | NO       | UNI |                 |
| email_verified_at | timestamp | YES      |     |                 |
| password          | varchar   | NO       |     |                 |
| staff_id          | bigint    | NO       |     |                 |
| remember_token    | varchar   | YES      |     |                 |
| created_at        | timestamp | YES      |     |                 |
| updated_at        | timestamp | YES      |     |                 |

## Notes
- The schema was generated from the MySQL database using `INFORMATION_SCHEMA` queries.

