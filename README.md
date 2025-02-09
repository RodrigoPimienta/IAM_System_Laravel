# IAM System

## Description
This project is an Identity and Access Management (IAM) system developed with Laravel 11. Its objective is to manage user authentication and permissions through a system of profiles and custom roles.

## Technologies Used
- Laravel 11
- Sanctum (for API authentication)
- Rate Limiting (Per IP and user)
- CORS policy
- SQLite (Database)
- PHP 8+
- Darkaonline/L5-Swagger (for API documentation)
- Zircote/swagger-php (for API documentation)
- PHPUnit (testing)

## Installation and Configuration

1. Clone the repository:
   ```bash
   git clone https://github.com/RodrigoPimienta/IAM_System_Laravel.git
   cd iam-system
   ```
2. Install dependencies:
   ```bash
   composer install
   ```
3. Copy the environment file and configure it:
   ```bash
   cp .env.example .env
   ```
4. Generate the application key:
   ```bash
   php artisan key:generate
   ```
5. Configure the database in the `.env` file.
6. Run migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
7. Install Darkaonline/L5-Swagger
   ```bash
   composer require "darkaonline/l5-swagger"
   php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"
   composer require "zircote/swagger-php"
   php artisan l5-swagger:generate
   ```
8. Start the server:
   ```bash
   php artisan serve
   ```

## Authentication and Permissions
The system uses Sanctum for token-based authentication and a permissions model based on profiles and roles:

- A user can only have one active profile at a time.
- A profile can be assigned to multiple users.
- A module has many permissions and roles.
- A role may or may not have permissions.
- A role can only belong to one module.
- A permission can only belong to one module.
- A profile can have multiple roles.

To determine a user's permissions, the following flow is followed:
1. Check if the user has a profile.
2. Retrieve the active roles from the profile.
3. Identify the modules to which each role belongs.
4. List the active permissions for each role.
5. Additionally, only active (status = 1) profiles, roles, permissions, and modules are considered; otherwise, the module and all its roles, the role and all its permissions, or just the specific permission will be ignored depending on the inactive status (status = 0).

## Custom Middleware
A custom middleware `CheckPermissions` has been developed, which receives two parameters: `module` and `permission`. This middleware retrieves the permissions of the authenticated user (validated by Sanctum token) and verifies whether the user has access to the module and the required permission.

Example usage in routes:
```php
Route::get('/modules', 'all')->middleware(CheckPermissions::class . ':modules,show');
```

## Documentation with Swagger
The `darkaonline/l5-swagger` library has been integrated to generate API documentation. To generate the documentation, run:
```bash
php artisan l5-swagger:generate
```
Then, access the documentation at:
```
http://localhost:8000/api/documentation
```

## Testing with PHPUnit
Automated tests have been developed using PHPUnit to ensure proper authentication and permission functionality. To run the tests:
```bash
php artisan test
```

## Contributions
If you wish to contribute, feel free to fork the repository and submit a pull request.

## Contact
For any inquiries or suggestions, you can contact me at rodrigopimienta28@gmail.com or https://www.linkedin.com/in/rodigopimienta/. 

