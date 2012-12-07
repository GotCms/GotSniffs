<?php
/**
 * Got_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * PHP version 5
 *
 * @category  PHP
 * @package   CodeSniffer
 * @author    Pierre Rambaud <pierre.rambaud86@gmail.com>
 * @author    Marc McIntyre <mmcintyre@squiz.net>
 * @license   http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link      http://rambaudpierre.fr
 */

class GoT_Sniffs_WhiteSpace_ScopeClosingBraceSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * The number of spaces code should be indented.
     *
     * @var int
     */
    protected $_indent = 0;


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        return PHP_CodeSniffer_Tokens::$scopeOpeners;

    }//end register()


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $php_cs_file All the tokens found in the document.
     * @param int                  $stack_ptr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $php_cs_file, $stack_ptr)
    {
        $tokens = $php_cs_file->getTokens();

        // If this is an inline condition (ie. there is no scope opener), then
        // return, as this is not a new scope.
        if(isset($tokens[$stack_ptr]['scope_closer']) === FALSE)
        {
            return;
        }

        $scopeStart  = $tokens[$stack_ptr]['scope_opener'];
        $scope_end    = $tokens[$stack_ptr]['scope_closer'];

        // If the scope closer doesn't think it belongs to this scope opener
        // then the opener is sharing its closer ith other tokens. We only
        // want to process the closer once, so skip this one.
        if($tokens[$scope_end]['scope_condition'] !== $stack_ptr)
        {
            return;
        }

        // We need to actually find the first piece of content on this line,
        // because if this is a method with tokens before it (public, static etc)
        // or an if with an else before it, then we need to start the scope
        // checking from there, rather than the current token.
        $line_start = ($stack_ptr - 1);
        for($line_start; $line_start > 0; $line_start--)
        {
            if(strpos($tokens[$line_start]['content'], $php_cs_file->eolChar) !== FALSE)
            {
                break;
            }
        }

        // We found a new line, now go forward and find the first non-whitespace
        // token.
        $line_start= $php_cs_file->findNext(
            array(T_WHITESPACE),
            ($line_start + 1),
            null,
            TRUE
        );

        $start_column = $tokens[$line_start]['column'];

        // Check that the closing brace is on it's own line.
        $lastContent = $php_cs_file->findPrevious(
            array(T_WHITESPACE),
            ($scope_end - 1),
            $scopeStart,
            TRUE
        );

        if($tokens[$lastContent]['line'] === $tokens[$scope_end]['line'])
        {
            $error = 'Closing brace must be on a line by itself';
            $php_cs_file->addError($error, $scope_end, 'Line');
            return;
        }

        // Check now that the closing brace is lined up correctly.
        $brace_indent   = $tokens[$scope_end]['column'];
        if(in_array($tokens[$stack_ptr]['code'], array(T_CASE, T_DEFAULT)) === TRUE)
        {
            // BREAK statements should be indented same spaces from the
            // CASE or DEFAULT statement.
            // RETURN statements should be indented n spaces from the
            // CASE or DEFAULT statement.
            if($tokens[$scope_end]['code'] == T_RETURN)
            {
                $this->_indent = 4;
            }
            else
            {
                $this->_indent = 0;
            }

            if($brace_indent !== ($start_column + $this->_indent))
            {
                $error = 'Case breaking statement indented incorrectly; expected %s spaces, found %s';
                $data  = array(
                    ($start_column + $this->_indent - 1),
                    ($brace_indent - 1),
                );
                $php_cs_file->addError($error, $scope_end, 'BreakIdent', $data);
            }
        }
        else
        {
            if($brace_indent !== $start_column)
            {
                $error = 'Closing brace indented incorrectly; expected %s spaces, found %s';
                $data  = array(
                    ($start_column - 1),
                    ($brace_indent - 1),
                );

                $php_cs_file->addError($error, $scope_end, 'Indent', $data);
            }
        }
    }
}
