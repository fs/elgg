<?php
/**
 * Elgg XML output pageshell
 *
 * @package Elgg
 * @subpackage Core
 * @author Curverider Ltd
 * @link http://elgg.org/
 *
 */

header("Content-Type: text/xml");
header("Content-Length: " . strlen($vars['body']));
echo "<?xml version='1.0' encoding='UTF-8'?>\n";
echo $vars['body'];