<script type="text/javascript">
	jQuery(document).ready(function () {
		window.fbAsyncInit = function () {
			FB.init({appId: '<?php echo sfConfig::get('app_facebook_api_id') ?>', status: false, cookie: true, xfbml: true});
			FB.Event.subscribe('auth.login', function (response) {
				window.location = "<?php echo url_for('default', array('module' => 'sfGuardAuth', 'action' => 'facebookLogin')) ?>";
			});
			FB.Event.subscribe('auth.logout', function (response) {
				window.location = "<?php echo url_for('@homepage') ?>";
			});
		};
		(function () {
			var e = document.createElement('script');
			e.type = 'text/javascript';
			e.src = 'http://connect.facebook.net/es_LA/all.js';
			e.async = true;
			document.getElementById('fb-root').appendChild(e);
		}());
	});
</script>