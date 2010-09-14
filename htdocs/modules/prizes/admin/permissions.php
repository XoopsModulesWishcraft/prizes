<?php
// $Id: common.php,v 1.02 2009/06/23 17:30:00 wishcraft Exp $

include 'admin_header.php';
include_once XOOPS_ROOT_PATH . '/class/xoopsform/grouppermform.php';

xoops_cp_header();
adminMenu(6);

$opform = new XoopsThemeForm(_AM_PERM_ACTION, 'actionform', 'permissions.php', "get");
$op_select = new XoopsFormSelect("", 'action');
$op_select->setExtra('onchange="document.forms.actionform.submit()"');
$op_select->addOptionArray(array(
	"no"=>_SELECT, 
	"pages"=>_AM_PRIZES_PAGES, 
	"category"=>_AM_PRIZES_CATEGORY,
	"forms"=>_AM_PRIZES_FORMS
	));
$op_select->setValue($_REQUEST['action']);
$opform->addElement($op_select);
$opform->display();

switch ($_REQUEST['action']) {
default:
case "pages":	
	
	echo "
		<fieldset><legend style='font-weight: bold; color: #900;'>"._AM_PRIZES_PAGES_PERMHEADER."</legend>\n
		<div style='padding: 2px;'>\n";

	$cat_form = new XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'prizes_pages_access', _AM_PRIZES_PAGES_PERMDESC, '/admin/permissions.php?action='.$_REQUEST['action']);

	$result = $xoopsDB->query("SELECT pid, title FROM " . $xoopsDB->prefix("prizes_pages"));
	if ($xoopsDB->getRowsNum($result))
	{
		while ($cat_row = $xoopsDB->fetcharray($result))
		{
				$cat_form->addItem($cat_row['pid'], ucfirst($cat_row['title']));
		} 
	} 
    echo $cat_form->render();
	echo "</div></fieldset><br />";
	unset ($cat_form);
	break;
case "category":
	echo "
		<fieldset><legend style='font-weight: bold; color: #900;'>"._AM_PRIZES_CATEGORY_PERMHEADER."</legend>\n
		<div style='padding: 2px;'>\n";

	$cat_form = new XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'prizes_category_access', _AM_PRIZES_CATEGORY_PERMDESC, '/admin/permissions.php?action='.$_REQUEST['action']);

	$result = $xoopsDB->query("SELECT cid, title FROM " . $xoopsDB->prefix("prizes_category"));
	if ($xoopsDB->getRowsNum($result))
	{
		while ($cat_row = $xoopsDB->fetcharray($result))
		{
				$cat_form->addItem($cat_row['cid'], ucfirst($cat_row['title']));
		} 
	} 
    echo $cat_form->render();
	echo "</div></fieldset><br />";
	unset ($cat_form);
	break;
case "forms":
	echo "
		<fieldset><legend style='font-weight: bold; color: #900;'>"._AM_PRIZES_FORMS_PERMHEADER."</legend>\n
		<div style='padding: 2px;'>\n";

	$cat_form = new XoopsGroupPermForm('', $xoopsModule->getVar('mid'), 'prizes_form_access', _AM_PRIZES_FORMS_PERMDESC, '/admin/permissions.php?action='.$_REQUEST['action']);

	$result = $xoopsDB->query("SELECT form_id, form_title FROM " . $xoopsDB->prefix("prizes_forms"));
	if ($xoopsDB->getRowsNum($result))
	{
		while ($cat_row = $xoopsDB->fetcharray($result))
		{
				$cat_form->addItem($cat_row['form_id'], ucfirst($cat_row['form_title']));
		} 
	} 
    echo $cat_form->render();
	echo "</div></fieldset><br />";
	unset ($cat_form);
}
footer_adminMenu(7);
xoops_cp_footer();

?>