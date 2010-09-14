<?php
// $Id: admin_header.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $


include '../../../include/cp_header.php';
include '../include/common.php';
include '../include/forms.php';
define('PRIZES_ADMIN_URL', PRIZES_URL.'admin/index.php');

function adminHtmlHeader(){
	xoops_cp_header();
//	//$xTheme->loadModuleAdminMenu(0);
}
?>