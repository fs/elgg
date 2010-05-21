<?php
/*******************************************************************************
 * Contains the Fake FirePHP classes
 * 
 * This page holds all of the classes to accept fire PHP calls - and log their errors
 * 
 * @package Elgg Developer Tools
 * @author Aaron Saray (102degrees.com)
 * @author Cash Costello
 ******************************************************************************/

/**
 * Static FB class reimplementation
 * 
 * since callStatic is not yet implemented, we have to define each one of the
 * other elements like this.  
 */
class FB
{
    public static function setEnabled(){self::logFirePHPStillInUse(__METHOD__);}
    public static function getEnabled(){self::logFirePHPStillInUse(__METHOD__);}
    public static function setObjectFilter(){self::logFirePHPStillInUse(__METHOD__);}
    public static function setOptions(){self::logFirePHPStillInUse(__METHOD__);}
    public static function send(){self::logFirePHPStillInUse(__METHOD__);}
    public static function group(){self::logFirePHPStillInUse(__METHOD__);}
    public static function groupEnd(){self::logFirePHPStillInUse(__METHOD__);}
    public static function log(){self::logFirePHPStillInUse(__METHOD__);}
    public static function info(){self::logFirePHPStillInUse(__METHOD__);}
    public static function warn(){self::logFirePHPStillInUse(__METHOD__);}
    public static function error(){self::logFirePHPStillInUse(__METHOD__);}
    public static function dump(){self::logFirePHPStillInUse(__METHOD__);}
    public static function trace(){self::logFirePHPStillInUse(__METHOD__);}
    public static function table(){self::logFirePHPStillInUse(__METHOD__);}
    
    /**
     * Class that logs the error
     * 
     * @param $function string The function name
     */
    public static function logFirePHPStillInUse($function)
    {
        $trace = debug_backtrace();
        $parts = $trace[1];
        
        $error = "{$function}() was called on line {$parts['line']} in file {$parts['file']}"; 
        error_log($error);
    }
}

/**
 * Rewrites the fb function in FirePHP to log the error
 */
function fb()
{
    FB::logFirePHPStillInUse(__FUNCTION__);
}

/**
 * Class rewrites the FirePHP class by logging any statics and instantiations
 */
class FirePHP
{
    public static function getInstance(){FB::logFirePHPStillInUse(__METHOD__);}
    public static function init(){FB::logFirePHPStillInUse(__METHOD__);}
    public function __construct(){FB::logFirePHPStillInUse(__METHOD__);}
    public function __call($function, $args){/**do nothing - logged constructor **/}
}
?>