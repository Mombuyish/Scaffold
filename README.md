<p align="center"><img src="https://i.imgur.com/0TEljAG.png"></p>

# Scaffold
Super fast build CRUD resource for Laravel.

# Installation
```
$ composer require yish/scaffold
```
5.5 or later:
*Auto discovery*

previous version
```
Yish\Scaffold\ScaffoldServiceProvider::class
```

# Publish config (require)
```
$ php artisan vendor:publish
```

# Usage
```
$ php artisan make:scaffold Post
```

It will be generating blong:
* request
* controller (including CRUD)
* migration
* model
* factory
* views {index, create, edit, show}
* append Route::resource(...) to specific route file.

# Option
```
$ php artisan make:scaffold Post --route=api
```
