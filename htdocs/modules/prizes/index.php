<?php
// $Id$

include 'header.php';

$myts =& MyTextSanitizer::getInstance();
if( empty($_POST['submit']) ){
	$page_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
	$category_id = isset($_GET['cid']) ? intval($_GET['cid']) : 0;
	if ( !empty($category_id) ) {
		$criteria = new Criteria('cid', $category_id);
		$pages =& $prizes_pages_mgr->getObjects($criteria);
		$xoopsOption['template_main'] = 'prizes_index.html';
		include_once XOOPS_ROOT_PATH.'/header.php';
		if( count($pages) > 0 ){
			foreach( $pages as $page ){
				$xoopsTpl->append('prizess',
								array(	'title' => $page->getVar('title'),
										'desc' => $page->getVar('description'),
										'id' => $page->getVar('pid')
								)
						);
			}
			$xoopsTpl->assign('prizes_intro', $myts->makeTareaData4Show($xoopsModuleConfig['intro']));
		}
	} elseif( empty($page_id) ){
		$pages =& $prizes_pages_mgr->getPermittedPages();
		if( false != $pages && count($pages) === 1 ){
			$page = $pages[0];
			include 'include/form_render.php';
		}else{
			$xoopsOption['template_main'] = 'prizes_index.html';
			include_once XOOPS_ROOT_PATH.'/header.php';
			if( count($pages) > 0 ){
				foreach( $pages as $page ){
					if ($page->getVar('default'))
						$xoopsTpl->append('prizess',
										array(	'title' => $page->getVar('title'),
												'desc' => $page->getVar('description'),
												'id' => $page->getVar('pid')
										)
								);
				}
				$xoopsTpl->assign('prizes_intro', $myts->makeTareaData4Show($xoopsModuleConfig['intro']));
			}
		}
	}else{
		if( !$page =& $prizes_pages_mgr->get($page_id) ){
			header("Location: ".PRIZES_URL);
			exit();
		}else{
			if( false != $prizes_pages_mgr->getSinglePagePermission($page_id) ){
				include 'include/form_render.php';
			}else{
				header("Location: ".PRIZES_URL);
				exit();
			}
		}
	}
	include XOOPS_ROOT_PATH.'/footer.php';
}else{
	$form_id = isset($_POST['form_id']) ? intval($_POST['form_id']) : 0;
	$page_id = isset($_POST['page_id']) ? intval($_POST['page_id']) : 0;
	if( empty($form_id) || !$form =& $prizes_form_mgr->get($form_id) || $prizes_form_mgr->getSingleFormPermission($form_id) == false ) {
		header("Location: ".PRIZES_URL);
		exit();
	}
	if(  empty($page_id) || !$page =& $prizes_pages_mgr->get($page_id) || $prizes_pages_mgr->getSinglePagePermission($page_id) == false ) {
		header("Location: ".PRIZES_URL);
		exit();
	}

	include 'include/form_execute.php';
}

?>