<?php

require 'core/ClassLoader.php';

// オートローダに'core'ディレクトリと'models'ディレクトリを登録
$loader = new ClassLoader();
$loader->registerDir(dirname(__FILE__).'/core');
$loader->registerDir(dirname(__FILE__).'/models');
$loader->register();