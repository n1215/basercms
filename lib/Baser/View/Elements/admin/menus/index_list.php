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


<table cellpadding="0" cellspacing="0" class="list-table sort-table" id="ListTable">
<thead>
	<tr>
		<th style="width:170px" class="list-tool">
			<div>
				<?php $this->BcBaser->link($this->BcBaser->getImg('admin/btn_add.png', array('width' => 69, 'height' => 18, 'alt' => '新規追加', 'class' => 'btn')), array('action' => 'add')) ?>
			</div>
		</th>
		<th>NO</th>
		<th>メニュー名</th>
		<th>公開状態</th>
	</tr>
</thead>
<tbody>
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
			foreach ($rowIdTmps as $rowIdTmp) {
				$rowClassies[] = 'row-group-' . $rowIdTmp;
			}
			$rowClassies[] = 'depth-' . $data['Menu']['depth'];
			?>
			<?php $currentDepth = $data['Menu']['depth'] ?>
			<?php $this->BcBaser->element('menus/index_row', array('datas' => $datas, 'data' => $data, 'count' => ($key + 1), 'rowClassies' => $rowClassies)) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="4"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>
