#Scripter 

```sh
$ composer require dipcms/scripter
```

```yaml  
extensions:
	scripter: DIPcms\Scripter\DI\ScripterExtension
```

```php

    /* Nette\Application\UI\Presenter */


    /**
     *
     * @var \DIPcms\Scripter\Scripter 
     */
    private $scripter;


    public function __construct(\DIPcms\Scripter\Scripter $scripter){
        $this->scripter = $scripter;
    }

    public function render(){
        $this->scripter->addCss(__DIR__.'/style.css');
    }

```

