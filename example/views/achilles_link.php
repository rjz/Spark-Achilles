<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>achilles: basic linking</title>
</head>

<body>

	<div class="achilles-message">Ground control to Major Tom?</div>

	<p><a href="<?php echo site_url('example/link'); ?>" class="achilles-able">Phone home</a></p>

	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script src="<?php echo base_url(); ?>sparks/achilles/0.0.1/js/achilles.js"></script>
	<script src="<?php echo base_url(); ?>sparks/achilles/0.0.1/js/achilles.lib.js"></script>

</body>
</html>