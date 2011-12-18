<?php

function __autoload($className) {
    $directories = array(
            'app'
    );

    foreach($directories as $directory) {
        $filepath = realpath(dirname(__FILE__).DS.'..'.DS.$directory.DS.$className.'.php');
        if(file_exists($filepath)) {
            require_once($filepath);
            return;
        }
    }
}