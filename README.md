Scripter for Nette Framework
============================

Scripter allows you to load CSS and JavaScript files for Nette Framework.



Installation
------------

The best way to install DIPcms/Scripter is using  [Composer](http://getcomposer.org/):
```sh
$ composer require dipcms/scripter
```

Minimal configuration
---------------------

```yaml
extensions:
    scripter: DIPcms\Scripter\DI\ScripterExtension
```

Settings
--------

```yaml
#Default settings
scripter:
    temp_dir: %tempDir% 
    url_path_name: 'getsource'
    default_syntax: 'asp' #synatxe: latte, double, asp, python, off
```


Rendering CSS and JS
--------------------

>Rendering is performed by Nette Latte (template engine) [latte](https://doc.nette.org/en/2.1/templating#toc-latte)
>Macros are written with the help of ASP tags. It depends on the settings. You can also use:
*latte: `{ ... }`
*double: `{{ ... }}`
*asp: `<% ... %>`
*python: `{% ... %}` and `{{ ... }}`

Details on how you can find [syntax](https://doc.nette.org/en/2.1/default-macros#toc-syntax-switching)

Macros
------

*`<%img "images.jpg"%>` Save the image to cahce and creates a link to the image

Default macro can be found [Latte](https://latte.nette.org/en/macros)


Functionality
-------------

Passed parameters

```php
   # use DIPcms/Scripter/Scripter;

   $scripter->parameter_name = "value"; 
    
```

Passed parameters

```php
   # use DIPcms/Scripter/Scripter;

   $scripter->addFile(__DIR__ . "/style.css"); 
```

Use
---

Create css file `style.css`

```css
    body{
        background: <%img "bg.jpg"%>;
        width: <%$width%>px;
    }
```


Your application

```php

    namespace App\Presenters;
    
    use Nette;
    use DIPcms\Scripter\Scripter;

    class HomepagePresenter extends Nette\Application\UI\Presenter{
    
        /**
        * @var Scripter @inject
        */
        public $scripter;
    
        public function renderDefault(){
            $this->scripter->width = 50;
            $this->scripter->addFile(__DIR__.'/style.css');
        }
    }
```

