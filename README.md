# Laravel module loader

Пакет помогает организовать модульную структуру для фреймворка Laravel.

## Установка

`composer require kodicms/module-loader`
https://packagist.org/packages/kodicms/module-loader

###Добавить в загрузку сервис провайдеры
<pre>
/*
 * KodiCMS Service Providers...
 */
KodiCMS\ModulesLoader\Providers\ModuleServiceProvider::class,
KodiCMS\ModulesLoader\Providers\RouteServiceProvider::class,
KodiCMS\ModulesLoader\Providers\AppServiceProvider::class,
KodiCMS\ModulesLoader\Providers\ConfigServiceProvider::class,
</pre>

### Добавить фасад в алиасы
<pre>
'ModulesLoader'     => KodiCMS\ModulesLoader\ModulesLoaderFacade::class,
'ModulesFileSystem' => KodiCMS\ModulesLoader\ModulesFileSystemFacade::class,
</pre>


### В `composer.json` добавить пространство имен
<pre>
"autoload": {
	...
	"psr-4": {
		...
		"Modules\\"    : "modules/"
	},
	...
},
</pre>

### Конфиг файл `app.php`
По умолчанию модули системы хранятся в папке `modules`, если вам необходимо загрузить модуль из другой директории, то вы должны указать через конфиг файл путь до модуля и его пространство имен:

<pre>
return [
	...
    'modules' => [
    	'modulename', // Namespace: KodiCMS\modulename, Path baseDir/modules/modulename
    	'ModuleName2' => [
    		'path' => {relative path to module},
    		'namespace' => '\\CustomNamespace\\ModuleName2\\'
    	]
    ]
    ...
];
</pre>

----------

По умолчанию загрузчик при подключении модуля использует `KodiCMS\ModulesLoader\ModuleContainer`, вы можете переопределить файл контейнера создав его по пути `app\DefaultModuleContainer.php` или если вы захотите изменить поведение конкретного модуля, то необходимо создать файл `ModuleContainer.php` в корне директории модуля и наследовать его от `KodiCMS\ModulesLoader\ModuleContainer`.

## Структура модуля
https://github.com/KodiCMS/kodicms-laravel/wiki/%D0%9C%D0%BE%D0%B4%D1%83%D0%BB%D0%B8

----------

Для просмотра списка подключенных модулей в системе используйте консольную команду:  
`php artisan modules:list`

![](https://dl.dropboxusercontent.com/u/1110641/kodicms-wiki/modulesList.png)
