# Регистрация обработчиков событий через атрибуты php8 (1С-Битрикс) #
[![Latest Stable Version](https://poser.pugx.org/technique102/bitrix-events-attributes/v/stable.svg)](https://packagist.org/packages/technique102/bitrix-events-attributes)
[![Total Downloads](http://poser.pugx.org/technique102/bitrix-events-attributes/downloads)](https://packagist.org/packages/technique102/bitrix-events-attributes)
[![License](http://poser.pugx.org/technique102/bitrix-events-attributes/license)](https://packagist.org/packages/technique102/bitrix-events-attributes)
[![PHP Version Require](http://poser.pugx.org/technique102/bitrix-events-attributes/require/php)](https://packagist.org/packages/technique102/bitrix-events-attributes)

Пакет поможет избавиться от классической ситуации при разработке на 1С-Битрикс,
когда есть файл events.php который подключается в init.php
и в котором большое количество вызовов \Bitrix\Main\EventManager::getInstance()->addEventHandler().

Регистрация обработчика событий происходит через атрибуты,
которые указываются рядом с методом класса который и будет выполнять обработку события.

Установка через composer
-------------------------
```
composer require technique102/bitrix-events-attributes
```

Простое использование
-------------------------

Создадим класс с методами которые будут обрабатывать события.

Пометим метод атрибутом EventHandler с указанием модуля и типа события.

Методов с обработчиками в классе может быть несколько.

Более того один обработчик может вызываться в разных событиях.

Важно помнить, что параметры передаваемые в обработчик могут отличаться в зависимости от события, особенно в событиях старого ядра.

Так же можно указывать сортировку, по умолчанию 100.

``` php
use Technique102\BitrixEventsAttributes\Attributes\EventHandler;

class Handlers
{
    #[EventHandler('main', 'OnPageStart', 10)]
    #[EventHandler('main', 'OnPageStart')]
    public static function handlerOne(): void
    {
        \Bitrix\Main\Diag\Debug::writeToFile('WORK handlerOne!!!', '', 'bitrix_log.txt');
    }
    
    #[EventHandler('catalog', '\Bitrix\Catalog\Product::OnBeforeUpdate')]
    #[EventHandler('catalog', '\Bitrix\Catalog\Product::OnAfterAdd')]
    public static function handlerTwo(\Bitrix\Main\Event $e): void
    {
        \Bitrix\Main\Diag\Debug::writeToFile($e->getParameters(), '', 'bitrix_log.txt');
    }
    
    #[EventHandler('main', 'OnPageStart', 50)]
    public static function handlerThree(): void
    {
        \Bitrix\Main\Diag\Debug::writeToFile('WORK handlerThree!!!', '', 'bitrix_log.txt');
    }
}
```

Далее в init.php создаем менеджер событий и добавляем туда наш класс.

``` php
use Technique102\BitrixEventsAttributes\EventManager;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandlerClass(Handlers::class);
$eventManager->boot();
```

Можно добавлять сколько угодно классов через метод addEventHandlerClass.

### Использование через настройки модуля ###

Тут почти все то же самое, что и в простом использовании,
только добавление классов происходит через файл .settings.php в модулях.

Класс с обработчиками событий при этом лежит в модуле.

Создаем в нужном модуле файл .settings.php.

В нем описываем значения для eventHandlerClasses, примерно так:

``` php
<?php
return [
    'eventHandlerClasses' => [
        'value' => [
            \Vendor\ModuleName\EventHandlers\OnAfterUserAuthorize::class
        ],
        'readonly' => true
    ]
];
```

Класс по структуре точно такой же как и Handlers из примера с простым использованием.

Далее в init.php создаем менеджер событий (если еще не создан)
только уже без добавления класса руками.

Оба способа работают вместе.
Подключаются классы из всех модулей, в которых есть описание настройки eventHandlerClasses,
и следом подключается то что добавлили через $eventManager->addEventHandlerClass() в init.php.

Требования как и при обычном использовании \Bitrix\Main\EventManager::getInstance()->addEventHandler(),
классы с обработчиками должны быть доступны для вызовов, т.е. подгружены через автолоад модулей или кастомно.

В классе может быть 10 методов которые отвечают за обработку событий.
Если раньше для этого прописывалось 10 раз \Bitrix\Main\EventManager::getInstance()->addEventHandler(),
то сейчас будет достаточно передать класс в менеджер событий пакета $eventManager->addEventHandlerClass(Handlers::class).
