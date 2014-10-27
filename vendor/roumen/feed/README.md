# [laravel-feed](http://roumen.it/projects/laravel-feed)

[![Latest Stable Version](https://poser.pugx.org/roumen/feed/version.png)](https://packagist.org/packages/roumen/feed) [![Total Downloads](https://poser.pugx.org/roumen/feed/d/total.png)](https://packagist.org/packages/roumen/feed) [![Build Status](https://travis-ci.org/RoumenDamianoff/laravel-feed.png?branch=master)](https://travis-ci.org/RoumenDamianoff/laravel-feed) [![License](https://poser.pugx.org/roumen/feed/license.png)](https://packagist.org/packages/roumen/feed)

A simple feed generator for Laravel 4.


## Installation

Add the following to your `composer.json` file :

```json
"roumen/feed": "dev-master"
```

Then register this service provider with Laravel :

```php
'Roumen\Feed\FeedServiceProvider',
```

And add an alias to app.php:

```php
'Feed' => 'Roumen\Feed\Facades\Feed',
```

## Examples

[How to generate basic feed (with optional caching)](https://github.com/RoumenDamianoff/laravel-feed/wiki/basic-feed)

and more in the [Wiki](https://github.com/RoumenDamianoff/laravel-feed/wiki)