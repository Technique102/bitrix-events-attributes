# Регистрация обработчиков событий через атрибуты php8 (1С-Битрикс) #

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

Использование
-------------------------
Создадим класс с методом который будет обрабатывать событие OnPageStart модуля main.

Пометим метод атрибутом EventHandler с указанием модуля и типа события.

``` php
use Technique102\BitrixEventsAttributes\Attributes\EventHandler;

class Handlers
{
    #[EventHandler('main', 'OnPageStart')]
    public static function handle()
    {
        \Bitrix\Main\Diag\Debug::writeToFile('WORK!!!', '', 'bitrix_log.txt');
    }
}
```
Методов обработчиков в классе может быть несколько.

Далее в init.php создаем менеджер событий и добавляем туда наш класс.

``` php
use Technique102\BitrixEventsAttributes\EventManager;

$eventManager = EventManager::getInstance();
$eventManager->addEventHandlerClass(Handlers::class);
$eventManager->boot();
```
Можно добавлять несколько классов.

Требования как и при обычном использовании \Bitrix\Main\EventManager::getInstance()->addEventHandler(),
классы с обработчиками должны быть доступны для вызовов, т.е. подгружены через автолоад модулей или кастомно.

В классе может быть 10 методов которые отвечают за обработку событий.
Если раньше для этого прописывалось 10 раз \Bitrix\Main\EventManager::getInstance()->addEventHandler(),
то сейчас будет достаточно передать класс в менеджер событий пакета $eventManager->addEventHandlerClass(Handlers::class).
