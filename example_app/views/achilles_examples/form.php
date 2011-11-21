<style>
span.error {
	color:#900;
	padding-left: 0.3em;
}
</style>
    <div class="container">
      <div class="content">
				<h1>Demo: Login form</h1>

<?php echo achilles_form_open('achilles_examples/form', array('id'=>'nametag','class'=>'dark')); ?>

<input type="hidden" name="submitted" value="yup" />

<div class="input">

<div class="message"><?php if( isset( $message) ) echo $message; ?></div>

<?php if( !isset( $success ) || !$success ): ?>

<div class="clearfix">
	<label for="username">Username<?php if( $f = form_error('username') ) echo '<span class="error">'.$f.'</span>'; ?></label>
	<div class="input">
		<input type="text" id="username" name="username" <?php if( $f ) echo 'class="error"'; ?> value="<?php echo set_value('username'); ?>" />
	</div>
</div>

<div class="clearfix">
	<label for="password">Password<?php if( $f = form_error('password') ) echo '<span class="error">'.$f.'</span>'; ?></label>
	<div class="input">
		<input type="password" id="password" name="password" <?php if( $f ) echo 'class="error"'; ?> value="" />
	</div>
</div>

	<div class="actions">
		<input type="submit" class="btn primary submit" value="Login" />
	</div>

	<p><em>Hint: try using <strong>demo</strong> as your username and <strong>achilles</strong> as your password.</em></p>
	
<?php endif; // check success ?>
	
</div><!--.input-->

</form>