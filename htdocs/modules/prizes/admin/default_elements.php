<?php
// $Id: default_elements.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $


if( preg_match('/default_elements.php/', $_SERVER['PHP_SELF']) ){
	die('Access denied');
}

$defaults = array();
$defaults[0]['caption'] = 'Your name';
$defaults[0]['req'] = true;
$defaults[0]['order'] = 1;
$defaults[0]['display'] = 1;
$defaults[0]['type'] = 'text';
$defaults[0]['value'] = array(
							0 => $xoopsModuleConfig['t_width'],
							1 => $xoopsModuleConfig['t_max'],
							2 => '{UNAME}'
							);

$defaults[1]['caption'] = 'Email address';
$defaults[1]['req'] = true;
$defaults[1]['order'] = 2;
$defaults[1]['display'] = 1;
$defaults[1]['type'] = 'text';
$defaults[1]['value'] = array(
							0 => $xoopsModuleConfig['t_width'],
							1 => $xoopsModuleConfig['t_max'],
							2 => '{EMAIL}'
							);

$defaults[2]['caption'] = 'Your comments';
$defaults[2]['req'] = true;
$defaults[2]['order'] = 3;
$defaults[2]['display'] = 1;
$defaults[2]['type'] = 'textarea';
$defaults[2]['value'] = array(
							0 => '',
							1 => $xoopsModuleConfig['ta_rows'],
							2 => $xoopsModuleConfig['ta_cols']
							);

?>