<?php
/**
 * Got_Sniffs_Strings_DoubleQuoteUsageSniff
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */

namespace Got\Sniffs\Strings;

use PHP_CodeSniffer_Sniff;
use PHP_CodeSniffer_File;

/**
 * Got_Sniffs_Strings_DoubleQuoteUsageSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud (GoT) <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */
class DoubleQuoteUsageSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return array(
            T_CONSTANT_ENCAPSED_STRING,
            T_DOUBLE_QUOTED_STRING,
        );

    }

    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpCsFile The file being scanned.
     * @param int                  $stackPtra The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpCsFile, $stackPtra)
    {
        $tokens = $phpCsFile->getTokens();

        // The use of variables in double quoted strings is not allowed.
        if ($tokens[$stackPtra]['code'] === T_DOUBLE_QUOTED_STRING) {
            $stringTokens = token_get_all('<?php ' . $tokens[$stackPtra]['content']);
            foreach ($stringTokens as $token) {
                if (is_array($token) === true && $token[0] === T_VARIABLE) {
                    return;
                }
            }

            return;
        }

        $workingString = $tokens[$stackPtra]['content'];

        // Check if it's a double quoted string.
        if (strpos($workingString, '"') === false) {
            return;
        }

        // Make sure it's not a part of a string started above.
        // If it is, then we have already checked it.
        if ($workingString[0] !== '"') {
            return;
        }

        // Work through the following tokens, in case this string is stretched
        // over multiple Lines.
        for ($i = ($stackPtra + 1); $i < $phpCsFile->numTokens; $i++) {
            if ($tokens[$i]['type'] !== 'T_CONSTANT_ENCAPSED_STRING') {
                break;
            }

            $workingString .= $tokens[$i]['content'];
        }

        $allowedChars = array(
            '\n',
            '\r',
            '\f',
            '\t',
            '\v',
            '\x',
            '\'',
        );

        foreach ($allowedChars as $testChar) {
            if (strpos($workingString, $testChar) !== false) {
                return;
            }
        }

        $error = 'String ' . $workingString . ' does not require double quotes; use single quotes instead';
        $phpCsFile->addError($error, $stackPtra);
    }
}
