# Helm Project

This is where we will have the codebase for the Helm project. This app is a Laravel application.

## Steps on How to Start Using the Project and Running Things Locally

### Running Laravel Project Locally

1. Ensure you have PHP, Composer, Node.js, and MySQL installed on your laptop.
2. Clone the repository.
3. Run `composer install` in the root directory.
4. Run `npm install` in the root directory.
5. Create a database in MySQL.
6. Make a copy of the .env.example and rename it .env.
7. Add your database credentials to the `.env` file of the Laravel project:
    - You can follow directions on the Laravel site to help with this.
    - Ensure your user can modify the database.
8. Add these Azure credentials to your .env to use SSO locally:
9. Run `php artisan migrate`. This will create the tables.
10. Run `php artisan db:seed` to seed the database:
    - If this fails, you might need to comment out a seeder file.
11. Run `php artisan key:generate` to set the application key.

### Creating a New User and Assigning a Role

1. Open your terminal and navigate to the root directory of your Laravel project.
2. Enter the Tinker console by running:
    ```bash
    php artisan tinker
    ```
3. Create a new user with the following command, replacing `name`, `email`, and `password` with the actual values:
    ```php
    $user = new App\Models\User;
    $user->name = 'John Doe';
    $user->email = 'john@example.com';
    $user->password = bcrypt('your-password');
    $user->save();
    ```
4. Assign a role to the user. You can use either 'creation' or 'administrator' as the role. For example:
    ```ph
    $user->assignRole('administrator');
    ```
5. Exit the Tinker console:
    ```php
    exit
    ```

## Other

If you run into issues or have any questions, please contact Rhea or Scott. Thanks!

