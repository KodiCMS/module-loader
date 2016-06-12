# Laravel module loader
Пакет помогает организовать модульную структуру для фреймворка Laravel.

https://packagist.org/packages/kodicms/module-loader

## Установка

Для установки пакета вы можете выполнить консольную комманду

`composer require "kodicms/module-loader:3.*"`

Или добавить пакет в `composer.json`
<pre>
{
  "require": {
    ...
    "kodicms/module-loader": "3.*"
       ...
  }
}
</pre>


##### Добавить в загрузку сервис провайдер
<pre>
'providers' => [
  ...
  KodiCMS\ModulesLoader\Providers\ModuleServiceProvider::class,
  ...
],
</pre>

##### В `composer.json` добавить пространство имен
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

##### Конфиг файл `app.php`
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

### Структура модуля

 * `Assets` - ассеты, которые будут скопированы в `public/cms/modules/{module}`
 * `config` - конфиги модуля. Если в модулях есть конфиг файлы с одинаковым названием, то их содержимое мерджится
 * `Console`
   * `Commands` - расположение файлов консольных компанды
 * `database`
   * `migrations` - файлы миграции, будут запущены по команде `modules:migrate`
   * `seeds`
     * `DatabaseSeeder.php` - если существует, то будет запущен по команде `modules:seed`
 * `Http`
   * `Controllers` - контроллеры модуля
   * `routes.php` - роуты текущего модуля, оборачиваются в неймспейс `Modules\{module}`
 * `Providers`
   * `ModuleServiceProvider.php` - Сервис провайдер модуля, если файл существует, будет запущен в момент инициализации приложения
 * `resources`
   * `lang` - Файлы переводов для модуля, доступны по ключу названия модуля приведенного в нижний регистр `trans('{module}::file.key')`
   * `views` - Шаблоны модуля, доступны по ключу названия модуля приведенного в нижний регистр `view('{module}::template')`
 * `ModuleContainer.php` - Если данный файл существует, то он будет подключен как системный файл модуля, в котором указаны относительыне пути и действия в момент инициализации. Необходимо наследовать от `KodiCMS\ModulesLoader\ModuleContainer`

----------

### Консольные команды

 * `php artisan modules:list` - просмотр списка подключенных модулей  
 * `php artisan modules:migrate` - выполнение миграций из модулей *(Доступны параметры аналогичные для стандартной команды `migrate`)*
 * `php artisan modules:seed` - сидирование данных из модулей
 * `php artisan modules:assets:publish` - публикация assets файлов из папки Assets в `public/modules/{module}`
