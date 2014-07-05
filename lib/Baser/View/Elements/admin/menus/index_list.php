<?php
/**
 * [ADMIN] グローバルメニュー一覧
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
<th>メニュー名</th>
<th>登録日<br />更新日</th>
</tr>
</thead>
<tbody>
	<?php if (!empty($listDatas)): ?>
		<?php foreach ($listDatas as $data): ?>
			<?php $this->BcBaser->element('menus/index_row', array('data' => $data)) ?>
		<?php endforeach; ?>
	<?php else: ?>
		<tr>
			<td colspan="3"><p class="no-data">データが見つかりませんでした。</p></td>
		</tr>
	<?php endif; ?>
</tbody>
</table>
