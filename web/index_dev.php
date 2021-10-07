<?php

require '../bootstrap.php';
require '../MiniBlogApplication.php';

// 開発用エントリポイント
$app = new MiniBlogApplication(true);
$app->run();