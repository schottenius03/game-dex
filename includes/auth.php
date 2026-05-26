<?php
// includes/auth.php
// Global session management
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}