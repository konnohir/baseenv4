<?php
/*
 * 環境毎に変化する設定値
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
    'debug' => true,

    /**
     * DebugKit configuration.
     * 
     * DebugKit is enabled when both 'debug' and 'DebugKit.use' are true.
     */
    'DebugKit' => [
        'use' => false,
    ],

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
            'log' => false,
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
            'host' => '127.0.0.1',
            'port' => 25,
            'username' => null,
            'password' => null,
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
