
<?php startSection('UrlAddress'); { ?>
	<h4>URL Address : </h4><?php e(CurrentUrl); ?>
<?php endSection();
} ?>
<?php startSection('ID'); { ?>
	<h4>User ID : </h4><?php e($username); ?>
<?php endSection();
} ?>
<?php loadView('_layout'); ?>
