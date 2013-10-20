<?php

class PluginNewpublishfrom_HookPublishfrom extends Hook {
	public function RegisterHook() {
		$this->AddHook('topic_add_after','topic_after');
		$this->AddHook('topic_edit_after','topic_after');
		$this->AddHook('template_form_add_topic_topic_end','template_form_add_topic_topic_end');
		$this->AddHook('template_form_add_topic_link_end','template_form_add_topic_topic_end');
		$this->AddHook('template_form_add_topic_question_end','template_form_add_topic_topic_end');
		$this->AddHook('template_form_add_topic_photoset_end','template_form_add_topic_topic_end');

		$this->AddHook('template_publishfrom_theme_select','publishfrom_theme_select');
	}

	public function topic_after($arg){
		$oTopic = $arg['oTopic'];
		$oBlog = $arg['oBlog'];
		$oUser = $this->User_GetUserCurrent();
		if($oUser->isAdministrator()){
			$uid = getRequest(Config::Get('plugin.newpublishfrom.select_name'));
			if(!$uid)
				$uid = $oUser->getId();
			$oTopic = $this->Topic_GetTopicById($oTopic->getId());

			if($oBlog->getType() == 'personal'){
				if($oTopic->getPublish()){
					$oBlogNew = $this->Blog_GetPersonalBlogByUserId($uid);
					$oTopic->setBlog($oBlogNew);
					$oTopic->setBlogId($oBlogNew->getId());
				}
			}

			$oTopic->setUser($this->User_GetUserById($uid));
			$oTopic->setUserId($uid);
			$this->PluginNewpublishfrom_Publishfrom_UpdateTopic($oTopic);
		}
	}

	public function template_form_add_topic_topic_end(){
		if($this->User_GetUserCurrent()->isAdministrator()){
			$oUserCurrent = $this->User_GetUserCurrent();
			$oAuthorId = null;
			if (Router::GetActionEvent() == 'edit') {				$oTopic = $this->Topic_GetTopicById(Router::GetParam(0));
				if (isset($oTopic)) {					$oAuthorId = $oTopic->getUserId();
					$this->Viewer_Assign("oAuthorId", $oAuthorId);
				}
			}
			$aUserList = $this->PluginNewpublishfrom_Publishfrom_GetUserList($oUserCurrent, $oAuthorId);
			$this->Viewer_Assign("oUserCurrent",$oUserCurrent);
			$this->Viewer_Assign("sSelectName",Config::Get('plugin.newpublishfrom.select_name'));
			$this->Viewer_Assign("aUserList",$aUserList);
			return $this->Viewer_Fetch(Plugin::GetTemplatePath(__CLASS__).'form_select.tpl');
		}
	}
}