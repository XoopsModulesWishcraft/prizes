<?php
// $Id$



if( !defined('PRIZES_ROOT_PATH') ){ exit(); }
class PrizesResponse extends XoopsObject {
	function PrizesResponse(){
		$this->XoopsObject();
		$this->initVar("rid", XOBJ_DTYPE_INT);
		$this->initVar("cid", XOBJ_DTYPE_INT);
		$this->initVar("pid", XOBJ_DTYPE_INT);
		$this->initVar("form_id", XOBJ_DTYPE_INT);		
		$this->initVar("fingerprint", XOBJ_DTYPE_TXTBOX, false, false, 32);
		$this->initVar("response", XOBJ_DTYPE_ARRAY);
		$this->initVar("time_response", XOBJ_DTYPE_INT, false, false);
	}
}

class PrizesResponseHandler extends XoopsObjectHandler {
	var $db;
	var $db_table;
	var $perm_name = 'prizes_response_access';
	var $obj_class = 'PrizesResponse';

	function PrizesResponseHandler(&$db){
		$this->db =& $db;
		$this->db_table = $this->db->prefix('prizes_response');
		$this->perm_handler =& xoops_gethandler('groupperm');
	}
	function &getInstance(&$db){
		static $instance;
		if( !isset($instance) ){
			$instance = new PrizesResponseHandler($db);
		}
		return $instance;
	}
	function &create(){
		return new $this->obj_class();
	}

	function &get($id, $fields='*'){
		$id = intval($id);
		if( $id > 0 ){
			$sql = 'SELECT '.$fields.' FROM '.$this->db_table.' WHERE rid='.$id;
			if( !$result = $this->db->query($sql) ){
				return false;
			}
			$numrows = $this->db->getRowsNum($result);
			if( $numrows == 1 ){
				$response = new $this->obj_class();
				$response->assignVars($this->db->fetchArray($result));
				return $response;
			}
			return false;
		}
		return false;
	}

	function insert(&$respons, $force = false){
        if( strtolower(get_class($respons)) != strtolower($this->obj_class)){
            return false;
        }
        if( !$respons->isDirty() ){
            return true;
        }
        if( !$respons->cleanVars() ){
            return false;
        }
		foreach( $respons->cleanVars as $k=>$v ){
			${$k} = $v;
		}
		if( empty($rid) ){
			$rid = $this->db->genId($this->db_table."_rid_seq");
			$sql = sprintf("INSERT INTO %s (
				rid, cid, pid, form_id, fingerprint, response, time_response
				) VALUES (
				%u, %u, %u, %u, %s, %s, %u
				)",
				$this->db_table,
				$rid,
				$cid,
				$pid,
				$form_id,
				$this->db->quoteString(md5(md5($response).md5(time()))),
				$this->db->quoteString($response),
				time()
			);
		}else{
			$respons->setErrors("Could not edit store data in the database.<br /> this is a locked record");
			return false;
		}
        if( false != $force ){
            $result = $this->db->queryF($sql);
        }else{
            $result = $this->db->query($sql);
        }
		if( !$result ){
			$response->setErrors("Could not store data in the database.<br />".$this->db->error().' ('.$this->db->errno().')<br />'.$sql);
			return false;
		}
		if( empty($rid) ){
			$rid = $this->db->getInsertId();
		}
		$respons->setVar('rid', $rid);
		return $rid;
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
			$responses = new $this->obj_class();
			$responses->assignVars($myrow);
			if( !$id_as_key ){
				$ret[] =& $responses;
			}else{
				$ret[$myrow['rid']] =& $responses;
			}
			unset($responses);
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
	
	function deleteResponsePermissions($rid){
		global $xoopsModule;
		$criteria = new CriteriaCompo();
		$criteria->add(new Criteria('gperm_itemid', $rid)); 
		$criteria->add(new Criteria('gperm_modid', $xoopsModule->getVar('mid')));
		$criteria->add(new Criteria('gperm_name', $this->perm_name)); 
		if( $old_perms =& $this->perm_handler->getObjects($criteria) ){
			foreach( $old_perms as $p ){
				$this->perm_handler->delete($p);
			}
		}
		return true;
	}
	
	function insertResponsePermissions($rid, $group_ids){
		global $xoopsModule;
		foreach( $group_ids as $id ){
			$perm =& $this->perm_handler->create();
			$perm->setVar('gperm_name', $this->perm_name);
			$perm->setVar('gperm_itemid', $rid);
			$perm->setVar('gperm_groupid', $id);
			$perm->setVar('gperm_modid', $xoopsModule->getVar('mid'));
			$this->perm_handler->insert($perm);
		}
		return true;
	}
	
	function &getPermittedResponse(){
		global $xoopsUser, $xoopsModule;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		if( $responses =& $this->getObjects($criteria) ){
			$ret = array();
			foreach( $responses as $f ){
				if( false != $this->perm_handler->checkRight($this->perm_name, $f->getVar('rid'), $groups, $xoopsModule->getVar('mid')) ){
					$ret[] = $f;
					unset($f);
				}
			}
			return $ret;
		}
		return false;
	}
	
	function getSingleResponsePermission($rid){
		global $xoopsUser, $xoopsModule;
		$groups = is_object($xoopsUser) ? $xoopsUser->getGroups() : 3;
		if( false != $this->perm_handler->checkRight($this->perm_name, $rid, $groups, $xoopsModule->getVar('mid')) ){
			return true;
		}
		return false;
	}
	
}
?>