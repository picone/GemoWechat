<?php

namespace Home\Behaviors;

class LoginBehavior extends \Think\Behavior {
	public function run(&$param) {
		if((CONTROLLER_NAME!='User'||ACTION_NAME!='index')&&(CONTROLLER_NAME!='Evaluate'||ACTION_NAME!='index')&&(CONTROLLER_NAME!='Contact'||ACTION_NAME!='index')&&CONTROLLER_NAME!='Wechat'&&CONTROLLER_NAME!='Address'&&!isset($_SESSION['user'])){
			redirect('/User');
			exit;
		}
	}
}