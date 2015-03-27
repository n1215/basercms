<?php
/**
 * ページヘルパー
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('Helper', 'View');

/**
 * ページヘルパー
 *
 * @package Baser.View.Helper
 * @property BcBaserHelper $BcBaser
 */
class BcPageHelper extends Helper {

/**
 * ページモデル
 * 
 * @var Page
 * @access public
 */
	public $Page = null;

/**
 * data
 * @var array
 * @access public
 */
	public $data = array();

/**
 * ヘルパー
 * 
 * @var array
 */
	public $helpers = array('BcBaser');

/**
 * construct
 * 
 * @param View $View ビュー
 */
	public function __construct(View $View) {
		parent::__construct($View);
		if (ClassRegistry::isKeySet('Page')) {
			$this->Page = ClassRegistry::getObject('Page');
		} else {
			$this->Page = ClassRegistry::init('Page', 'Model');
		}
	}

/**
 * beforeRender
 * 
 * @param string $viewFile (継承もとで利用中) The view file that is going to be rendered
 * @return void
 */
	public function beforeRender($viewFile) {
		if ($this->request->is('page')) {
			$this->request->data['Page'] = $this->_View->get('page');
			$this->request->data['PageCategory'] = $this->_View->get('pageCategory');
		}
	}

/**
 * ページ機能用URLを取得する
 * 
 * @param array $page 固定ページデータ
 * @return string URL
 */
	public function getUrl($page) {
		if (isset($page['Page'])) {
			$page = $page['Page'];
		}
		if (!isset($page['url'])) {
			return '';
		}
		return $this->Page->convertViewUrl($page['url']);
	}

/**
 * 現在のページが所属するカテゴリデータを取得する
 * 
 * @return array 失敗すると getCategory() は FALSE を返します。
 */
	public function getCategory() {
		return $this->_View->get('pageCategory', false);
	}

/**
 * 現在のページが所属する親のカテゴリを取得する
 *
 * @param bool $top 親カテゴリが存在するかどうか、 オプションのパラメータ、初期値はオプションのパラメータ、初期値は false
 * @return array
 */
	public function getParentCategory($top = false) {
		$category = $this->getCategory();
		if (empty($category['id'])) {
			return false;
		}

		if (!$top) {
			return $this->Page->PageCategory->getParentNode($category['id']);
		}

		$path = $this->Page->PageCategory->getPath($category['id']);
		if ($path) {
			return $path[0];
		}
		return false;
	}

/**
 * ページリストを取得する
 * 
 * @param int $pageCategoryId ページカテゴリID
 * @param int $recursive 再帰的に取得するかどうか
 * @return array
 */
	public function getPageList($pageCategoryId, $recursive = null) {
		return $this->requestAction('/contents/get_page_list_recursive', array('pass' => array($pageCategoryId, $recursive)));
	}

/**
 * カテゴリ名を取得する
 * 
 * @return mixed string / false
 */
	public function getCategoryName() {
		$category = $this->getCategory();
		if ($category['name']) {
			return $category['name'];
		}
		return false;
	}

/**
 * 公開状態を取得する
 *
 * @param array $data データリスト
 * @return bool 公開状態
 */
	public function allowPublish($data) {
		if (isset($data['Page'])) {
			$data = $data['Page'];
		}

		$allowPublish = (int)$data['status'];

		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if (($data['publish_begin'] != 0 && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
			($data['publish_end'] != 0 && $data['publish_end'] <= date('Y-m-d H:i:s'))) {
			$allowPublish = false;
		}

		return $allowPublish;
	}

/**
 * ページカテゴリ間の次の記事へのリンクを取得する
 *
 * @param string $title
 * @param array $attributes
 * @return void|string コンテンツナビが無効の場合のみ、空文字を返す
 */
	public function nextLink($title = '', $attributes = array()) {
		if (!$this->contentsNaviAvailable()) {
			return '';
		}

		$_attributes = array('class' => 'next-link', 'arrow' => ' ≫');
		$attributes = am($_attributes, $attributes);

		$arrow = $attributes['arrow'];
		unset($attributes['arrow']);

		$conditions = am(array(
			'Page.sort >' => $this->request->data['Page']['sort'],
			'Page.page_category_id' => $this->request->data['Page']['page_category_id']
			), $this->Page->getConditionAllowPublish());
		$nextPost = $this->Page->find('first', array(
			'conditions' => $conditions,
			'fields' => array('title', 'url'),
			'order' => 'sort',
			'recursive' => -1,
			'cache' => false
		));

		if ($nextPost) {
			if (!$title) {
				$title = $nextPost['Page']['title'] . $arrow;
			}
			$this->BcBaser->link($title, preg_replace('/^\/mobile/', '/m', $nextPost['Page']['url']), $attributes);
		}
	}

/**
 * ページカテゴリ間の前の記事へのリンクを取得する
 *
 * @param string $title
 * @param array $attributes
 * @return void|string
 */
	public function prevLink($title = '', $attributes = array()) {
		if (!$this->contentsNaviAvailable()) {
			return '';
		}

		if (ClassRegistry::isKeySet('Page')) {
			$PageClass = ClassRegistry::getObject('Page');
		} else {
			$PageClass = ClassRegistry::init('Page');
		}

		$_attributes = array('class' => 'prev-link', 'arrow' => '≪ ');
		$attributes = am($_attributes, $attributes);

		$arrow = $attributes['arrow'];
		unset($attributes['arrow']);

		$conditions = am(array(
			'Page.sort <' => $this->request->data['Page']['sort'],
			'Page.page_category_id' => $this->request->data['Page']['page_category_id']
			), $PageClass->getConditionAllowPublish());
		$nextPost = $PageClass->find('first', array(
			'conditions' => $conditions,
			'fields' => array('title', 'url'),
			'order' => 'sort DESC',
			'recursive' => -1,
			'cache' => false
		));
		if ($nextPost) {
			if (!$title) {
				$title = $arrow . $nextPost['Page']['title'];
			}
			$this->BcBaser->link($title, preg_replace('/^\/mobile/', '/m', $nextPost['Page']['url']), $attributes);
		}
	}

/**
 * コンテンツナビ有効チェック
 *
 * @return bool
 */
	public function contentsNaviAvailable() {
		return !empty($this->request->data['Page']['page_category_id'])
			&& !empty($this->request->data['PageCategory']['contents_navi']);
	}

/**
 * 固定ページのコンテンツを出力する
 * 
 * @return void
 */
	public function content() {
		$agent = '';
		if (Configure::read('BcRequest.agentPrefix')) {
			$agent = Configure::read('BcRequest.agentPrefix');
		}
		$path = $this->_View->get('pagePath');

		if ($agent) {
			$url = '/' . implode('/', $this->request->params['pass']);
			$linked = $this->Page->isLinked($agent, $url);
			if (!$linked) {
				$path = $agent . DS . $path;
			}
		}
		echo $this->_View->evaluate(getViewPath() . 'Pages' . DS . $path . '.php', $this->_View->viewVars);
	}

/**
 * テンプレートを取得
 * セレクトボックスのソースとして利用
 * 
 * @param string $type layout or content
 * @param string $agent '' or mobile or smartphone
 * @return array
 */
	public function getTemplates($type = 'layout', $agent = '') {
		$agentPrefix = '';
		if ($agent) {
			$agentPrefix = Configure::read('BcAgent.' . $agent . '.prefix');
		}

		$siteConfig = Configure::read('BcSite');
		$themePath = WWW_ROOT . 'theme' . DS . $siteConfig['theme'] . DS;
		$viewPaths = array_merge(array($themePath), App::path('View'));
		$ext = Configure::read('BcApp.templateExt');

		$templates = array();

		foreach ($viewPaths as $viewPath) {

			$templatePath = '';
			switch ($type) {
				case 'layout':
					if (!$agentPrefix) {
						$templatePath = $viewPath . 'Layouts' . DS;
					} else {
						$templatePath = $viewPath . 'Layouts' . DS . $agentPrefix . DS;
					}
					break;
				case 'content':
					if (!$agentPrefix) {
						$templatePath = $viewPath . 'Pages' . DS . 'templates' . DS;
					} else {
						$templatePath = $viewPath . 'Pages' . DS . $agentPrefix . DS . 'templates' . DS;
					}
					break;
			}

			if (!$templatePath) {
				continue;
			}

			$Folder = new Folder($templatePath);
			$files = $Folder->read(true, true);
			if ($files[1]) {
				foreach ($files[1] as $file) {
					if (preg_match('/(.+)' . preg_quote($ext) . '$/', $file, $matches)) {
						if (!in_array($matches[1], $templates)) {
							$templates[] = $matches[1];
						}
					}
				}
			}
		}

		if ($templates) {
			return array_combine($templates, $templates);
		} else {
			return array();
		}
	}

	public function treeList($datas, $recursive = 0) {
		return $this->BcBaser->getElement('pages/index_tree_list', array('datas' => $datas, 'recursive' => $recursive));
	}

}
