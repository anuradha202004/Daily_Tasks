<?php

class Controller_Page extends Core_Controller {
    
    public function about() {
        $view = new View_Product('page/about');
        $view->assign('title', 'About Us');
        echo $view->toHtml();
    }

    public function contact() {
        $view = new View_Product('page/contact');
        $view->assign('title', 'Contact Us');
        echo $view->toHtml();
    }
    
    public function processContact() {
        // Handle contact form submission
        // ...
        $this->redirect('contact?success=1');
    }
}
