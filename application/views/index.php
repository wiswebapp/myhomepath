<!DOCTYPE html>
<html>
<head>
	<title>CPBox Homepage</title>
</head>
<body>

<?php
	
	$urlAdmin = base_url('adminw/login');
	header("Location: ".$urlAdmin);
	exit;

 ?>

<p>Visit <a href="<?=base_url('adminw/login')?>?access=demo@demo.com">Admin Panel Now</a></p>
</body>
</html>