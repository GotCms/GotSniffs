<?php
/**
 * Got_Sniffs_Strings_ConcatenationSpacingSniff
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */

/**
 * Got_Sniffs_Strings_ConcatenationSpacingSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */
class Got_Sniffs_Strings_ConcatenationSpacingSniff implements PHP_CodeSniffer_Sniff
{
    /**
    * Returns an array of tokens this test wants to listen for.
    *
    * @return array
    */
    public function register()
    {
        return array(T_STRING_CONCAT);
    }

    /**
    * Processes this test, when one of its tokens is encountered.
    *
    * @param PHP_CodeSniffer_File $php_cs_file The file being scanned.
    * @param int $stack_ptr The position of the current token in the
    * stack passed in $tokens.
    *
    * @return void
    */
    public function process(PHP_CodeSniffer_File $php_cs_file, $stack_ptr)
    {
        $tokens = $php_cs_file->getTokens();
        if($tokens[($stack_ptr - 1)]['code'] !== T_WHITESPACE or $tokens[($stack_ptr + 1)]['code'] !== T_WHITESPACE)
        {
            $php_cs_file->addError('Concat operator must be surrounded by spaces', $stack_ptr, 'Missing');
        }
    }
}
