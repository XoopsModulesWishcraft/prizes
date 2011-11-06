<?php
// $Id$
include('functions.php');

$module_handler = xoops_gethandler('module');
$xoopsModule =& $module_handler->getByDirname('prizes');

if( !defined("PRIZES_CONSTANTS_DEFINED") ){
	define("PRIZES_URL", XOOPS_URL.'/modules/'.$xoopsModule->getVar('dirname').'/');
	define("PRIZES_ROOT_PATH", XOOPS_ROOT_PATH.'/modules/'.$xoopsModule->getVar('dirname').'/');
	define("PRIZES_UPLOAD_PATH", $xoopsModuleConfig['uploaddir'].'/');

	define("PRIZES_CONSTANTS_DEFINED", true);
}

$prizes_form_mgr =& xoops_getmodulehandler('forms', 'prizes');
$prizes_category_mgr =& xoops_getmodulehandler('category', 'prizes');
$prizes_response_mgr =& xoops_getmodulehandler('response', 'prizes');
$prizes_pages_mgr =& xoops_getmodulehandler('pages', 'prizes');

if( false != PRIZES_UPLOAD_PATH ){
	if( !is_dir(PRIZES_UPLOAD_PATH) ){
		$oldumask = umask(0);
		mkdir(PRIZES_UPLOAD_PATH, 0777);
		umask($oldumask);
	}
	if( is_dir(PRIZES_UPLOAD_PATH) && !is_writable(PRIZES_UPLOAD_PATH) ){
		chmod(PRIZES_UPLOAD_PATH, 0777);
	}
}

?>