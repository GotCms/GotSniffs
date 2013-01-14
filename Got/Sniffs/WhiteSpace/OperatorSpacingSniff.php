<?php
/**
 * GoT_Sniffs_WhiteSpace_OperatorSpacingSniff
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
 * GoT_Sniffs_WhiteSpace_OperatorSpacingSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */

class GoT_Sniffs_WhiteSpace_OperatorSpacingSniff implements PHP_CodeSniffer_Sniff
{
    protected $_errorMessageTpl = 'Expected 1 space after "%s" : %s found';
    /**
     * A list of tokenizers this sniff supports.
     *
     * @var array
     */
    public $supportedTokenizers = array(
        'PHP',
        'JS',
    );


    /**
     * Returns an array of tokens this test wants to listen for.
     *
     * @return array
     */
    public function register()
    {
        $comparison = PHP_CodeSniffer_Tokens::$comparisonTokens;
        $operators  = PHP_CodeSniffer_Tokens::$operators;
        $assignment = PHP_CodeSniffer_Tokens::$assignmentTokens;

        return array_unique(array_merge($comparison, $operators, $assignment));

    }

    /**
     * Processes this sniff, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $php_cs_file The current file being checked.
     * @param int                  $stack_ptr  The position of the current token in the
     *                                        stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $php_cs_file, $stack_ptr)
    {
        $tokens = $php_cs_file->getTokens();

        if($tokens[$stack_ptr]['code'] === T_EQUAL)
        {
            // Skip for '=&' case.
            if(isset($tokens[($stack_ptr + 1)]) === true && $tokens[($stack_ptr + 1)]['code'] === T_BITWISE_AND)
            {
                return;
            }

            // Skip default values in function declarations.
            if(isset($tokens[$stack_ptr]['nested_parenthesis']) === true)
            {
                $bracket = end($tokens[$stack_ptr]['nested_parenthesis']);
                if(isset($tokens[$bracket]['parenthesis_owner']) === true)
                {
                    $function = $tokens[$bracket]['parenthesis_owner'];
                    if($tokens[$function]['code'] === T_FUNCTION)
                    {
                        return;
                    }
                }
            }
        }

        if($tokens[$stack_ptr]['code'] !== T_BITWISE_AND)
        {
            if($tokens[$stack_ptr]['code'] === T_MINUS)
            {
                // Check minus spacing, but make sure we aren't just assigning
                // a minus value or returning one.
                $prev = $php_cs_file->findPrevious(T_WHITESPACE, ($stack_ptr - 1), null, true);
                if($tokens[$prev]['code'] === T_RETURN)
                {
                    // Just returning a negative value; eg. return -1.
                    return;
                }

                if(in_array($tokens[$prev]['code'], PHP_CodeSniffer_Tokens::$operators) === true)
                {
                    // Just trying to operate on a negative value; eg. ($var * -1).
                    return;
                }

                if(in_array($tokens[$prev]['code'], PHP_CodeSniffer_Tokens::$comparisonTokens) === true)
                {
                    // Just trying to compare a negative value; eg. ($var === -1).
                    return;
                }

                // A list of tokens that indicate that the token is not
                // part of an arithmetic operation.
                $invalid_tokens = array(
                    T_COMMA,
                    T_OPEN_PARENTHESIS,
                    T_OPEN_SQUARE_BRACKET,
                );

                if(in_array($tokens[$prev]['code'], $invalid_tokens) === true)
                {
                    // Just trying to use a negative value; eg. myFunction($var, -2).
                    return;
                }

                $number = $php_cs_file->findNext(T_WHITESPACE, ($stack_ptr + 1), null, true);
                if($tokens[$number]['code'] === T_LNUMBER)
                {
                    $semi = $php_cs_file->findNext(T_WHITESPACE, ($number + 1), null, true);
                    if($tokens[$semi]['code'] === T_SEMICOLON)
                    {
                        if($prev !== false && (in_array($tokens[$prev]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === true))
                        {
                            // This is a negative assignment.
                            return;
                        }
                    }
                }
            }

            $operator = $tokens[$stack_ptr]['content'];

            if($tokens[($stack_ptr - 1)]['code'] !== T_WHITESPACE)
            {
                $php_cs_file->addError(sprintf($this->_errorMessageTpl, $operator, 0), $stack_ptr);
            }
            else if(strlen($tokens[($stack_ptr - 1)]['content']) !== 1)
            {
                // Don't throw an error for assignments, because other standards allow
                // multiple spaces there to align multiple assignments.
                if(in_array($tokens[$stack_ptr]['code'], PHP_CodeSniffer_Tokens::$assignmentTokens) === false)
                {
                    $found = strlen($tokens[($stack_ptr - 1)]['content']);
                    $php_cs_file->addError(sprintf($this->_errorMessageTpl, $operator, 0), $stack_ptr);
                }
            }

            if($tokens[($stack_ptr + 1)]['code'] !== T_WHITESPACE)
            {
                $php_cs_file->addError(sprintf($this->_errorMessageTpl, $operator, 0), $stack_ptr);
            }
            else if(strlen($tokens[($stack_ptr + 1)]['content']) !== 1)
            {
                $found = strlen($tokens[($stack_ptr + 1)]['content']);
                $php_cs_file->addError(sprintf($this->_errorMessageTpl, $operator, $found), $stack_ptr);
            }
        }
    }
}
