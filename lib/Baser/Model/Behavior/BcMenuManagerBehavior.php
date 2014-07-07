<?php
class BcMenuManagerBehavior extends ModelBehavior {
	
	public $Menu = null;
/**
 * Setup this behavior with the specified configuration settings.
 *
 * @param Model $model Model using this behavior
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $model, $config = array()) {
		$this->Menu = ClassRegistry::init('Menu');
	}
	
/**
 * メニューを保存する
 * 
 * array(
 *	'menu_type'		=> 'タイプ',
 *	'status'		=> '公開状態',
 *	'publish_begin' => '公開開始日',
 *	'publish_end'	=> '公開終了日',
 *	'link'			=> '公開ページリンク',
 *	'edit_link'		=> '編集リンク',
 *	'parent_id'		=> '親カテゴリ',
 *	'enabled'		=> '利用可否',
 * );
 * @param Model $Model
 * @param type $data
 * @return type
 */
	public function saveMenu(Model $Model, $data) {
		
		if (!$data) {
			return;
		}
		if(empty($data['model_id']) && !empty($data['id'])) {
			$data['model_id'] = $data['id'];
			unset($data['id']);
		}
		$data['model'] = $Model->alias;
		$id = '';
		if (!empty($data['model_id'])) {
			$id = $this->getMenuId($Model, $data['model_id']);
		}
		if ($id) {
			$data['id'] = $id;
			unset($data['parent_id']);	// 編集時に階層構造は変更しない
			$this->Menu->set($data);
		} else {
			
			$this->Menu->create($data);
		}
		$result = $this->Menu->save();
		return $result;
		
	}
	
/**
 * ビヘイビアを実装したモデルのIDで、メニューのIDを取得する
 * 
 * @param Model $Model モデル
 * @param type $modelId モデルID
 * @return int Or false;
 */
	public function getMenuId(Model $Model, $modelId) {
		$id = $this->Menu->field('id', array(
			'Menu.model' => $Model->alias,
			'Menu.model_id' => $modelId
		));
		if(!$id) {
			$id = null;
		}
		return $id;
	}
	
	public function deleteMenu() {
		
	}
	
}