## Migmag Laravel migrations extender 

### Install

By adding: 
```sh
   "dushevadnqka/migmag": "1.0.*"
```

on your composer json file.

### Add this line to your config/app.php (in Providers array)
```php
\Dushevadnqka\Migmag\Providers\MigmagServiceProvider::class
```
### and than:

```php
    php artisan migmag:migrate:magic
```
and then as a answer type the path to your migration file without extension.
And that's it... 
### Enjoy!