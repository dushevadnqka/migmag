## Migmag Laravel migrations extender 

### Install

### Add this line to your config/app.php (in Providers array)
```php
\Dushevadnqka\Migmag\Providers\MigmagServiceProvider::class
```
### and than:

```php
    php artisan migrate:magic --path=some/path
```