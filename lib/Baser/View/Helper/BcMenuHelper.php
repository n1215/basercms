<?php
/**
 * メニューヘルパー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * メニューヘルパー
 *
 * @package Baser.View.Helper
 */
class BcMenuHelper extends AppHelper {
/**
 * 公開状態を取得する
 *
 * @param array データリスト
 * @return boolean 公開状態
 */
	public function allowPublish($data) {

		if (isset($data['Menu'])) {
			$data = $data['Menu'];
		}

		$allowPublish = (int) $data['status'];

		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if (($data['publish_begin'] != 0 && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
			($data['publish_end'] != 0 && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
			$allowPublish = false;
		}

		return $allowPublish;
	}
	
/**
 * 利用可否を確認する
 * 
 * @param array $data メニューデータ
 * @return boolean 利用可否
 */
	public function enabled($data) {
		if(isset($data['Menu'])) {
			$data = $data['Menu'];
		}
		return (int) $data['enabled'];
	}
}