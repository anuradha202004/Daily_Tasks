<?php

class View_Product {
    protected $template;
    protected $data = [];

    public function __construct($template) {
        $this->template = $template;
    }

    public function assign($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }

    public function toHtml() {
        // Extract data to make it available in the view
        $data = $this->data; // Expose $data variable for legacy compatibility
        extract($this->data);
        
        ob_start();
        $viewFile = APP_PATH . '/Views/' . $this->template . '.php';
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            echo "View template not found: " . $this->template;
        }
        return ob_get_clean();
    }
}
