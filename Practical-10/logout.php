<?php
/**
 * StudentHub — Secure Logout (Practical 10)
 * 
 * Cleans session, unsets session cookies, deletes remember-me tokens from DB,
 * and redirects to login screen.
 * 
 * @author Megh Rana (25CE099)
 * @subject ITUE203 Web Development Frameworks
 */

require_once __DIR__ . '/includes/auth.php';

logoutUser();

header('Location: login.php?msg=logged_out');
exit;
