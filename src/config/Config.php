<?php

/**
 * Configuration class for environment management
 * Ejemplo de mejora: Gestión centralizada de configuración
 */
class Config
{
    private static $config = [];

    /**
     * Load configuration from environment or defaults
     */
    public static function load()
    {
        self::$config = [
            'database' => [
                'host' => $_ENV['DB_HOST'] ?? 'localhost',
                'name' => $_ENV['DB_NAME'] ?? 'optica_san_gabriel_db',
                'user' => $_ENV['DB_USER'] ?? 'admin_osg',
                'password' => $_ENV['DB_PASSWORD'] ?? 'admin_osg',
                'charset' => $_ENV['DB_CHARSET'] ?? 'utf8mb4'
            ],
            'app' => [
                'name' => $_ENV['APP_NAME'] ?? 'Optic-Suite',
                'env' => $_ENV['APP_ENV'] ?? 'development',
                'debug' => $_ENV['APP_DEBUG'] ?? true,
                'url' => $_ENV['APP_URL'] ?? 'http://localhost'
            ],
            'security' => [
                'csrf_token_name' => '_token',
                'session_timeout' => 3600, // 1 hora
                'max_login_attempts' => 5
            ]
        ];
    }

    /**
     * Get configuration value using dot notation
     * 
     * @param string $key Configuration key (e.g., 'database.host')
     * @param mixed $default Default value if key doesn't exist
     * @return mixed
     */
    public static function get(string $key, $default = null)
    {
        if (empty(self::$config)) {
            self::load();
        }

        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Check if we're in production environment
     */
    public static function isProduction(): bool
    {
        return self::get('app.env') === 'production';
    }

    /**
     * Check if debug mode is enabled
     */
    public static function isDebug(): bool
    {
        return self::get('app.debug') === true;
    }
}