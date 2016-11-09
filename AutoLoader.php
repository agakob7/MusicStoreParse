<?php

class AutoLoader
{

    static public function loader($className)
    {


        $className = ltrim($className, '\\');
        $fileName = '';
        $namespace = '';
        if ($lastNsPos = strrpos($className, '\\')) {
            $namespace = substr($className, 0, $lastNsPos);
            $className = substr($className, $lastNsPos + 1);
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
        }

        $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

        if (file_exists(__ROOT__ . $fileName))
            include(__ROOT__ . $fileName);

        if (file_exists(__DRIVERS__ . $fileName))
            include(__DRIVERS__ . $fileName);

        if (file_exists(__VENDOR__ . $fileName))
            include __VENDOR__ . $fileName;


        #  print_r(get_included_files());
    }

}

spl_autoload_register(array('AutoLoader', 'loader'));