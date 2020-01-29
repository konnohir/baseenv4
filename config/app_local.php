<?php
use Cake\Mailer\Transport\MailTransport;
use Cake\Mailer\Transport\DebugTransport;

/*
 * 環境設定
 */
return [
    /*
     * Debug Level:
     *
     * Production Mode:
     * false: No error messages, errors, or warnings shown.
     *
     * Development Mode:
     * true: Errors and warnings shown.
     */
    'debug' => !false,

    /**
     * DebugKit configuration.
     * 
     * DebugKit is Enabled when 'debug' and 'DebugKit' is true.
     */
    'DebugKit' => false,

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     *
     * See app.php for more configuration options.
     */
    'Datasources' => [
        'default' => [
            'host' => '127.0.0.1',
            'username' => 'my_app',
            'password' => 'secret',
            'database' => 'baseenv4',
            'log' => false,
        ],

        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'host' => '127.0.0.1',
            'username' => 'my_app',
            'password' => 'secret',
            'database' => 'test_myapp',
        ],
    ],

    /*
     * Email configuration.
     *
     * Host and credential configuration in case you are using SmtpTransport
     *
     * See app.php for more configuration options.
     */
    'EmailTransport' => [
        'default' => [
            'className' => DebugTransport::class,
            'host' => '127.0.0.1',
            'port' => 25,
            'username' => null,
            'password' => null,
            'client' => null,
        ],
    ],

    /*
     * Security and encryption configuration
     *
     * - salt - A random string used in security hashing methods.
     *   The salt value is also used as the encryption key.
     *   You should treat it as extremely sensitive data.
     */
    'Security' => [
        'salt' => 'da9cf479d24fe593d1a5c8800558162cb76a13ddced661ee1c5b561f85552d57',
    ],
];
