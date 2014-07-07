<?php if($datas): ?>
<ul>
<?php foreach($datas as $data): ?>
	<?php 
		$current = '';
		if('/' . $this->request->url == $data['Menu']['link']) {
			$current = ' current';
		}
	?>
	<?php if($data['Menu']['menu_type'] == 1): ?>
	<li class="menu-item menu-contents menu-depth-<?php echo $data['Menu']['depth'] ?><?php echo $current ?>">
		<?php $this->BcBaser->link($data['Menu']['name'], $data['Menu']['link']) ?>
	<?php elseif($data['Menu']['menu_type'] == 2): ?>
	<li class="menu-item menu-folder menu-depth-<?php echo $data['Menu']['depth'] ?><?php echo $current ?>">
		<?php echo $data['Menu']['name'] ?>
		<?php if($data['Menu']['children']): ?>
		<?php $this->BcBaser->element('menu', array('datas' => $data['Menu']['children'])) ?>
		<?php endif ?>
	</li>
	<?php elseif($data['Menu']['menu_type'] == 3): ?>
	<li class="menu-item menu-link menu-depth-<?php echo $data['Menu']['depth'] ?><?php echo $current ?>">
		
	</li>		
	<?php endif ?>
<?php endforeach ?>
</ul>
<?php endif ?>