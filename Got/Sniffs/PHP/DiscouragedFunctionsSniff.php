<?php
/**
 * Got_Sniffs_PHP_DiscouragedFunctionsSniff
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */

if (class_exists('Generic_Sniffs_PHP_ForbiddenFunctionsSniff', true) === false) {
    throw new PHP_CodeSniffer_Exception('Class Generic_Sniffs_PHP_ForbiddenFunctionsSniff not found');
}

/**
 * Got_Sniffs_PHP_DiscouragedFunctionsSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */

class Got_Sniffs_PHP_DiscouragedFunctionsSniff extends Generic_Sniffs_PHP_ForbiddenFunctionsSniff
{

    /**
     * A list of forbidden functions with their alternatives.
     *
     * The value is null if no alternative exists. IE, the
     * function should just not be used.
     *
     * @var array(string => string|null)
     */
    protected $forbiddenFunctions = array(
         'error_log'                => null,
         'split'                    => null,
         'spliti'                   => null,
         'define_syslog_variables'  => null,
         'dl'                       => null,
         'sql_regcase'              => null,
         'ereg'                     => null,
         'ereg_replace'             => 'preg_replace',
         'eregi_replace'            => 'preg_replace',
         'call_user_method'         => 'call_user_func',
         'call_user_method_array'   => 'call_user_func_array',
         'set_magic_quotes_runtime' => 'magic_quotes_runtime',
         'session_unregister'       => '$_SESSION',
         'session_unregister'       => '$_SESSION',
         'session_is_registered'    => '$_SESSION',
         'session_register'         => '$_SESSION',
         'session_unregister'       => '$_SESSION',
         'session_is_registered'    => '$_SESSION',
         'set_socket_blocking'      => 'stream_set_blocking',

    );

    /**
     * If true, an error will be thrown; otherwise a warning.
     *
     * @var bool
     */
    public $error = true;

}
