<?php

class Controller {
    public function view($view, $data = []) {
        if (file_exists(__DIR__ . '/../Views/' . $view . '.php')) {
            extract($data);
            require_once __DIR__ . '/../Views/' . $view . '.php';
        } else {
            die("View does not exist: " . $view);
        }
    }

    public function model($model) {
        require_once __DIR__ . '/../Models/' . $model . '.php';
        return new $model();
    }
    
    public function redirect($url) {
        header("Location: " . $url);
        exit();
    }
}
