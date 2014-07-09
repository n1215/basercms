<?php

/**
 * メニューコントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * メニューコントローラー
 *
 * @package Baser.Controller
 */
class MenusController extends AppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Menus';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('Menu');

/**
 * コンポーネント
 *
 * @var array
 * @accesspublic
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'RequestHandler');

/**
 * ヘルパ
 *
 * @var array
 * @access public
 */
	public $helpers = array('BcTime', 'BcForm', 'BcMenu');

/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array();

/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array(
		array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')),
		array('name' => 'メニュー管理', 'url' => array('controller' => 'menus', 'action' => 'index'))
	);

/**
 * メニューの一覧を表示する
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		
		set_time_limit(0);
		$default = array(
			'named' => array('num' => $this->siteConfigs['admin_list_num'])
		);
		$this->setViewConditions('Menu', array('default' => $default));

		$conditions = $this->_createAdminIndexConditions($this->request->data);

		$treeList = $this->Menu->generateTreeList($conditions);
		$menus = $this->Menu->find('all', array('conditions' => $conditions, 'order' => 'Menu.lft'));
		
		$datas = array();
		foreach ($menus as $menu) {
			$name = $treeList[$menu['Menu']['id']];
			$menu['Menu']['prefix'] = '';
			if (preg_match("/^([_]+)/i", $name, $matches)) {
				$menu['Menu']['prefix'] = str_replace('_', '&nbsp&nbsp&nbsp', $matches[1])  . '└';
				$menu['Menu']['depth'] = strlen($matches[1]);
			} else {
				$menu['Menu']['depth'] = 0;
			}
			$datas[] = $menu;
		}
		
		$this->set('datas', $datas);

		if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		// 表示設定
		$this->subMenuElements = array('site_configs', 'menus');
		$this->pageTitle = 'メニュー一覧';
		$this->search = 'menus_index';
		$this->help = 'menus_index';
	}

/**
 * [ADMIN] 登録処理
 *
 * @return void
 * @access public
 */
	public function admin_add() {
		if (!$this->request->data) {
			$this->request->data = array('Menu' => array(
				'enabled' => 1,
				'menu_type' => 2
			));
		} else {
			/* 登録処理 */
			if (!preg_match('/^http/is', $this->request->data['Menu']['link']) && !preg_match('/^\//is', $this->request->data['Menu']['link'])) {
				$this->request->data['Menu']['link'] = '/' . $this->request->data['Menu']['link'];
			}
			$this->Menu->create($this->request->data);
			// データを保存
			if ($this->Menu->save()) {
				clearViewCache();
				$this->setMessage('新規メニュー「' . $this->request->data['Menu']['name'] . '」を追加しました。', false, true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		$menuTypes = $this->Menu->getControlSource('menu_type');
		unset($menuTypes[1]);
		$this->set('menuTypes', $menuTypes);
		$this->set('parents', $this->Menu->getDirList());
		$this->subMenuElements = array('site_configs', 'menus');
		$this->pageTitle = '新規メニュー登録';
		$this->help = 'menus_form';
		$this->render('form');
	}

/**
 * [ADMIN] 編集処理
 *
 * @param	int ID
 * @return void
 * @access public
 */
	public function admin_edit($id) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->Menu->read(null, $id);
		} else {

			/* 更新処理 */
			if (!preg_match('/^http/is', $this->request->data['Menu']['link']) && !preg_match('/^\//is', $this->request->data['Menu']['link'])) {
				$this->request->data['Menu']['link'] = '/' . $this->request->data['Menu']['link'];
			}
			$this->Menu->set($this->request->data);
			if ($this->Menu->save()) {
				clearViewCache();
				$this->setMessage('メニュー「' . $this->request->data['Menu']['name'] . '」を更新しました。', false, true);
				$this->redirect(array('action' => 'index', $id));
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		$menuTypes = $this->Menu->getControlSource('menu_type');
		$this->set('menuTypes', $menuTypes);
		$this->set('parents', $this->Menu->getDirList());
		$this->subMenuElements = array('site_configs', 'menus');
		$this->pageTitle = 'メニュー編集：' . $this->request->data['Menu']['name'];
		$this->help = 'menus_form';
		$this->render('form');
	}

/**
 * [ADMIN] 削除処理 (ajax)
 *
 * @param int ID
 * @return void
 * @access public
 */
	public function admin_ajax_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// メッセージ用にデータを取得
		$post = $this->Menu->read(null, $id);

		/* 削除処理 */
		if ($this->Menu->delete($id)) {
			clearViewCache();
			$message = 'メニュー「' . $post['Menu']['name'] . '」 を削除しました。';
			$this->Menu->saveDbLog($message);
			exit(true);
		}
		exit();
	}

/**
 * [ADMIN] 削除処理
 *
 * @param int ID
 * @return void
 * @access public
 */
	public function admin_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$post = $this->Menu->read(null, $id);

		/* 削除処理 */
		if ($this->Menu->delete($id)) {
			clearViewCache();
			$this->setMessage('メニュー「' . $post['Menu']['name'] . '」 を削除しました。', false, true);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * 管理画面ページ一覧の検索条件を取得する
 *
 * @param array $data
 * @return string
 * @access protected
 */
	protected function _createAdminIndexConditions($data) {
		if (isset($data['Menu'])) {
			$data = $data['Menu'];
		}

		/* 条件を生成 */
		$conditions = array();
		
		if (isset($data['enabled']) && $data['enabled'] !== '') {
			$conditions['Menu.enabled'] = $data['enabled'];
		}
		if (isset($data['status']) && $data['status'] !== '') {
			$conditions = array_merge($conditions, $this->Menu->getConditionAllowPublish());
		}
		return $conditions;
	}

/**
 * [ADMIN] カテゴリの並び替え順を上げる
 * 
 * @param int $id
 * @return void 
 * @access public
 */
	public function admin_ajax_up($id) {
		if ($this->Menu->moveUp($id)) {
			echo true;
		} else {
			$this->ajaxError(500, '一度リロードしてから再実行してみてください。');
		}
		exit();
	}

/**
 * [ADMIN] カテゴリの並び替え順を下げる
 * 
 * @param int $id 
 * @return void
 * @access public
 * @deprecated
 */
	public function admin_ajax_down($id) {
		if ($this->Menu->moveDown($id)) {
			echo true;
		} else {
			$this->ajaxError(500, '一度リロードしてから再実行してみてください。');
		}
		exit();
	}
	
/**
 * [ADMIN] 無効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 * @access public
 */
	public function admin_ajax_disable($id) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, false)) {
			exit(true);
		} else {
			$this->ajaxError(500, $this->Menu->validationErrors);
		}
		exit();
	}

