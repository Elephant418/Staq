Staq
======
Staq is a small PHP framework for a enjoyable web development.

### Features

1. **Stack**: A new object pattern for extensible & low dependency development ;
2. **Routing**: An adaptable routing system for [small](#hello-world-tutorial) & big projects ;
3. **Model**: *Planned for the version v0.5* ;
4. **Extendable applications**: *Planned for the version v0.7*.

### License

Staq is under [MIT License](http://opensource.org/licenses/MIT)

### Community

Staq is created and maintained by [Thomas ZILLIOX](http://zilliox.me). 
If you have a question, you can send a message to the community via [the mailing list](staq-project@googlegroups.com). 



Getting Started
--------

### System Requirements
You need **PHP >= 5.4** and some happiness.

### Hello world tutorial 

```php
require_once( 'path/to/Staq/include.php' );

\Staq\Application::create( 'Hello_World' )
    ->add_controller( '/*', function( ) {
        return 'Hello World';
    } )
    ->run( );
```



Roadmap
--------
The last stable version is [v0.2](https://github.com/Pixel418/Staq/tree/v0.2).

I am working hard on the [v0.3](https://github.com/Pixel418/Staq/tree/v0.3). <br>
If you are curious on the next features, you can see my [trello board](https://trello.com/board/staq/50de3fe18942735c620000a9).
