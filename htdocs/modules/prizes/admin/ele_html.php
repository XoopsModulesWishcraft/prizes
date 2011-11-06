<?php
// $Id$


if( !preg_match("/editelement.php/", $_SERVER['PHP_SELF']) ){
	exit("Access Denied");
}
$rows = !empty($value[1]) ? $value[1] : $xoopsModuleConfig['ta_rows'];
$cols = !empty($value[2]) ? $value[2] : $xoopsModuleConfig['ta_cols'];
$rows = new XoopsFormText(_AM_ELE_ROWS, 'ele_value[1]', 3, 3, $rows);
$cols = new XoopsFormText(_AM_ELE_COLS, 'ele_value[2]', 3, 3, $cols);
$default = new XoopsFormDhtmlTextArea(_AM_ELE_DEFAULT, 'ele_value[0]', $myts->stripSlashesGPC($value[0]), 10, 50);
$output->addElement($rows, 1);
$output->addElement($cols, 1);
$output->addElement($default);
?>