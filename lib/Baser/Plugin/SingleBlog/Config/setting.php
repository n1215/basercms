<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			SingleBlog.Config
 * @since			baserCMS v 3.1.0
 * @license			http://basercms.net/license/index.html
 */

$config['BcContents']['items']['SingleBlog'] = [
	'SingleBlog'	=> [
		'title'     => 'シングルブログ',
        'preview'   => true,
		'icon'	=> 'admin/icon_single_blog.png',
		'routes' => [
			'manage' => [
				'plugin'	=> 'single_blog',
				'controller'=> 'single_blog_posts',
				'action'	=> 'index'
			],
			'delete' => [
				'plugin'	=> 'single_blog',
				'controller'=> 'single_blog',
				'action'	=> 'delete'
			],
			'view' => [
				'plugin'	=> 'single_blog',
				'controller'=> 'single_blog',
				'action'	=> 'index'
			]
		]
	]
];
