<?php
function addcslash($str) {
	return addcslashes($str,"'");
}

class PluginNewpublishfrom_ModulePublishfrom_MapperPublishfrom extends Mapper {
	public function getUserList($current = null, $author = null){
		$users = Config::Get('plugin.newpublishfrom.user_logins');
		$ids = Config::Get('plugin.newpublishfrom.user_ids');
		$expr = Config::Get('plugin.newpublishfrom.user_id_expression');
		if (isset($author)) {			$ids[] = $author;		}
		if(is_array($users)){			$users = array_unique(array_map("addcslash", $users));
			$logins_in = '\''.implode("','", $users).'\'';
		}
		if($expr){
			$expr = explode("-",$expr);
			$s = $expr[0]; $e = $expr[1];
			while($s <= $e) {
				$ids[] = $s++;
			}
		}
		if(is_array($ids)){
			$ids = array_unique(array_map("intval", $ids), SORT_NUMERIC);
			$ids_in = implode(",", $ids);
		}
		if($logins_in||$ids_in){
			$or = ($logins_in&&$ids_in?' OR ':'');
			$sql = "SELECT * FROM ".Config::Get('db.table.user')." WHERE (".($logins_in?"user_login IN ($logins_in)":'').$or.($ids_in?"user_id IN ($ids_in)":'').")";
			if (isset($current)) {
				$sql .= " AND user_id <> " . $current;
			}
			return $this->oDb->select($sql);
		}
		return array();
	}

	public function UpdateTopic($oTopic) {
		$sql = "UPDATE ".Config::Get('db.table.topic')."
			SET
				user_id= ?d,
				blog_id= ?d
			WHERE
				topic_id = ?d
		";
		if ($this->oDb->query($sql,$oTopic->getUserId(),$oTopic->getBlogId(),$oTopic->getId())) {
			//$this->UpdateTopicContent($oTopic);
			return true;
		}
		return false;
	}
}