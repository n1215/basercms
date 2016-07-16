<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			SingleBlog.View
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */
?>


<?php echo $this->BcForm->create() ?>

<?php $this->BcBaser->element('admin/SingleBlogPosts/form') ?>

    <div class="submit">
        <?php echo $this->BcForm->button('保存', array('class' => 'button')) ?>
    </div>

<?php echo $this->BcForm->end() ?>