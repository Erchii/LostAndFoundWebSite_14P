<?php
// Load environment variables from .env file
$env = parse_ini_file(dirname(__DIR__) . '/.env');

// Application settings
define('APP_NAME', $env['APP_NAME'] ?? 'Lost and Found Portal');
define('APP_URL', $env['APP_URL'] ?? 'http://localhost:8080');
define('APP_DEBUG', $env['APP_DEBUG'] ?? false);
define('APP_SECRET', $env['APP_SECRET'] ?? 'default_secret_key');

// Database settings
define('DB_HOST', $env['DB_HOST'] ?? 'localhost');
define('DB_PORT', $env['DB_PORT'] ?? '3306');
define('DB_DATABASE', $env['DB_DATABASE'] ?? 'lost_found_db');
define('DB_USERNAME', $env['DB_USERNAME'] ?? 'root');
define('DB_PASSWORD', $env['DB_PASSWORD'] ?? '');

// Mail settings
define('MAIL_HOST', $env['MAIL_HOST'] ?? 'smtp.example.com');
define('MAIL_PORT', $env['MAIL_PORT'] ?? '587');
define('MAIL_USERNAME', $env['MAIL_USERNAME'] ?? 'noreply@example.com');
define('MAIL_PASSWORD', $env['MAIL_PASSWORD'] ?? '');
define('MAIL_ENCRYPTION', $env['MAIL_ENCRYPTION'] ?? 'tls');
define('MAIL_FROM_ADDRESS', $env['MAIL_FROM_ADDRESS'] ?? 'noreply@example.com');
define('MAIL_FROM_NAME', $env['MAIL_FROM_NAME'] ?? APP_NAME);

// Upload settings
define('UPLOAD_PATH', dirname(__DIR__) . '/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif']);

// Pagination
define('ITEMS_PER_PAGE', 10);

// Session settings
define('SESSION_LIFETIME', 3600); // 1 hour

// Set timezone
date_default_timezone_set('UTC');