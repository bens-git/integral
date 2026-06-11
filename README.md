# Integral

## Installation

### Prerequisites

- PHP >= 8.2
- Composer
- Node.js >= 20.x
- NPM or Yarn
- MariaDB (or another supported database)

### Steps

1. **Clone the repository and enter the project directory**
   ```bash
   git clone https://github.com/bens-git/integral.git
   cd integral
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Set up the environment file**
   ```bash
   cp .env.example .env
   ```
   Update `.env` with your local configuration. The default settings use MariaDB. Ensure your database configuration matches your setup.

4. **Generate the application key**
   ```bash
   php artisan key:generate
   ```

5. **Create the database**
    create a database and add a user

5. **Run database migrations**
   ```bash
   php artisan migrate:fresh --seed
   ```

6. **Install frontend dependencies and build assets**
   ```bash
   npm install
   npm run build
   ```

7. **Start the development server**
   ```bash
   composer run dev
   ```
   This starts the Laravel server, queue worker, logs, and Vite dev server concurrently.

### Running Tests

```bash
composer test
```

### Scripts

| Command | Description |
|---------|-------------|
| `composer install` | Install PHP dependencies |
| `composer run dev` | Start local dev environment |
| `composer run setup` | Run setup (install, .env, key, migrate, npm install, build) |
| `composer test` | Run PHPUnit tests |
| `npm run build` | Build frontend assets for production |
| `npm run dev` | Start Vite dev server |
