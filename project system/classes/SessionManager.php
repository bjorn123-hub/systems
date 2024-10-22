<?php
class SessionManager {
    public function startSession() {
        session_start(); 
    }

    public function endSession() {
        session_unset(); 
        session_destroy(); 
    }

    public function redirectTo($location) {
        header("Location: $location"); 
        exit(); 
    }

    public function isLoggedIn() {
        return isset($_SESSION['admin_id']); 
    }
}
?>
