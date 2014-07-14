<?php
/**
 * [ADMIN] メニュー一覧
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php if (!empty($datas)): ?>
	<?php $currentDepth = 0 ?>
	<?php foreach ($datas as $key => $data): ?>
		<?php
		$rowIdTmps[$data['Menu']['depth']] = $data['Menu']['id'];

		// 階層が上がったタイミングで同階層よりしたのIDを削除
		if ($currentDepth > $data['Menu']['depth']) {
			$i = $data['Menu']['depth'] + 1;
			while (isset($rowIdTmps[$i])) {
				unset($rowIdTmps[$i]);
				$i++;
			}
		}
		$currentDepth = $data['Menu']['depth'];
		$rowClassies = array();
		if(!empty($data['Menu']['path'])) {
			foreach($data['Menu']['path'] as $path) {
				$rowClassies[] = 'children-' . $path;
			}
		}
		if(!empty($this->request->params['pass'][0])) {
			$rowClassies[] = 'row-group-' . $this->request->params['pass'][0];
		}
		foreach ($rowIdTmps as $rowIdTmp) {
			$rowClassies[] = 'row-group-' . $rowIdTmp;
		}
		$rowClassies[] = 'depth-' . $data['Menu']['depth'];
		?>
		<?php $currentDepth = $data['Menu']['depth'] ?>
		<?php $this->BcBaser->element('menus/index_row', array('datas' => $datas, 'data' => $data, 'count' => ($key + 1), 'rowClassies' => $rowClassies)) ?>
	<?php endforeach; ?>
<?php else: ?>
<?php endif ?>