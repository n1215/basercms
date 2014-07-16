<?php
/**
 * [ADMIN] メニューフォーム
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
$statuses = array(0 => '非公開', 1 => '公開中');
?>


<script type="text/javascript">
$(window).load(function() {
	$("#MenuName").focus();
});

$(function(){

	$("input[name='data[Menu][menu_type]']").click(function(){
		initForm($(this).val());
	});
	
	var menuType = $("input[name='data[Menu][menu_type]']:checked").val();
	if(!menuType) {
		menuType = $("#MenuMenuType").val();
	}
	initForm(menuType);
	
	function initForm(menuType) {
		switch (menuType) {
			case "1":	// コンテンツ
				$("#RowStatus").show();
				$("#RowLinkUrl").show();
				$("#MenuLink").attr('readonly', 'readonly').css('background-color', '#eee');
				break;
			case "2":	// フォルダ
				$("#RowStatus").hide();
				$("#RowLinkUrl").hide();
				break;
			case "3":	// リンク
				$("#RowStatus").hide();
				$("#RowLinkUrl").show();
				$("#MenuLink").removeAttr('readonly').css('background-color', '#FFF');
				break;
		}
	}
});
</script>


<?php echo $this->BcForm->create('Menu') ?>
<?php echo $this->BcForm->input('Menu.id', array('type' => 'hidden')) ?>
<?php echo $this->BcForm->input('Menu.status', array('type' => 'hidden')) ?>

<div class="section">
	<table cellpadding="0" cellspacing="0" id="FormTable" class="form-table">
		<?php if ($this->request->action == 'admin_edit'): ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Menu.id', 'NO') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->value('Menu.id') ?>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Menu.parent_id', '親フォルダ') ?>&nbsp</th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Menu.parent_id', array('type' => 'select', 'options' => $parents, 'empty' => 'なし')) ?>
				<?php echo $this->BcForm->error('Menu.parent_id') ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Menu.parent_id', 'タイプ') ?>&nbsp</th>
			<td class="col-input">
<?php if($this->request->action == 'admin_add'): ?>
				<?php echo $this->BcForm->input('Menu.menu_type', array('type' => 'radio', 'options' => $menuTypes)) ?>
				<?php echo $this->BcForm->error('Menu.munu_type') ?>
<?php elseif($this->request->action == 'admin_edit'): ?>
				<?php echo $menuTypes[$this->BcForm->value('Menu.menu_type')] ?>
				<?php echo $this->BcForm->input('Menu.menu_type', array('type' => 'hidden')) ?>
<?php endif ?>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Menu.name', '名称') ?>&nbsp;<span class="required">*</span></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Menu.name', array('type' => 'text', 'size' => 40, 'maxlength' => 20)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpName', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<div id="helptextName" class="helptext">
					<ul>
						<li>日本語が利用できます。</li>
						<li>識別しやすくわかりやすい名前を入力します。</li>
					</ul>
				</div>
				<?php echo $this->BcForm->error('Menu.name') ?>
			</td>
		</tr>
		<tr id="RowLinkUrl">
			<th class="col-head"><?php echo $this->BcForm->label('Menu.link', 'リンクURL') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Menu.link', array('type' => 'text', 'size' => 40, 'maxlength' => 255)) ?>
				<?php echo $this->Html->image('admin/icn_help.png', array('id' => 'helpLink', 'class' => 'btn help', 'alt' => 'ヘルプ')) ?>
				<?php echo $this->BcForm->error('Menu.link') ?>
				<div id="helptextLink" class="helptext"> 内部リンクの場合は、先頭にスラッシュつけたルートパスで入力してください。<br />
					(例) /admin/global/index </div>
			</td>
		</tr>
		<tr>
			<th class="col-head"><?php echo $this->BcForm->label('Menu.enabled', '利用状態') ?></th>
			<td class="col-input">
				<?php echo $this->BcForm->input('Menu.enabled', array('type' => 'radio', 'options' => $this->BcText->booleanDoList('利用'))) ?>
				<?php echo $this->BcForm->error('Menu.enabled') ?>
			</td>
		</tr>
<?php if($this->request->action == 'admin_edit' && $this->BcForm->value('Menu.menu_type') == 1): ?>
		<tr id="RowStatus">
			<th class="col-head"><?php echo $this->BcForm->label('Menu.status', '公開状態') ?></th>
			<td class="col-input">
				<?php echo $statuses[$this->request->data['Menu']['status']] ?>　
				<?php if($this->request->data['Menu']['publish_begin'] || $this->request->data['Menu']['publish_end']): ?>
				（　<?php echo $this->BcTime->format('Y/m/d', $this->request->data['Menu']['publish_begin']) ?>
				　〜　
				<?php echo $this->BcTime->format('Y/m/d', $this->request->data['Menu']['publish_end']) ?>　）
				<?php endif ?>
			</td>
		</tr>
<?php endif ?>
	</table>
</div>
<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('div' => false, 'class' => 'button', 'id' => 'BtnSave')) ?>
	<?php if ($this->action == 'admin_edit'): ?>
		<?php if ($this->BcForm->value('Menu.edit_link')): ?>
			<?php $this->BcBaser->link('編集', $this->BcForm->value('Menu.edit_link'), array('class' => 'button')); ?>
		<?php endif; ?>
		<?php $this->BcBaser->link('削除', array('action' => 'delete', $this->BcForm->value('Menu.id')), array('class' => 'button'), sprintf('%s を本当に削除してもいいですか？', $this->BcForm->value('Menu.name')), false); ?>
	<?php endif; ?>
</div>

<?php echo $this->BcForm->end() ?>