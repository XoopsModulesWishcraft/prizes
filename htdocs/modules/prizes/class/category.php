<?php
// $Id$



if( !defined('PRIZES_ROOT_PATH') ){ exit(); }
class PrizesCategory extends XoopsObject {
	function PrizesCategory(){
		$this->XoopsObject();
		$this->initVar("cid", XOBJ_DTYPE_INT);
		$this->initVar("title", XOBJ_DTYPE_TXTBOX, false, true, 255);
		$this->initVar("domain", XOBJ_DTYPE_TXTBOX, false, true, 255);
		$this->initVar("domains", XOBJ_DTYPE_ARRAY, false, true);
	}
}

class PrizesCategoryHandler extends XoopsObjectHandler {
	var $db;
	var $db_table;
	var $perm_name = 'prizes_category_access';
	var $obj_class = 'PrizesCategory';

	function PrizesCategoryHandler(&$db){
		$this->db =& $db;
		$this->db_table = $this->db->prefix('prizes_category');
		$this->perm_handler =& xoops_gethandler('groupperm');
	}
	function &getInstance(&$db){
		static $instance;
		if( !isset($instance) ){
			$instance = new PrizesCategoryHandler($db);
		}
		return $instance;
	}
	function &create(){
		return new $this->obj_class();
	}

	function &get($id, $fields='*'){
		$id = intval($id);
		if( $id > 0 ){
			$sql = 'SELECT '.$fields.' FROM '.$this->db_table.' WHERE cid='.$id;
			if( !$result = $this->db->query($sql) ){
				return false;
			}
			$numrows = $this->db->getRowsNum($result);
			if( $numrows == 1 ){
				$category = new $this->obj_class();
				$category->assignVars($this->db->fetchArray($result));
				return $category;
			}
			return false;
		}
		return false;
	}

	function insert(&$category, $force = false){
        if( strtolower(get_class($category)) != strtolower($this->obj_class)){
            return false;
        }
        if( !$category->isDirty() ){
            return true;
        }
        if( !$category->cleanVars() ){
            return false;
        }
		foreach( $category->cleanVars as $k=>$v ){
			${$k} = $v;
		}
		if( $category->isNew() || empty($cid) ){
			$cid = $this->db->genId($this->db_table."_cid_seq");
			$sql = sprintf("INSERT INTO %s (
				cid, title, domain, domains
				) VALUES (
				%u, %s, %s, %s
				)",
				$this->db_table,
				$cid,
				$this->db->quoteString($title),
				$this->db->quoteString($domain),
				$this->db->quoteString($domains)
			);
		}else{
			$sql = sprintf("UPDATE %s SET
				title = %s,
				domain = %s,
				domains = %s
				WHERE cid = %u",
				$this->db_table,
				$this->db->quoteString($title),
				$this->db->quoteString($domain),
				$this->db->quoteString($domains),
				$cid
			);
		}
        if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		if( !$result ){
			$category->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
			return false;
		}
		if( empty($cid) ){
			$cid = $this->db->getInsertId();
		}
        $category->assignVar('cid', $cid);
		return $cid;
	}
	
	function delete(&$category, $force = false){
		if( strtolower(get_class($category)) != strtolower($this->obj_class) ){
			return false;
		}
		$sql = "DELETE FROM ".$this->db_table." WHERE cid=".$category->getVar("cid")."";
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
			$categorys = new $this->obj_class();
			$categorys->assignVars($myrow);
			if( !$id_as_key ){
				$ret[] =& $categorys;
			}else{
				$ret[$myrow['cid']] =& $categorys;
			}
			unset($categorys);
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
	
	function deleteCategoryPermissions($cid){
		global $xoopsModule;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('gperm_itemid', $cid)); 
		$criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
		$criteria->add(new Criteria('gperm_name', $this->perm_name)); 
		if( $old_perms =& $this->perm_handler->getObjects($criteria) ){
			foreach( $old_perms as $p ){
				$this->perm_handler->delete($p);
			}
		}
		return true;
	}
	
	function insertCategoryPermissions($cid, $group_ids){
		global $xoopsModule;
		foreach( $group_ids as $id ){
			$perm =& $this->perm_handler->create();
			$perm->setVar('gperm_name', $this->perm_name);
			$perm->setVar('gperm_itemid', $cid);
			$perm->setVar('gperm_groupid', $id);
			$perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
			$this->perm_handler->insert($perm);
		}
		return true;
	}
	
	function &getPermittedCategory(){
		global $xoopsUser, $xoopsModule;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		if( $categorys =& $this->getObjects(NULL) ){
			$ret = array();
			foreach( $categorys as $f ){
				if( false != $this->perm_handler->checkRight($this->perm_name, $f->getVar('cid'), $groups, $xoopsModule->getVar('mid')) ){
					$ret[] = $f;
					unset($f);
				}
			}
			return $ret;
		}
		return false;
	}
	
	function getSingleCategoryPermission($cid){
		global $xoopsUser, $xoopsModule;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		if( false != $this->perm_handler->checkRight($this->perm_name, $cid, $groups, $xoopsModule->getVar('mid')) ){
			return true;
		}
		return false;
	}
	
}
?>