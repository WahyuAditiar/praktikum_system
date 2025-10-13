<?php
class StaffLabController {
    
    public function __construct() {
        // Constructor jika diperlukan
    }
    
    public function index() {
        header('Location: ' . BASEURL . '/staff_lab/dashboard');
        exit;
    }
    
    public function dashboard() {
        // Check authorization
        if ($_SESSION['user_role'] != 'staff_lab') {
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
        
        $data['title'] = 'Dashboard Staff Lab';
        $this->view('staff_lab/dashboard', $data);
    }
    
    // Redirect ke AsistenController
    public function asisten() {
        header('Location: ' . BASEURL . '/asisten');
        exit;
    }
    
    // Method untuk menu lainnya (praktikum, jadwal, dll)
    public function praktikum() {
        // Implementasi untuk menu praktikum
        $data['title'] = 'Data Praktikum';
        $this->view('staff_lab/praktikum', $data);
    }
    
    public function jadwal() {
        // Implementasi untuk menu jadwal
        $data['title'] = 'Jadwal Praktikum';
        $this->view('staff_lab/jadwal', $data);
    }
    
    // View method
    private function view($view, $data = []) {
        require_once '../app/views/templates/header.php';
        require_once '../app/views/templates/sidebar_staff_lab.php';
        require_once '../app/views/' . $view . '.php';
        require_once '../app/views/templates/footer.php';
    }
}
?>