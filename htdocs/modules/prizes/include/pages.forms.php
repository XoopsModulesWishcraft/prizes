<?php

	function edit_pages_form()
	{
	
		if (isset($_REQUEST['id']))
		{
			$id = intval($_REQUEST['id']);				
			$pagesshandler =& xoops_getmodulehandler('pages', 'prizes');
			$page = $pagesshandler->get($id);	
			$pid = $page->getVar('pid');
			$cid = $page->getVar('cid');
			$weight = $page->getVar('weight');
			$form_id = $page->getVar('form_id');	
			$html = $page->getVar('html');	
			$title = $page->getVar('title');
			$default = $page->getVar('default');				
			$description = $page->getVar('description');							
			$weight = $page->getVar('weight');							
			$ptitle = 'Edit Page Item';
		} else {
			$pid = 0;
			$cid = 0;
			$weight = 1;
			$form_id = 0;	
			$html = '';	
			$title = '';				
			$ptitle = 'New Page Item';
		}
		
		$editor_config['editor'] = 'fckeditor';
		
		$form = new XoopsThemeForm($ptitle, "edititem", "", "post");

		$form->addElement(new XoopsFormText(_AM_PAGE_TITLE, "title", 35, 128, $title));
		$form->addElement(new XoopsFormTextArea(_AM_PAGE_DESCRIPTION, "description", $description, 5, 50));
		$form->addElement(new XoopsFormText(_AM_PAGE_WEIGHT, "weight", 5, 4, $weight));
		$form->addElement(new PrizesFormSelectCategory(_AM_PAGE_CAT, "cid", $cid, 1, false));
		$form->addElement(new PrizesFormSelectForms(_AM_PAGE_FORMS, "form_id", $form_id, 1, false));		

		$editor_config['value'] = $html;
		$form->addElement(new XoopsFormEditor(_AM_PAGE, "html", $editor_config));
		$form->addElement(new XoopsFormRadioYN(_AM_PAGE_DEFAULT, "default", $default));
		
		$form->addElement(new XoopsFormHidden("id", $pid));
		$form->addElement(new XoopsFormHidden("op", "pages"));		
		$form->addElement(new XoopsFormHidden("fct", "save"));		
		$form->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
		$form->display();
	}
	
	function sel_pages_form()
	{
	
		$form = new XoopsThemeForm('Pages Available', "current", "", "post");

		$pagesshandler =& xoops_getmodulehandler('pages', 'prizes');
		if (isset($_REQUEST['cid']))
			$criteria = new Criteria('cid', $_REQUEST['cid']);
		else
			$criteria = new Criteria('1', 1);

		$pages = $pagesshandler->getObjects($criteria);	
		$element = array();
		foreach($pages as $key => $item)
		{
			$element[$key] = new XoopsFormElementTray('Item '.$item->getVar('pid').':');
			$element[$key]->addElement(new XoopsFormLabel('', '<a href="index.php?op=pages&fct=edit&id='.$item->getVar('pid').'">Edit</a>&nbsp;<a href="index.php?op=pages&fct=delete&id='.$item->getVar('pid').'">Delete</a>'));
			$element[$key]->addElement(new XoopsFormText(_AM_PAGE_TITLE, 'title['.$item->getVar('pid').']', 25,128,$item->getVar('title')));
			$element[$key]->addElement(new XoopsFormHidden('id['.$key.']', $item->getVar('pid')));
			$form->addElement($element[$key]);
		}
		$form->addElement(new XoopsFormHidden("op", "pages"));		
		$form->addElement(new XoopsFormHidden("fct", "saveall"));				
		//$form->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
		
		$form->display();
				
	}
		
?>