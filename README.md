Staq
======

Presentation
--------
Staq is a mini PHP framework for *modular & maintainable website*.<br>
Staq means "Stack query", it allow you to instantiate a stack of classes through several extensions.

Instead of directly instantiate a class: <code>new \Model\User</code> or <code>new \My_Extension\Model\User</code>,<br>
You can instantiate a stack: <code>new \Stack\Model\User</code>.

It's great because :

* You can instantiate a stack even before defining his classes ;
* You can add classes to a stack when ever you want ;
* You can add an external extension to complete your own stack ;
* The stacks are powerfull, but still readable. 


License
--------
Staq is under [MIT License](http://opensource.org/licenses/MIT)


Roadmap
--------
The stable version is [v0.2](https://github.com/Pixel418/Staq/tree/v0.2).

We are working hard on the [v0.3](https://github.com/Pixel418/Staq/tree/v0.3). <br>
If you are curious on the next features, you can see our [trello board](https://trello.com/board/staq/50de3fe18942735c620000a9).
