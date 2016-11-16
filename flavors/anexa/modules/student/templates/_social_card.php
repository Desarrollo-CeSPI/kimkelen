<h2><?php echo __('Social card') ?> </h2>
<div class="sf_admin_form_row sf_admin_Text sf_admin_form_field_full_name">
	<div>
		<a href="<?php echo url_for('student/printSocialCard?id='. $student->getId()) ?>"><?php echo __('Print social card') ?></a>

	</div>
	<div style="margin-top: 1px; clear: both;"></div>
</div>
