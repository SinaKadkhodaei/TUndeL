<?php

require_once(__DIR__ . '/../__Bootstrap/__Bases/__FirstConfigs.php');

(new __App\__Routes)->make();
__Bootstrap\__Routes::run(CurrentRoute);
