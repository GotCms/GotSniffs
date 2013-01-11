<?php
/**
 * Got_Sniffs_NamingConventions_ValidVariableNameSniff
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */

if(class_exists('PHP_CodeSniffer_Standards_AbstractVariableSniff', TRUE) === FALSE)
{
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractVariableSniff not found');
}

/**
 * Got_Sniffs_NamingConventions_ValidVariableNameSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */

class Got_Sniffs_NamingConventions_ValidVariableNameSniff extends PHP_CodeSniffer_Standards_AbstractVariableSniff
{

    /**
     * Tokens to ignore so that we can find a DOUBLE_COLON.
     *
     * @var array
     */
    protected $_ignore = array(
        T_WHITESPACE,
        T_COMMENT,
    );


    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcs_file The file being scanned.
     * @param int                  $stack_ptr  The position of the current token in the
     *                                        stack passed in $tokens.
     * @return void
     */
    protected function processVariable(PHP_CodeSniffer_File $phpcs_file, $stack_ptr)
    {
        $tokens  = $phpcs_file->getTokens();
        $var_name = ltrim($tokens[$stack_ptr]['content'], '$');

        $php_reserved_vars = array(
            '_SERVER',
            '_GET',
            '_OLD_GET',
            '_POST',
            '_OLD_POST',
            '_REQUEST',
            '_SESSION',
            '_ENV',
            '_COOKIE',
            '_FILES',
            '_OLD_FILES',
            'GLOBALS',
        );

        // If it's a php reserved var, then its ok.
        if(in_array($var_name, $php_reserved_vars) === TRUE)
        {
            return;
        }

        $obj_operator = $phpcs_file->findNext(array(T_WHITESPACE), ($stack_ptr + 1), NULL, TRUE);
        if($tokens[$obj_operator]['code'] === T_OBJECT_OPERATOR)
        {
            // Check to see if we are using a variable from an object.
            $var = $phpcs_file->findNext(array(T_WHITESPACE), ($obj_operator + 1), NULL, TRUE);
            if($tokens[$var]['code'] === T_STRING)
            {
                // Either a var name or a function call, so check for bracket.
                $bracket = $phpcs_file->findNext(array(T_WHITESPACE), ($var + 1), NULL, TRUE);

                if($tokens[$bracket]['code'] !== T_OPEN_PARENTHESIS)
                {
                    $obj_var_name = $tokens[$var]['content'];

                    // There is no way for us to know if the var is public or private,
                    // so we have to ignore a leading underscore if there is one and just
                    // check the main part of the variable name.
                    $original_var_name = $obj_var_name;
                    if(substr($obj_var_name, 0, 1) === '_')
                    {
                        $obj_var_name = substr($obj_var_name, 1);
                    }

                    if(PHP_CodeSniffer::isCamelCaps($obj_var_name, FALSE, TRUE, FALSE) === FALSE)
                    {
                        $error = 'Variable "%s" is not in valid camel caps format';
                        $data  = array($original_var_name);
                        $phpcs_file->addError($error, $var, 'NotCamelCaps', $data);
                    }
                    elseif(preg_match('|\d|', $obj_var_name))
                    {
                        $warning = 'Variable "%s" contains numbers but this is discouraged';
                        $data    = array($original_var_name);
                        $phpcs_file->addWarning($warning, $stack_ptr, 'ContainsNumbers', $data);
                    }
                }
            }
        }

        // There is no way for us to know if the var is public or private,
        // so we have to ignore a leading underscore if there is one and just
        // check the main part of the variable name.
        $original_var_name = $var_name;
        //Protected, private or static variables
        if(substr($var_name, 0, 1) === '_')
        {
            $obj_operator = $phpcs_file->findPrevious(array(T_WHITESPACE), ($stack_ptr - 1), NULL, TRUE);
            if($tokens[$obj_operator]['code'] === T_DOUBLE_COLON)
            {
                // The variable lives within a class, and is referenced like
                // this: MyClass::$_variable, so we don't know its scope.
                $in_class = TRUE;
            }
            else
            {
                $in_class = $phpcs_file->hasCondition($stack_ptr, array(T_CLASS, T_INTERFACE));
            }

            if($in_class === TRUE)
            {
                $var_name = substr($var_name, 1);
            }

            if(PHP_CodeSniffer::isCamelCaps($var_name, FALSE, TRUE, FALSE) === FALSE)
            {
                $error = 'Variable "%s" is not in valid camel caps format';
                $data  = array($var_name);
                $phpcs_file->addError($error, $stack_ptr, 'NotCamelCaps', $data);
            }
            elseif(preg_match('|\d|', $var_name))
            {
                $warning = 'Variable "%s" contains numbers but this is discouraged';
                $data    = array($var_name);
                $phpcs_file->addWarning($warning, $stack_ptr, 'ContainsNumbers', $data);
            }
        }
        else //Others variables
        {
            if(!preg_match('~^[a-z0-9_]+$~', $var_name))
            {
                $error = 'Variable "%s" is not in valid underscore format';
                $data  = array($original_var_name);
                $phpcs_file->addError($error, $stack_ptr, 'NotCamelCaps', $data);
            }
            elseif(preg_match('|\d|', $var_name))
            {
                $warning = 'Variable "%s" contains numbers but this is discouraged';
                $data    = array($original_var_name);
                $phpcs_file->addWarning($warning, $stack_ptr, 'ContainsNumbers', $data);
            }
        }
    }


