<?php
/**
 * Got_Sniffs_Commenting_FileCommentSniff
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */

if(class_exists('PHP_CodeSniffer_Standards_AbstractPatternSniff', TRUE) === FALSE)
{
    throw new PHP_CodeSniffer_Exception('Class PHP_CodeSniffer_Standards_AbstractPatternSniff not found');
}

/**
 * GoT_Sniffs_ControlStructures_ControlSignatureSniff.
 *
 * PHP version 5
 *
 * @category PHP
 * @package  CodeSniffer
 * @author   Pierre Rambaud <pierre.rambaud86@gmail.com>
 * @license  http://matrix.squiz.net/developer/tools/php_cs/licence BSD Licence
 * @link     http://rambaudpierre.fr
 */

class GoT_Sniffs_ControlStructures_ControlSignatureSniff extends PHP_CodeSniffer_Standards_AbstractPatternSniff
{

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
     * Returns the patterns that this test wishes to verify.
     *
     * @return array(string)
     */
    protected function getPatterns()
    {
        return array(
            'tryEOL...{...}EOL...catch(...)EOL...{',
            'doEOL...{...}EOL...while(...);',
            'while(...)EOL...{',
            'for(...)EOL...{',
            'if(...)EOL...{',
            'foreach(...)EOL...{',
            '}EOL...elseif(...)EOL...{',
            '}EOL...elseEOL...{',
        );
    }
}
