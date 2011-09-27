<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>achilles: form validation</title>
</head>

<body>

<?php echo form_open('example/form', array('id'=>'nametag', 'class' => 'achilles-able')); ?>

<label for="name">Your name, please. <?php if( form_error('name') ) echo 'ERROR'; ?></label><br />
<input type="text" id="name" name="name" value="<?php echo set_value('name'); ?>" /><br />

<input type="submit" value="Grab a nametag" />

</form>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>sparks/achilles/0.0.1/js/achilles.js"></script>
</body>
</html>
