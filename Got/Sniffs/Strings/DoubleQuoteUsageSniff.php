<?php
/**
 * Got_Sniffs_Strings_DoubleQuoteUsageSniff
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
 * Got_Sniffs_Strings_DoubleQuoteUsageSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */
class Got_Sniffs_Strings_DoubleQuoteUsageSniff implements PHP_CodeSniffer_Sniff
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
     * @param PHP_CodeSniffer_File $php_cs_file The file being scanned.
     * @param int                  $stack_ptra  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $php_cs_file, $stack_ptra)
    {
        $tokens = $php_cs_file->getTokens();

        // The use of variables in double quoted strings is not allowed.
        if($tokens[$stack_ptra]['code'] === T_DOUBLE_QUOTED_STRING)
        {
            $string_tokens = token_get_all('<?php ' . $tokens[$stack_ptra]['content']);
            foreach($string_tokens as $token)
            {
                if(is_array($token) === TRUE && $token[0] === T_VARIABLE)
                {
                    return;
                }
            }

            return;
        }

        $working_string = $tokens[$stack_ptra]['content'];

        // Check if it's a double quoted string.
        if(strpos($working_string, '"') === FALSE)
        {
            return;
        }

        // Make sure it's not a part of a string started above.
        // If it is, then we have already checked it.
        if($working_string[0] !== '"')
        {
            return;
        }

        // Work through the following tokens, in case this string is stretched
        // over multiple Lines.
        for($i = ($stack_ptra + 1); $i < $php_cs_file->numTokens; $i++)
        {
            if($tokens[$i]['type'] !== 'T_CONSTANT_ENCAPSED_STRING')
            {
                break;
            }

            $working_string .= $tokens[$i]['content'];
        }

        $allowed_chars = array(
            '\n',
            '\r',
            '\f',
            '\t',
            '\v',
            '\x',
            '\'',
        );

        foreach($allowed_chars as $test_char)
        {
            if(strpos($working_string, $test_char) !== FALSE)
            {
                return;
            }
        }

        $error = 'String ' . $working_string . ' does not require double quotes; use single quotes instead';
        $php_cs_file->addError($error, $stack_ptra);

    }
}

