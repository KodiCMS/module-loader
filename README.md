# Laravel module loader

Пакет помогает организовать модульную структуру для фреймворка Laravel

## Установка

`composer require 'kodicms/module-loader:5.0.*@dev'`

###Добавить в загрузку сервис провайдеры:
<pre>
/*
 * KodiCMS Service Providers...
 */
'KodiCMS\ModuleLoader\Providers\ModuleServiceProvider',
'KodiCMS\ModuleLoader\Providers\RouteServiceProvider',
'KodiCMS\ModuleLoader\Providers\AppServiceProvider',
'KodiCMS\ModuleLoader\Providers\ConfigServiceProvider',
</pre>

### Добавить фасад в алиасы

<pre>
'ModuleLoader' => 'KodiCMS\ModuleLoader\ModuleLoaderFacade',
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
<pre>
retunrn [

	...

	'modules' => [
		'modulename', 
		'modulename1' => {path to module},
		'ModuleName2' => [
			'path' => {custom path to module},
			'namespace' => '\\CustomPath\\ModuleName2\\'
		]
	]	
	
	...

];
</pre>

----------

По умолчанию модули системы хранятся в папке `modules`, если вам необходимо загрузить модуль из другой директории, то вы должны указать через конфиг файл путь до модуля и создать в папке модуля файл `ModuleContainer.php` и указать в нем пространство имен для данного модуля:

<pre>
use KodiCMS\ModuleLoader\ModuleContainer as BaseModuleContainer;

class ModuleContainer extends BaseModuleContainer
{
	/**
	 * @var string
	 */
	protected $namespace = 'Custom\Namespace';

</pre>

По умолчанию загрузчик при подключении модуля использует `KodiCMS\ModuleLoader\ModuleContainer`, вы можете переопределить файл контейнера создав его по пути `app\ModuleLoader\ModuleContainer.php` или если вы захотите изменить поведение конкретного модуля, то необходимо создать файл в корне директории модуля `ModuleContainer.php` и наследовать его от `KodiCMS\ModuleLoader\ModuleContainer`

## Структура модуля

Структура модуля аналогична структуре той, что в папке `app/`

 * `config` - конфиги приложения, могут быть перезаписаны из папки `/config/`
 * `Console`
  * `Commands` - расположение файлов консольных компанды
 * `database`
 * `Http`
  * `Controllers` - контроллеры модуля
  * `Middleware`
  * `routes.php` - роуты текущего модуля, оборачиваются в неймспейс (По умолчанию: `Modules\{module}`)
 * `Observers` - Наблюдатели для моделей Eloquent
 * `Providers`
  * `ModuleServiceProvider.php` - Сервис провайдер, если есть, будет запущен в момент инициализации приложения
 * `resources`
  * `lang` - Файлы переводов для модуля, доступны по ключу названия модуля приведенного в нижний регистр `trans('{module}::file.key')`
  * `views` - Шаблоны модуля, доступны по ключу названия модуля приведенного в нижний регистр `view('{module}::template')`
 * `Services` - Сервисные контейнеры
 * `ModuleContainer.php` - Если данный файл существует, то он будет подключен как системный файл модуля, в котором указаны относительыне пути и действия в момент инициализации. Необходимо наследовать от `KodiCMS\ModuleLoader\ModuleContainer`

### Основные методы

 * `addModule($modulename. $modulepath = null, , $namespace = null)` - добавление модуля в загрузчик
 * `getRegisteredModules()` - получение списка объектов модулей
	<pre>
	ModuleLoader::getRegisteredModules()

	// return
	[
		ModuleContainerInterface $module,
		...
		ModuleContainerInterface $moduleX
	]
	</pre>
 * `getPaths($subpath = null)` - получение массива путей относительно каждого модуля. Пригодится если вам необходимо получить спиcок всех модулей у которых есть директория `/modulename/resources` метод выведет
	<pre>
	ModuleLoader::getPaths('resources')

	// return
	[
		'modulename' => '{dir}/modulename/resources',
		...
	]
	</pre>
 * `findFile($dir, $file, $ext = null, $array = false)` - поиск определенного файла либо во всех модулях. Возвращает (путь|список путей) найденых файлов. Поиск файлов кешируется на 10 минут.
	<pre>
	ModuleLoader::findFile('resources/js', 'PagesController', 'js')

	// return
	{dir}/Pages/resources/js/PagesController.js

	ModuleLoader::findFile('resources', 'packages', 'php', true)

	//return
	[
		{dir}/CMS/resources/packages.php,
		{dir}/Cron/resources/packages.php,
		{dir}/Widgets/resources/packages.php
	]

	</pre>

### Основные методы модуля

 * `getName()` - получение названия модуля
 * `getNamespace()` - получение неймспейса
 * `getPath($subpath = NULL)` - получение абсолютного пути до модуля или пути до переданного пути относительно модуля
 * `getLocalePath()` - путь до языковых файлов
 * `getViewsPath()` - путь до шаблонов
 * `getConfigPath()` - путь до конфигов
