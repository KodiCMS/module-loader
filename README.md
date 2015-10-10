# Laravel module loader
Пакет помогает организовать модульную структуру для фреймворка Laravel.
https://packagist.org/packages/kodicms/module-loader

## Установка

Для установки пакета вы можете выполнить консольную комманду

`composer require "kodicms/module-loader:~2.0"`

Или добавить пакет в `composer.json`
<pre>
{
  "require": {
    ...
    "kodicms/module-loader": "~2.0"
       ...
  }
}
</pre>


###Добавить в загрузку сервис провайдер
<pre>
'providers' => [
  ...
  KodiCMS\ModulesLoader\Providers\ModuleServiceProvider::class,
  ...
],
</pre>

### В `composer.json` добавить пространство имен
<pre>
{
  "autoload": {
    ...
    "psr-4": {
      ...
      "Modules\\"  : "modules/"
    },
    ...
  },
}
</pre>

### Конфиг файл `app.php`
По умолчанию модули системы хранятся в папке `modules`, если вам необходимо загрузить модуль из другой директории,
то вы должны указать через конфиг файл путь до модуля и его пространство имен:

<pre>
return [
  ...
  'modules' => [
    'modulename', // Namespace: Modules\modulename, Path baseDir/modules/modulename
    'ModuleName2' => [
      'path' => {relative path to module},
      'namespace' => '\\CustomNamespace\\ModuleName2\\'
    ]
  ]
  ...
];
</pre>

----------

По умолчанию загрузчик при подключении модуля использует `KodiCMS\ModulesLoader\ModuleContainer`, вы можете 
переопределить файл контейнера создав его по пути `app\DefaultModuleContainer.php` или если вы захотите 
изменить поведение конкретного модуля, то необходимо создать файл `ModuleContainer.php` в корне директории модуля 
и наследовать его от `KodiCMS\ModulesLoader\ModuleContainer`.

## Структура модуля
https://github.com/KodiCMS/kodicms-laravel/wiki/%D0%9C%D0%BE%D0%B4%D1%83%D0%BB%D0%B8

----------

Для просмотра списка подключенных модулей в системе используйте консольную команду:  

`php artisan modules:list`

![](https://dl.dropboxusercontent.com/u/1110641/kodicms-wiki/modulesList.png)

----------

Для выполнения миграций из модулей используте консольную команду:

`php artisan modules:migrate`  *(Доступны параметры аналогичные для стандартной команды `migrate`)*

Для сидирования данных из модулей используте консольную команду:

`php artisan modules:seed`


