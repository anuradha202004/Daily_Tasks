<?php

class PageController extends Controller {

    public function about() {
        // Since about is currently a section on index, we might want to just redirect or show a dedicated view if we extract it.
        // For now, let's create a dedicated view.
        $this->view('page/about', ['title' => 'About Us']);
    }

    public function contact() {
        $this->view('page/contact', ['title' => 'Contact Us']);
    }

    public function processContact() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $message = $_POST['message'] ?? '';

            // Simple validation
            if (empty($name) || empty($email) || empty($message)) {
                $this->sendJson(['success' => false, 'message' => 'Please fill in all fields.']);
            }

            // In a real app, you'd send an email here.
            // For now, just return success.
            $this->sendJson(['success' => true, 'message' => 'Message sent successfully!']);
        }
    }

    private function sendJson($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
