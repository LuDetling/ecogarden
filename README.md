
# Portfolio

Portfolio a partager avec le MONDE




## Run Locally

Clone the project

```bash
  git clone https://github.com/LuDetling/ecogarden.git
```

Install dependencies

```bash
  composer up
```

Start the server

```bash
  symfony server:start
```

copy .env -> .env.local and edit

```bdd
  DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=8.0.32&charset=utf8mb4"
```

Create database

```bash
  symfony console doctrine:database:create
```

mirgate migration

```bash
  symfony console doctrine:migrations:migrate
```

load fixtures

```bash
  symfony console doctrine:fixtures:load
```

