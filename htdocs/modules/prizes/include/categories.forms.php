<?php

	function edit_categories_form()
	{
	
		if (isset($_REQUEST['id']))
		{
			$id = intval($_REQUEST['id']);				
			$categorieshandler =& xoops_getmodulehandler('category', 'prizes');
			$categories = $categorieshandler->get($id);	
			$cid = $categories->getVar('cid');
			$title = $categories->getVar('title');	
			$domain = $categories->getVar('domain');	
			$domains = $categories->getVar('domains');				
			$ptitle = 'Edit categories Item';
		} else {
			$cid = 0;
			$title = '';	
			$domain = urlencode(XOOPS_URL);
			$domains = array(0=>urlencode(XOOPS_URL));
			$ptitle = 'New categories Item';
		}
		
		$form = new XoopsThemeForm($ptitle, "edititem", "", "post");

		$form->addElement(new XoopsFormText(_AM_CAT_TITLE, "title", 35, 128, $title));
		if (class_exists('XoopsFormSelectDomains')) {
			$form->addElement(new XoopsFormSelectDomains(_AM_CAT_DOMAIN, "domain", $domain, 1, false));
			$form->addElement(new XoopsFormSelectDomains(_AM_CAT_DOMAINS, "domains", $domains, 8, true));
		} else {
			$form->addElement(new XoopsFormHidden("domain", $domain));				
			foreach($domains as $key => $value)
				$form->addElement(new XoopsFormHidden("domains[".$key.']', $value));				
		}

		$form->addElement(new XoopsFormHidden("id", $cid));
		$form->addElement(new XoopsFormHidden("op", "category"));		
		$form->addElement(new XoopsFormHidden("fct", "save"));		
		$form->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
		$form->display();
	}
	
	function sel_categories_form()
	{
	
		$form = new XoopsThemeForm('Current categories', "current", "", "post");

		$categorieshandler = xoops_getmodulehandler('category','prizes');
		$criteria = new Criteria('1', 1);
		$categories = $categorieshandler->getObjects($criteria);	
		$element = array();
		foreach($categories as $key => $item)
		{
			$element[$key] = new XoopsFormElementTray('Item '.$item->getVar('id').':');
			$element[$key]->addElement(new XoopsFormLabel('', '<a href="index.php?op=category&fct=edit&id='.$item->getVar('cid').'">Edit</a>&nbsp;<a href="index.php?op=category&fct=delete&id='.$item->getVar('cid').'">Delete</a>'));
			$element[$key]->addElement(new XoopsFormText(_AM_CAT_TITLE, 'title['.$item->getVar('cid').']', 25,128,$item->getVar('title')));
			$element[$key]->addElement(new XoopsFormHidden('id['.$key.']', $item->getVar('cid')));
			$form->addElement($element[$key]);
		}
		$form->addElement(new XoopsFormHidden("op", "category"));		
		$form->addElement(new XoopsFormHidden("fct", "saveall"));				
		//$form->addElement(new XoopsFormButton('', 'contents_submit', _SUBMIT, "submit"));
		
		$form->display();
				
	}
		
?>