<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			MultiPage.View
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php echo $this->BcForm->create() ?>
<?php echo $this->BcForm->hidden('id') ?>

<?php echo $this->BcBaser->element('admin/form') ?>

<div class="submit">
	<?php echo $this->BcForm->submit('保存', array('class' => 'button', 'div' => false)) ?>
</div>

<?php echo $this->BcForm->end() ?>
