<?php

namespace KodiCMS\ModulesLoader;

use Cache;
use Carbon\Carbon;
use Illuminate\Filesystem\Filesystem;

class ModulesFileSystem
{
    /**
     * @var ModuleLoader
     */
    protected $moduleLoader;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var array File path cache, used when caching is true
     */
    protected $files = [];

    /**
     * @var bool
     */
    protected $filesChanged = false;

    /**
     * @param ModulesLoader $loader
     * @param Filesystem    $filesystem
     */
    public function __construct(ModulesLoader $loader, Filesystem $filesystem)
    {
        $this->moduleLoader = $loader;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string|array|null $sub
     *
     * @return array
     */
    public function getPaths($sub = null)
    {
        $paths = [];

        foreach ($this->moduleLoader->getRegisteredModules() as $module) {
            if (is_dir($dir = $module->getPath($sub))) {
                // This path has a file, add it to the list
                $paths[$module->getName()] = $dir;
            }
        }

        return $paths;
    }

    /**
     * @param string $dir   directory name (views, i18n, classes, extensions, etc.)
     * @param string $file  filename with subdirectory
     * @param string $ext   extension to search for
     * @param bool   $array return an array of files?
     *
     * @return array  a list of files when $array is TRUE cached by 10 minutes
     * @return string single file path cached by 10 minutes
     */
    public function findFile($dir, $file, $ext = null, $array = false)
    {
        if ($ext === null) {
            // Use the default extension
            $ext = '.php';
        } elseif ($ext) {
            // Prefix the extension with a period
            $ext = ".{$ext}";
        } else {
            // Use no extension
            $ext = '';
        }

        // Create a partial path of the filename
        $path = normalize_path("{$dir}/{$file}{$ext}");

        if (isset($this->files[$path.($array ? '_array' : '_path')])) {
            // This path has been cached
            return $this->files[$path.($array ? '_array' : '_path')];
        }

        if ($array) {
            // Array of files that have been found
            $found = [];

            foreach ($this->moduleLoader->getRegisteredModules() as $module) {
                $dir = $module->getPath().DIRECTORY_SEPARATOR;

                if (is_file($dir.$path)) {
                    // This path has a file, add it to the list
                    $found[] = $dir.$path;
                }
            }
        } else {
            // The file has not been found yet
            $found = false;

            foreach ($this->moduleLoader->getRegisteredModules() as $module) {
                $dir = $module->getPath().DIRECTORY_SEPARATOR;

                if (is_file($dir.$path)) {
                    // A path has been found
                    $found = $dir.$path;

                    // Stop searching
                    break;
                }
            }
        }

        // Add the path to the cache
        $this->files[$path.($array ? '_array' : '_path')] = $found;

        // Files have been changed
        $this->filesChanged = true;

        return $found;
    }

    /**
     * @param string       $directory directory name
     * @param string|array $ext
     *
     * @return array
     */
    public function listFiles($directory = null, $ext = null)
    {
        if ($directory !== null) {
            // Add the directory separator
            $directory .= DIRECTORY_SEPARATOR;
        }

        if ($ext === null) {
            // Use the default extension
            $ext = 'php';
        }

        $paths = $this->getPaths();

        // Create an array for the files
        $found = [];

        foreach ($paths as $moduleName => $path) {
            if (is_dir($path = normalize_path($path.DIRECTORY_SEPARATOR.$directory))) {
                foreach ($this->filesystem->allFiles($path) as $file) {
                    $fileExt = $file->getExtension();

                    // Relative filename is the array key
                    $key = $file->getRelativePathname();

                    if (!empty($ext) and is_array($ext) ? !in_array($fileExt, $ext) : ($fileExt != $ext)) {
                        continue;
                    }

                    if (!isset($found[$key])) {
                        $found[$key] = $file;
                    }
                }
            }
        }

        // Sort the results alphabetically
        ksort($found);

        return $found;
    }

    /**
     * @param string $directory
     * @param string $file
     * @param string $ext
     *
     * @return string cached by 20 minutes
     */
    public function mergeFiles($directory, $file, $ext = null)
    {
        $cacheKey = 'files::merge::'.md5($directory.$file.$ext);

        $content = Cache::remember($cacheKey, Carbon::now()->minute(20), function () use ($directory, $file, $ext) {
            $return = '';
            foreach ($this->findFile($directory, $file, $ext, true) as $file) {
                $return .= file_get_contents($file).PHP_EOL.PHP_EOL;
            }

            return $return;
        });

        return $content;
    }

    /**
     * @param string|null $namespace
     *
     * @return string
     */
    public function getModuleNameByNamespace($namespace = null)
    {
        $defaultNamespace = 'app';
        $currentRoute = app('router')->getCurrentRoute();

        if (is_null($namespace) and !is_null($currentRoute)) {
            $namespace = $currentRoute->getAction()['namespace'];
        }

        if (is_null($namespace)) {
            return $defaultNamespace;
        }

        foreach ($this->moduleLoader->getRegisteredModules() as $module) {
            if (!empty($moduleNamespace = $module->getNamespace())) {
                if (strpos($namespace, $moduleNamespace) === 0) {
                    return $module->getKey();
                }
            }
        }

        return $defaultNamespace;
    }

    public function getFoundFilesFromCache()
    {
        $this->files = Cache::get('ModulesFileSystem::findFile', []);
    }

    public function cacheFoundFiles()
    {
        if ($this->filesChanged) {
            Cache::put('ModulesFileSystem::findFile', $this->files, Carbon::now()->addMinutes(10));
        }
    }
}
