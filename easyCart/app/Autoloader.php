<?php

class Autoloader {
    public static function register() {
        spl_autoload_register(function ($className) {
            // Convert Class_Name_Model to path: Class/Name/Model.php
            $parts = explode('_', $className);
            $path = implode('/', $parts) . '.php';
            
            // Check in Code/Local first (Custom)
            $file = __DIR__ . '/Code/Local/' . $path;
            if (file_exists($file)) {
                require_once $file;
                return;
            }
            
            // Check in Code/Core (Framework)
            $file = __DIR__ . '/Code/Core/' . $path;
            if (file_exists($file)) {
                require_once $file;
                return;
            }
            
            // Fallback for legacy (optional/deprecated)
            // ...
        });
    }
}

Autoloader::register();