    /**
     * Processes class member variables.
     *
     * @param PHP_CodeSniffer_File $phpcs_file The file being scanned.
     * @param int                  $stack_ptr  The position of the current token in the
     *                                         stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(PHP_CodeSniffer_File $phpcs_file, $stack_ptr)
    {
        $tokens      = $phpcs_file->getTokens();
        $var_name     = ltrim($tokens[$stack_ptr]['content'], '$');
        $member_props = $phpcs_file->getMemberProperties($stack_ptr);
        $public      = ($member_props['scope'] === 'public');

        if($public === TRUE)
        {
            if(substr($var_name, 0, 1) === '_')
            {
                $error = 'Public member variable "%s" must not contain a leading underscore';
                $data  = array($var_name);
                $phpcs_file->addError($error, $stack_ptr, 'PublicHasUnderscore', $data);

                return;
            }
        }
        else
        {
            if(substr($var_name, 0, 1) !== '_')
            {
                $scope = ucfirst($member_props['scope']);
                $error = '%s member variable "%s" must contain a leading underscore';
                $data  = array(
                    $scope,
                    $var_name,
                );
                $phpcs_file->addError($error, $stack_ptr, 'PrivateNoUnderscore', $data);

                return;
            }
        }

        if(PHP_CodeSniffer::isCamelCaps($var_name, FALSE, $public, FALSE) === FALSE)
        {
            $error = 'Variable "%s" is not in valid camel caps format';
            $data  = array($var_name);
            $phpcs_file->addError($error, $stack_ptr, 'MemberVarNotCamelCaps', $data);
        }
        elseif(preg_match('|\d|', $var_name))
        {
            $warning = 'Variable "%s" contains numbers but this is discouraged';
            $data    = array($var_name);
            $phpcs_file->addWarning($warning, $stack_ptr, 'MemberVarContainsNumbers', $data);
        }
    }


    /**
     * Processes the variable found within a double quoted string.
     *
     * @param PHP_CodeSniffer_File $phpcs_file The file being scanned.
     * @param int                  $stack_ptr  The position of the double quoted
     *                                        string.
     * @return void
     */
    protected function processVariableInString(PHP_CodeSniffer_File $phpcs_file, $stack_ptr)
    {
        $tokens = $phpcs_file->getTokens();

        $php_reserved_vars = array(
            '_SERVER',
            '_GET',
            '_OLD_GET',
            '_POST',
            '_OLD_POST',
            '_REQUEST',
            '_SESSION',
            '_ENV',
            '_COOKIE',
            '_FILES',
            '_OLD_FILES',
            'GLOBALS',
        );

        if(preg_match_all('|[^\\\]\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)|', $tokens[$stack_ptr]['content'], $matches) !== 0)
        {
            foreach($matches[1] as $var_name)
            {
                // If it's a php reserved var, then its ok.
                if(in_array($var_name, $php_reserved_vars) === TRUE)
                {
                    continue;
                }

                // There is no way for us to know if the var is public or private,
                // so we have to ignore a leading underscore if there is one and just
                // check the main part of the variable name.
                $original_var_name = $var_name;
                if(substr($var_name, 0, 1) === '_')
                {
                    if($phpcs_file->hasCondition($stack_ptr, array(T_CLASS, T_INTERFACE)) === TRUE)
                    {
                        $var_name = substr($var_name, 1);
                    }
                }

                if(PHP_CodeSniffer::isCamelCaps($var_name, FALSE, TRUE, FALSE) === FALSE)
                {
                    $var_name = $matches[0];
                    $error   = 'Variable "%s" is not in valid camel caps format';
                    $data    = array($original_var_name);
                    $phpcs_file->addError($error, $stack_ptr, 'StringVarNotCamelCaps', $data);
                }
                elseif(preg_match('|\d|', $var_name))
                {
                    $warning = 'Variable "%s" contains numbers but this is discouraged';
                    $data    = array($original_var_name);
                    $phpcs_file->addWarning($warning, $stack_ptr, 'StringVarContainsNumbers', $data);
                }
            }
        }
    }
}
