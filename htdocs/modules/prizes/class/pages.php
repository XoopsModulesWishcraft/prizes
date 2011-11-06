<?php
// $Id$



if( !defined('PRIZES_ROOT_PATH') ){ exit(); }
class PrizesPages extends XoopsObject {
	function PrizesPages(){
		$this->XoopsObject();
		$this->initVar("pid", XOBJ_DTYPE_INT);
		$this->initVar("cid", XOBJ_DTYPE_INT);		
		$this->initVar("form_id", XOBJ_DTYPE_INT);				
		$this->initVar("default", XOBJ_DTYPE_INT);						
		$this->initVar("weight", XOBJ_DTYPE_INT);						
		$this->initVar("html", XOBJ_DTYPE_OTHER);
		$this->initVar("title", XOBJ_DTYPE_TXTBOX, false, false, 128);
		$this->initVar("description", XOBJ_DTYPE_TXTBOX, false, false, 255);
	}
}

class PrizesPagesHandler extends XoopsObjectHandler {
	var $db;
	var $db_table;
	var $perm_name = 'prizes_pages_access';
	var $obj_class = 'PrizesPages';

	function PrizesPagesHandler(&$db){
		$this->db =& $db;
		$this->db_table = $this->db->prefix('prizes_pages');
		$this->perm_handler =& xoops_gethandler('groupperm');
	}
	function &getInstance(&$db){
		static $instance;
		if( !isset($instance) ){
			$instance = new PrizesPagesHandler($db);
		}
		return $instance;
	}
	function &create(){
		return new $this->obj_class();
	}

	function &get($id, $fields='*'){
		$id = intval($id);
		if( $id > 0 ){
			$sql = 'SELECT '.$fields.' FROM '.$this->db_table.' WHERE pid='.$id;
			if( !$result = $this->db->query($sql) ){
				return false;
			}
			$numrows = $this->db->getRowsNum($result);
			if( $numrows == 1 ){
				$page = new $this->obj_class();
				$page->assignVars($this->db->fetchArray($result));
				return $page;
			}
			return false;
		}
		return false;
	}

	function insert(&$page, $force = false){
        if( strtolower(get_class($page)) != strtolower($this->obj_class)){
            return false;
        }
        if( !$page->isDirty() ){
            return true;
        }
        if( !$page->cleanVars() ){
            return false;
        }
		foreach( $page->cleanVars as $k=>$v ){
			${$k} = $v;
		}
		if( $page->isNew() || empty($pid) ){
			$pid = $this->db->genId($this->db_table."_pid_seq");
			$sql = sprintf("INSERT INTO %s (
				pid, cid, form_id, `default`, html, title, description, weight
				) VALUES (
				%u, %u, %u, %u, %s, %s, %s, %u
				)",
				$this->db_table,
				$pid,
				$cid,
				$form_id,
				$default,
				$this->db->quoteString($html),
				$this->db->quoteString($title),
				$this->db->quoteString($description),
				$weight				
			);
		}else{
			$sql = sprintf("UPDATE %s SET
				html = %s,
				title = %s,
				description = %s,
				weight = %u,
				cid = %u,
				form_id = %u,
				`default` = %u
				WHERE pid = %u",
				$this->db_table,
				$this->db->quoteString($html),
				$this->db->quoteString($title),
				$this->db->quoteString($description),
				$weight,
				$cid,
				$form_id,
				$default,
				$pid
			);
		}
        if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		if( !$result ){
			$page->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
			return false;
		} else {
			if( empty($pid) ){
				$pid = $this->db->getInsertId();
			}
			$sql = sprintf("UPDATE %s SET `default` = 0 WHERE cid = %u and pid != %u", $this->db_table, $cid, $pid);
	        if( false != $force ){
				$resultb = $this->db->queryF($sql);
			}else{
				$resultb = $this->db->query($sql);
			}
		}
        $page->assignVar('pid', $pid);
		return $pid;
	}
	
	function delete(&$page, $force = false){
		if( strtolower(get_class($page)) != strtolower($this->obj_class) ){
			return false;
		}
		$sql = "DELETE FROM ".$this->db_table." WHERE pid=".$page->getVar("pid")."";
        if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		return true;
	}

	function &getObjects($criteria = null, $fields='*', $id_as_key = false){
		$ret = array();
		$limit = $start = 0;
		$sql = 'SELECT '.$fields.' FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
			if( $criteria->getSort() != '' ){
				$sql .= ' ORDER BY '.$criteria->getSort().' '.$criteria->getOrder();
			}
			$limit = $criteria->getLimit();
			$start = $criteria->getStart();
		}
		$result = $this->db->query($sql, $limit, $start);
		if( !$result ){
			return false;
		}
		while( $myrow = $this->db->fetchArray($result) ){
			$pages = new $this->obj_class();
			$pages->assignVars($myrow);
			if( !$id_as_key ){
				$ret[] =& $pages;
			}else{
				$ret[$myrow['pid']] =& $pages;
			}
			unset($pages);
		}
		return count($ret) > 0 ? $ret : false;
	}
	
    function getCount($criteria = null){
		$sql = 'SELECT COUNT(*) FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
		}
		$result = $this->db->query($sql);
		if( !$result ){
			return 0;
		}
		list($count) = $this->db->fetchRow($result);
		return $count;
	}
    
    function deleteAll($criteria = null){
		$sql = 'DELETE FROM '.$this->db_table;
		if( isset($criteria) && is_subclass_of($criteria, 'criteriaelement') ){
			$sql .= ' '.$criteria->renderWhere();
		}
		if( !$result = $this->db->query($sql) ){
			return false;
		}
		return true;
	}
	
	function deletePagesPermissions($pid){
		global $xoopsModule;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('gperm_itemid', $pid)); 
		$criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
		$criteria->add(new Criteria('gperm_name', $this->perm_name)); 
		if( $old_perms =& $this->perm_handler->getObjects($criteria) ){
			foreach( $old_perms as $p ){
				$this->perm_handler->delete($p);
			}
		}
		return true;
	}
	
	function insertPagesPermissions($pid, $group_ids){
		global $xoopsModule;
		foreach( $group_ids as $id ){
			$perm =& $this->perm_handler->create();
			$perm->setVar('gperm_name', $this->perm_name);
			$perm->setVar('gperm_itemid', $pid);
			$perm->setVar('gperm_groupid', $id);
			$perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
			$this->perm_handler->insert($perm);
		}
		return true;
	}
	
	function &getPermittedPages(){
		global $xoopsUser, $xoopsModule;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('weight', 1, '>='), 'OR');
		$criteria->setSort('weight');
		$criteria->setOrder('ASC');
		if( $pages =& $this->getObjects($criteria) ){
			$ret = array();
			foreach( $pages as $f ){
				if( false != $this->perm_handler->checkRight($this->perm_name, $f->getVar('pid'), $groups, $xoopsModule->getVar('mid')) ){
					$ret[] = $f;
					unset($f);
				}
			}
			return $ret;
		}
		return false;
	}
	
	function getSinglePagePermission($pid){
		global $xoopsUser, $xoopsModule;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		if( false != $this->perm_handler->checkRight($this->perm_name, $pid, $groups, $xoopsModule->getVar('mid')) ){
			return true;
		}
		return false;
	}
	
}
?>