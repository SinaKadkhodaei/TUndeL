<html>

<head>
	<link rel="stylesheet" href="<?php e(url('test.css')); ?>">
	<?php metaCsrf(); ?>
</head>

<body>
	<div class="test">
		<?php loadSection('UrlAddress'); ?>
	</div>
	<div class="test">
		<?php loadSection('ID'); ?>
	</div>
</body>

</html>