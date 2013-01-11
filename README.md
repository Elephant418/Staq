Staq [![Build Status](https://travis-ci.org/Pixel418/Staq.png?branch=develop)](https://travis-ci.org/Pixel418/Staq?branch=develop)
======

Staq is a small PHP framework for an enjoyable web development.

### Features

Staq contains all the expected features : Extensible structure, routing, ORM ( *Planned* ), templating ( *Planned* ) & pre-coded applications ( *Planned* ). <br>
It mainly contains a new object pattern, *the stack*, for low dependency, high extensible & enjoyable development !



Let's coding
--------

### Hello world tutorial 

```php
require_once( 'path/to/Staq/include.php' );

\Staq\Application::create( 'Hello_World' )
    ->add_controller( '/hello/:name', function( $name ) {
        return 'Hello ' . $name;
    } )
    ->run( );
```

### System Requirements
You need **PHP >= 5.4** and some happiness.



How to Install
--------

If you don't have composer, you have to [install it](http://getcomposer.org/doc/01-basic-usage.md#installation).

Add or complete the composer.json file at the root of your repository, like this :

```json
{
    "require": {
        "pixel418/staq": "0.4"
    }
}
```

Staq can now be [downloaded via composer](http://getcomposer.org/doc/01-basic-usage.md#installing-dependencies).

The last step is to include Staq in your PHP file :

```php
require_once( './vendor/pixel418/staq/Staq/include.php' );
```



How to Contribute
--------

1. Fork the Staq repository
2. Create a new branch for each feature or improvement
3. Send a pull request from each feature branch to the **develop** branch

If you don't know much about pull request, you can read [the Github article](https://help.github.com/articles/using-pull-requests).

All pull requests must follow this particular [style guide](https://github.com/Pixel418/Style_Guide) and accompanied by passing tests.



Miscellaneous
--------

Staq is under [MIT License](http://opensource.org/licenses/MIT).  
It is created and maintained by [Thomas ZILLIOX](http://zilliox.me).  
If you have a question, you can send a message to the community via [the mailing list](mailto:staq-project@googlegroups.com).  
If you are curious on the next features, you can see my [trello board](https://trello.com/board/staq/50de3fe18942735c620000a9).
