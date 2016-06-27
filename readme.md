## Migmag Laravel migrations extender 

### Install


- ### Version 2.0 (Compatibility with version laravel 5.2)
By adding: 
```sh
   "dushevadnqka/migmag": "2.0.*"
```

- ### Version 1.0 (Compatibility with version laravel 5.1)
By adding: 
```sh
   "dushevadnqka/migmag": "1.0.*"
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

### To get a migration status by certain migration file:

```php
    php artisan migmag:migrate:status
```
and then as a answer type the path to your migration file without extension.


### Enjoy!
