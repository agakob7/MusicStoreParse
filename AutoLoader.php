<?php

class AutoLoader
{

    static public function loader($className)
    {


        $className = ltrim($className, '\\');

        $filename = $namespace = null;

        if ($lastNsPos = strrpos($className, '\\')) {
            $className = substr($className, $lastNsPos + 1);
        }

        $filename .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';


        $directories = [
            ROOT, HELPERS, DRIVERS, VENDOR, MODEL
        ];

        foreach ($directories as $directory)
            if (file_exists($directory . $filename))
                include_once($directory . $filename);

    }

}

spl_autoload_register(array('AutoLoader', 'loader'));