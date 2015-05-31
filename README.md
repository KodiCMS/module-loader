# KodiCMS module loader

Пакет помогает организовать модульную структуру для фреймворка Laravel

## Установка

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

	'modules' => ['modulename', 'modulename1' => {path to module}]	
	
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