## Migmag Laravel migrations extender 

### Install


- Version 2.0 (Compatibility with laravel 5.2)
By adding: 
```sh
   "dushevadnqka/migmag": "2.*"
```

- Version 1.0 (Compatibility with laravel 5.1)
By adding: 
```sh
   "dushevadnqka/migmag": "1.*"
```

on your composer json file.


### Add this line to your config/app.php (in Providers array)
```php
\Dushevadnqka\Migmag\Providers\MigmagServiceProvider::class
```
### To exec migration from certain file:

```php
    php artisan migmag:migrate
```

and then as a answer type the path to your migration file without extension.

### or

```php
    php artisan migmag:migrate --path=path/to/your/migration-file
```

### To get a migration status by certain migration file:

```php
    php artisan migmag:migrate:status
```
and then as a answer type the path to your migration file without extension.

### or

```php
    php artisan migmag:migrate:status --path=path/to/your/migration-file
```

### To make a migration reset by certain migration name:

```php
    php artisan migmag:migrate:reset
```
and then as a answer type the name of your migration file without extension.

### or

```php
    php artisan migmag:migrate:reset --migration=migration-file-name
```

### To make a migration refresh for certain migration:

```php
    php artisan migmag:migrate:refresh
```
and then as a answer type the path to your migration file without extension.

### or

```php
    php artisan migmag:migrate:refresh --path=path/to/your/migration-file
```

### Enjoy!
