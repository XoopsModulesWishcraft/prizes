<?php
// $Id: ele_text.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $


if( !preg_match("/editelement.php/", $_SERVER['PHP_SELF']) ){
	exit("Access Denied");
}

$size = !empty($value[0]) ? intval($value[0]) : $xoopsModuleConfig['t_width'];
$max = !empty($value[1]) ? intval($value[1]) : $xoopsModuleConfig['t_max'];
$size = new XoopsFormText(_AM_ELE_SIZE, 'ele_value[0]', 3, 3, $size);
$max = new XoopsFormText(_AM_ELE_MAX_LENGTH, 'ele_value[1]', 3, 3, $max);
$default = new XoopsFormText(_AM_ELE_DEFAULT, 'ele_value[2]', 50, 255, $myts->htmlspecialchars($myts->stripSlashesGPC($value[2])));
$default->setDescription(_AM_ELE_TEXT_DESC);
$output->addElement($size, 1);
$output->addElement($max, 1);
$output->addElement($default);

?>