/**
 * [ADMIN] 有効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 * @access public
 */
	public function admin_ajax_enable($id) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, true)) {
			exit(true);
		} else {
			$this->ajaxError(500, $this->Menu->validationErrors);
		}
		exit();
	}
	
/**
 * ステータスを変更する
 * 
 * @param int $id
 * @param boolean $status
 * @return boolean 
 */
	protected function _changeStatus($id, $status) {
		$statusTexts = array(0 => '利用不可', 1 => '利用可');
		$data = $this->Menu->find('first', array('conditions' => array('Menu.id' => $id), 'recursive' => -1));
		$data['Menu']['enabled'] = $status;
		$this->Menu->set($data);
		if ($this->Menu->save()) {
			clearViewCache();
			$statusText = $statusTexts[$status];
			$this->Menu->saveDbLog('メニュー「' . $data['Menu']['name'] . '」 を' . $statusText . 'にしました。');
			return true;
		} else {
			return false;
		}
	}
	
	public function admin_reconstruction() {
		
		set_time_limit(0);
		$Db = ConnectionManager::getDataSource('baser');
		$Db->truncate('menus');
		clearAllCache();
		
		$PageCategory = ClassRegistry::init('PageCategory');
		$excludeIds = array(
			$PageCategory->getAgentId('mobile'),
			$PageCategory->getAgentId('smartphone')
		);
		// 固定ページカテゴリ
		$PageCategory->updateRelatedPage = false;
		$datas = $PageCategory->find('all', array(
			'recursive' => -1, 'order' => 'lft',
			'conditions' => array(
				'not' => array(
					'PageCategory.id' => $excludeIds
				)
			)
		));
		if($datas) {
			foreach($datas as $data) {
				$parent = $PageCategory->getPath($data['PageCategory']['id']);
				if(!in_array($parent[0]['PageCategory']['id'], $excludeIds)) {
					$PageCategory->set($data);
					$PageCategory->afterSave(false);
				}
			}
		}

		// 固定ページ
		$Page = ClassRegistry::init('Page');
		$Page->contentSaving = false;
		$Page->fileSave = false;
		$Page->PageCategory = $PageCategory;
		$datas = $Page->find('all', array('recursive' => -1,
			'conditions' => array(
				'or' => array(
					'not' => array(
						'Page.page_category_id' => $excludeIds
					),
					'Page.page_category_id' => null
		))));
		
		if($datas) {
			foreach($datas as $data) {
				if(!preg_match('/^\/mobile\//', $data['Page']['url']) && !preg_match('/^\/smartphone\//', $data['Page']['url'])) {
					$Page->set($data);
					$Page->afterSave(false);
				}
			}
		}

		// プラグイン
		$PluginContent = ClassRegistry::init('PluginContent');
		$datas = $PluginContent->find('all', array('recursive' => -1));
		if($datas) {
			foreach($datas as $data) {
				$plugin = Inflector::camelize($data['PluginContent']['plugin']);
				$Model = ClassRegistry::init($plugin . 'Content');
				$Model->contentSaving = false;
				$Model->read(null, $data['PluginContent']['content_id']);
				$Model->afterSave(false);
			}
		}
		$this->redirect(array('action' => 'index'));
		
	}
	
}
