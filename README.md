## Steps to Set Up the Server-Side of the Project

To set up the server-side of the Laravel project, follow these steps:

### 1. Clone the Repository
Clone the Laravel project repository to your local machine or remote server:

```bash
git clone https://github.com/anutkaborisenko87/apzcourseproject.git <project-name>
cd <project-name>
```

### 2. Install Dependencies
Install all dependencies using Composer:
```bash
composer install
```

### 3. Environment Configuration
- Create a .env file by copying the existing template:

```bash
cp .env.example .env
```
- Generate the application key:

```bash
php artisan key:generate
```
- Set the database connection parameters in the .env file.

### 4. Database Migrations

Run database migrations and seed initial data:

```bash
php artisan migrate --seed
```
### 5. Install Laravel Passport

To enable authentication, install Laravel Passport:

```bash
php artisan passport:install
```
### 6. Start the Server
For local development, start the server with the following command:

```bash
php artisan serve
```
### 7. Optimize for Production
Run the following commands to enhance performance for production environments:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```
