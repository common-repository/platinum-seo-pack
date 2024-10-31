<?php
/*
Plugin Name: Platinum SEO Pack
Plugin URI: https://techblissonline.com/platinum-wordpress-seo-plugin/
Author: Rajesh - Techblissonline
Author URI: http://techblissonline.com/
*/ 
?>
<?php  
	wp_enqueue_script( 'psp-ajax-wizard-script', plugins_url( 'settings/js/psp_wizard.js', PSP_PLUGIN_SETTINGS_URL ), array('jquery'), '2.2.1', false );
	wp_enqueue_style("psp-settings-bs-css", plugins_url( 'settings/css/psp-settings-bs.css', PSP_PLUGIN_SETTINGS_URL ));
 ?>
 
<style>
 body {
	background: cyan;
	font-family: 'Roboto Condensed', sans-serif;
}

.container {
	position: fixed;
	top: 0px;
	left: 0px;
	width: 100%;
	height: 100%;
	z-index: 0;
	background: -webkit-radial-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3) 35%, rgba(0, 0, 0, 0.7));
	background: -moz-radial-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3) 35%, rgba(0, 0, 0, 0.7));
	background: -ms-radial-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3) 35%, rgba(0, 0, 0, 0.7));
	background: radial-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3) 35%, rgba(0, 0, 0, 0.7));
}

/**
.container .psp-bs .psp-start-btn {	
	position: fixed;
	top: 50%;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 1000;
	background: -webkit-radial-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3) 35%, rgba(0, 0, 0, 0.7));
	background: -moz-radial-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3) 35%, rgba(0, 0, 0, 0.7));
	background: -ms-radial-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3) 35%, rgba(0, 0, 0, 0.7));
	background: radial-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.3) 35%, rgba(0, 0, 0, 0.7));
}
**/

.container .psp-bs {
    background-color: transparent !important;
}

.btn-purple {
	background-color: purple !important;
	color: white !important;
}

.content {
	position: absolute;
	width: 100%;
	height: 100%;
	left: 0px;
	top: 0px;
	z-index: 1000;
}

.container h2 {
	position: absolute;
	top: 50%;
	line-height: 100px;
	height: 100px;
	margin-top: -50px;
	font-size: 30px;
	width: 100%;
	text-align: center;
	color: transparent;
	animation: blurFadeInOut 3s ease-in backwards;
}
.container h2.frame-1 {
	animation-delay: 0s;
}
.container h2.frame-2 {
	animation-delay: 2.5s;
}
.container h2.frame-3 {
	animation-delay: 5s;
	animation: blurFadeIn 1s ease-in 5s backwards;
}
.container h2.frame-4 {
	font-size: 200px;
	animation-delay: 7.5s;
}
.container h2.frame-5 {
	animation: none;
	color: transparent;
	text-shadow: 0px 0px 1px #fff;
}
.container h2.frame-5 span {
	animation: blurFadeIn 3s ease-in 12s backwards;
	color: transparent;
	text-shadow: 0px 0px 1px #fff;
}
.container h2.frame-5 span:nth-child(2) {
	animation-delay: 13s;
}
.container h2.frame-5 span:nth-child(3) {
	animation-delay: 14s;
}

a.btn-purple:before {
  display: inline-block;
  content: "\00d7"; /* This will render the 'X' */
  font-size: 30px;
}

@keyframes blurFadeInOut{
	0%{
		opacity: 0;
		text-shadow: 0px 0px 40px #fff;
		transform: scale(0.9);
	}
	20%,75%{
		opacity: 1;
		text-shadow: 0px 0px 1px #fff;
		transform: scale(1);
	}
	100%{
		opacity: 0;
		text-shadow: 0px 0px 50px #fff;
		transform: scale(0);
	}
}
@keyframes blurFadeIn{
	0%{
		opacity: 0;
		text-shadow: 0px 0px 40px #fff;
		transform: scale(1.3);
	}
	50%{
		opacity: 0.5;
		text-shadow: 0px 0px 10px #fff;
		transform: scale(1.1);
	}
	100%{
		opacity: 1;
		text-shadow: 0px 0px 1px #fff;
		transform: scale(1);
	}
}
@keyframes fadeInBack{
	0%{
		opacity: 0;
		transform: scale(0);
	}
	50%{
		opacity: 0.4;
		transform: scale(2);
	}
	100%{
		opacity: 0.2;
		transform: scale(5);
	}
}
</style>
<!------ Include the above in your HEAD tag ---------->
<div class="container">
	<div class="content">
		<h2 class="frame-1"><?php esc_html_e('Congragulations!!!', 'platinum-seo-pack') ?></h2>
		<h2 class="frame-2"><?php esc_html_e('You are done setting up the essential options in Platinum SEO', 'platinum-seo-pack') ?> </h2>
		<h2 class="frame-3">
		<div class="psp-bs">
			<div class="psp-start-btn">
				<a href="<?php echo get_admin_url(get_current_blog_id())."admin.php?page=platinum-seo-social-pack-by-techblissonline" ?>" class="btn btn-lg btn-purple" role="button"> <?php esc_html_e('Close this wizard and Go to Dashboard', 'platinum-seo-pack') ?></a>	
			</div>
		</div>
		</h2>
	</div>
</div>