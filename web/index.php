<?php

require '../bootstrap.php';
require '../MiniBlogApplication.php';

// エントリポイント
$app = new MiniBlogApplication(false);
$app->run();