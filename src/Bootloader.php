<?php
namespace TinyPixel\Config;

use Dotenv\Dotenv;
use Roots\WPConfig\Config;

/**
 * WordPress Application Bootloader
 *
 * @package TinyPixel\Config
 */
class Bootloader
{
    /**
     * Bedrock Config tool
     * @var  Roots\WPConfig\Config
     */
    public static $roots;

    /**
     * Instance
     * @var TinyPixel\Config\Bootloader
     */
    protected static $bootloader;

    /**
     * Bedrock root dir
     * @var string
     */
    public $bedrockDir;

    /**
     * Instantiate class.
     *
     * @return \TinyPixel\Config\Bootloader
     */
    public static function getInstance()
    {
        if (self::$bootloader) {
            return self::$bootloader;
        }

        return self::$bootloader = new Bootloader();
    }

    /**
     * Initialize class.
     *
     * @param  string Bedrock root directory
     * @return void
     */
    public function init(string $bedrockDir) : void
    {
        self::$roots = Config::class;

        $this->bedrockDir = $bedrockDir;
    }

    /**
     * Load environmental parameters.
     *
     * @paramrray required environmental parameters
     *
     * @return void
     */
    public function loadEnv(array $required = ['WP_HOME', 'WP_ENV', 'WP_SITEURL']) : void
    {
        $this->env = Dotenv::create($this->bedrockDir);

        $this->env->load();
        $this->env->required($required);
    }

    /**
     * Define filesystem
     *
     * @param  array filesystem
     * @return void
     */
    public function defineFS(array $fs) : void
    {
        $this->defineSet($fs);

        $this->defineSet([
            'WP_CONTENT_DIR' => "{$this->bedrockDir}/web/{$fs['CONTENT_DIR']}",
            'WP_CONTENT_URL' => "{$fs['WP_HOME']}/{$fs['CONTENT_DIR']}",
        ]);
    }

    /**
     * Define database
     *
     * @param  array $db
     * @return void
     */
    public function defineDB(array $db) : void
    {
        $this->defineSet($db);
    }

    /**
     * Define S3
     *
     * @param  array s3 configuration
     * @return void
     */
    public function defineS3(array $s3) : void
    {
        $this->defineSet($s3);
    }

    /**
     * Define stages
     *
     * @param  array
     * @return void
     */
    public function defineEnvironments(array $envs) : void
    {
        self::define('ENVIRONMENTS', $envs);
    }

    /**
     * Configure WordPress application
     *
     * @param  array wordpress configuration
     * @return void
     */
    public function configureWordPressApp(array $config) : void
    {
        self::defineSet($config);
    }

    /**
     * Define Redis
     *
     * @param  array Redis connection
     * @return void
     */
    public function defineRedis(array $redis) : void
    {
        global $redis_server;

        $redis_server = [
            'host' => $redis['REDIS_HOST'],
            'port' => $redis['REDIS_PORT'],
            'auth' => $redis['REDIS_AUTH'],
            'ssl' => [
                'local_cert'   => $redis['PREDIS_CERT'],
                'verify_peers' => $redis['PREDIS_VERIFY_PEERS'],
            ],
        ];
    }

    /**
     * Configure Redis
     *
     * @param  array Redis configuration
     * @return void
     */
    public function configureRedis(array $redis) : void
    {
        $this->defineSet($redis);
    }

    /**
     * Define salts
     *
     * @param  array salts
     * @return void
     */
    public function defineSalts(array $salt) : void
    {
        $this->defineSet($salt);
    }

    /**
     * Override environment
     *
     * @param  string environment
     * @return void
     */
    public function overrides(string $env)
    {
        $file = "{$this->bedrockDir}/environments/{$env}.php";

        if(file_exists($file)) {
            require $file;
        }
    }

    /**
     * Boot WordPress
     *
     * @return void
     */
    public function boot() : void
    {
        self::overrides(self::get('WP_ENV'));

        self::$roots::apply();

        if (!defined('ABSPATH')) {
            define('ABSPATH', "{$this->bedrockDir}/web/wp");
        }
    }

    /**
     * Get from Roots config
     *
     * @return mixed
     */
    public static function get($get)
    {
        return self::$roots::get($get);
    }

    /**
     * Define for Roots Config
     *
     * @param  string  constant
     * @param  mixed   value
     * @return void
     */
    public static function define(string $const, $value) : void
    {
        self::$roots::define($const, $value);
    }

    /**
     * Define set of config items
     *
     * @param  array definitions
     * @return void
     */
    public function defineSet($definitions) : void
    {
        foreach ($definitions as $const => $def) {
            self::define($const, $def);
        }
    }

    /**
     * Allow WordPress to detect HTTPS when used behind a reverse proxy or a load balancer
     * @see https://codex.wordpress.org/Function_Reference/is_ssl#Notes
     *
     * @return void
     */
    public function exposeSSL() : void
    {
        if (self::isSSL()) {
            $_SERVER['HTTPS'] = 'on';
        }
    }

    /**
     * Is SSL?
     *
     * @return bool true if SSL is enabled
     */
    public static function isSSL()
    {
        return isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https';
    }
}
