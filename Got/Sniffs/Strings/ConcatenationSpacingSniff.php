<?php
/**
 * Got_Sniffs_Strings_ConcatenationSpacingSniff
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
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
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
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
    * @param PHP_CodeSniffer_File $phpCsFile The file being scanned.
    * @param int                  $stackPtr  The position of the current token in the
    *                                        stack passed in $tokens.
    *
    * @return void
    */
    public function process(PHP_CodeSniffer_File $phpCsFile, $stackPtr)
    {
        $tokens = $phpCsFile->getTokens();
        if ($tokens[($stackPtr - 1)]['code'] !== T_WHITESPACE or $tokens[($stackPtr + 1)]['code'] !== T_WHITESPACE) {
            $phpCsFile->addError('Concat operator must be surrounded by spaces', $stackPtr, 'Missing');
        }
    }
}
