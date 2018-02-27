<?php

use \Yagamii\Page;

$app->get('/', function() {

    $page = new Page();

    $page->setTpl("index");

});

 ?>
