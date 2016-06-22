## Migmag Laravel migrations extender 

### Install

By adding: 
```sh
   "dushevadnqka/migmag": "^2.0"
```

on your composer json file.

### Add this line to your config/app.php (in Providers array)
```php
\Dushevadnqka\Migmag\Providers\MigmagServiceProvider::class
```
### and than:

```php
    php artisan migrate:magic --path=some/path
```