<?php
// config/config.php - Main configuration file

class Config {
    // Database Configuration
    const DB_HOST = 'localhost';
    const DB_NAME = 'blogspace';
    const DB_USER = 'root';
    const DB_PASS = '';
    const DB_CHARSET = 'utf8mb4';
    
    // Application Configuration
    const APP_NAME = 'BlogPhp';
    const APP_ENV = 'development'; 
    const APP_DEBUG = true;
    const APP_URL = 'http://localhost';

    // Timezone
    const TIMEZONE = 'UTC';
    
    /**
     * Get database configuration as array
     */
    public static function getDbConfig() {
        return [
            'host' => self::DB_HOST,
            'dbname' => self::DB_NAME,
            'username' => self::DB_USER,
            'password' => self::DB_PASS,
            'charset' => self::DB_CHARSET
        ];
    }
    
    /**
     * Check if application is in debug mode
     */
    public static function isDebug() {
        return self::APP_DEBUG && self::APP_ENV === 'development';
    }
}