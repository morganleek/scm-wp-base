<!doctype html>
<html <?php language_attributes(); ?> class="no-js">
	<head>
		<meta charset="<?php bloginfo('charset'); ?>">
		<title><?php wp_title(''); ?><?php if(wp_title('', false)) { print ' : '; } ?><?php bloginfo('name'); ?></title>

		<link href="//www.google-analytics.com" rel="dns-prefetch">
		<link href="<?php print get_template_directory_uri(); ?>/favicon.ico?v=1.0.0" rel="shortcut icon">

		<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
		<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
		<meta name="description" content="<?php bloginfo('description'); ?>">

    <?php wp_head(); ?>
  </head>

  <body <?php body_class(); ?>>

  	<!-- Header -->
  	<header class="header" role="banner">
  		<div class="logo">
				<a href="<?php echo home_url(); ?>">
					<img src="" alt="Logo">
				</a>
			</div>

			<nav class="nav">
				<?php scm_nav(array('theme_location' => 'header-menu')); ?>
			</nav>
		</header>
		<!-- /Header -->
