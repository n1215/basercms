<?php
if (!isset($channel)) {
	$channel = array();
}

echo $this->Rss->document(
	$this->Rss->channel(
		array(),
		$this->Blog->transformRssChannel($channel),
		$this->fetch('content')
	)
);