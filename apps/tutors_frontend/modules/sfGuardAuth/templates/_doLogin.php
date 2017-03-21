<?php if (!isset($referer)): ?>
    <script type="text/javascript">

        function ajaxSubmit(form, div) {
            jQuery.ajax({
                type: "POST",
                url: form.attr('action'),
                data: form.serialize(),
                success: function(data) {
                    jQuery(div).html(data);
                },
                beforeSend: function() {
                    jQuery('#loading').css('display', 'block');
                },
                complete: function() {
                    jQuery('#loading').css('display', 'none');
                }
            });

        }

        

        jQuery(document).ready(function() {
            jQuery('.fb-auth-login').click(function() {
                FB.login(function(response) {
                    if (response.authResponse) {
                        window.location = "<?php echo url_for('@facebook_login') ?>";
                    }
                }, {scope: 'email', cookie: true});
            });
            jQuery('.login-form').submit(function(e) {
                e.preventDefault();
                var form = jQuery(this).closest('form');
                ajaxSubmit(form, form.parent().parent());
            });
        });
    </script>
    <div class="standalone-loginbox">
        <form class="login-form" action="<?php echo url_for('@sf_guard_signin'); ?>" method="post">
            <?php echo $form; ?>



            <div class="actions">
                <button class="btn btn-block" type="submit"> <?php echo __('Sign in') ?></button>
                <a class="fb-auth-login flat-btn flat-btn-facebook" href="#"> <i class="icon-facebook"> | </i>  Facebook Login</a>
                <!-- <a class="btn btn-primary" href="<?php //echo $openid->authUrl()  ?>">Google Login</a> -->
            </div>
        </form>
    </div>
<?php else: ?>
    <script type="text/javascript">
        window.location = "<?php echo $referer ?>";
    </script>
<?php endif; ?>

