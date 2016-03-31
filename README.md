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

>Rendering is performed by [Nette Latte](https://doc.nette.org/en/2.1/templating#toc-latte) (template engine)
>Macros are written with the help of ASP tags. It depends on the settings. You can also use:

* latte: `{ ... }`
* double: `{{ ... }}`
* asp: `<% ... %>`
* python: `{% ... %}` and `{{ ... }}`

Details on how you can find [syntax](https://doc.nette.org/en/2.1/default-macros#toc-syntax-switching)

Macros
------

* `<%file "font.ttf"%>` Save the file to cahce and creates a link to the file.
* `<%img "images.jpg"%>` Save the image to cahce and creates a link to the image.

Default macro can be found [Latte](https://latte.nette.org/en/macros)


Functionality
-------------

Adding Parameters to file

```php
   # use DIPcms/Scripter/Scripter;

   $scripter->parameter_name = "value"; 
    
```

Add file

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

Scripter automatically add a link in the header to the CSS and JS file.

```html
    <head>
	<meta charset="utf-8">  
        #............
        <script type="text/javascript" src="/getsource/6666cd76f96956469e7be39d750cc7d9/js"></script>
        <link rel="stylesheet" href="/getsource/6666cd76f96956469e7be39d750cc7d9/css">
    </head>
```