<?php
$user = $this->session->userdata('user');
?>
<!DOCTYPE html>
<!--[if IE 8]>         <html class="no-js lt-ie9" lang="en"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js" lang="en"> <!--<![endif]-->

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width" />
  <title><?= $title.' : '.$this->config->item('app_name') ?></title>
  
  <link rel="shortcut icon" href="<?=base_url()?>assets/images/favicon.ico" type="image/x-icon">

  <link rel="stylesheet" href="<?=base_url()?>assets/foundation/css/normalize.css" />
  <link rel="stylesheet" href="<?=base_url()?>assets/foundation/css/app.css" />
  <link rel="stylesheet" href="<?=base_url()?>assets/foundation/css/foundation.min.css" />
  <?=$_styles?>

  <script src="<?=base_url()?>assets/foundation/js/vendor/custom.modernizr.js"></script>
  
  <?=$_scripts?>

</head>
<body>

  <!-- body content here -->
	
	<? /*echo serialize(array('billing', 'posts'))*/ ?>
	
	<div class="sticky">
	    <nav class="top-bar">
	      <ul class="title-area">
	        <!-- Title Area -->
	        <li class="name">
	          <h1><a href="/" title="<?=lang('dashboard.backdashboard')?>"></a></h1>
	        </li>
	        <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
	        <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
	      </ul>
	
		 <!-- Right Nav Section -->
	      <section class="top-bar-section">
	        <ul class="right">

			  <? if(check_feature('pqr')): ?>
	          <li class="divider"></li>
	          <li><?=anchor('pqr',lang('dashboard.pqr'))?></li>
	          <? endif; ?>

			  <? if(check_feature('config')): ?>
	          <li class="divider"></li>
	          <li><?=anchor('config',lang('dashboard.config'))?></li>
	          <? endif; ?>

			  <? if(check_feature('users')): ?>
	          <li class="divider"></li>
	          <li><?=anchor('users',lang('dashboard.users'))?></li>
	          <? endif; ?>

	          <? if(check_feature('billing')): ?>
	          <li class="divider"></li>
	          <li><?=anchor('billing',lang('dashboard.billing'))?></li>
	          <? endif; ?>
			  
			  <? if(check_feature('posts')): ?>
	          <li class="divider"></li>
	          <li><?=anchor('posts',lang('dashboard.posts'))?></li>
	          <? endif; ?>
	          
	          <li class="divider"></li>
	          <li class="has-form">
	            <a href="<?=site_url('logout')?>" class="button"><?=lang('dashboard.logout')?></a>
	          </li>
	        </ul>
	      </section>
	    </nav>
	</div>
	
	<!-- Subheader -->
	<div class="row full-width">
		<div class="large-12 columns">
			<h6 class="docs float-right">
				<?=lang('dashboard.hello')?> <?=$user->name?>!
			</h6>
			<h7 id="date" class="docs float-left"></h7>
		</div>
	</div>
	
	<!-- Content -->
	<?=$content?>
	<!-- End Content -->
	
	<!-- Footer -->
	<!-- <div id="copyright">
		<div class="row full-width">
			<div class="large-4 columns">
				<p><?=$this->config->item('app_name');?> © 2013 sector78.com</p>
			</div>
			<div class="large-8 columns">
				<ul class="inline-list right">
			</div>
		</div>
	</div> -->
  <!-- END body content here -->


  <script>
  document.write('<script src=' +
  ('__proto__' in {} ? '<?=base_url()?>assets/foundation/js/vendor/zepto' : 'js/vendor/jquery') +
  '.js><\/script>')
  </script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.alerts.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.clearing.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.cookie.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.dropdown.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.forms.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.joyride.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.magellan.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.orbit.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.placeholder.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.reveal.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.section.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.tooltips.js"></script>
  <script src="<?=base_url()?>assets/foundation/js/foundation/foundation.topbar.js"></script>
  <script>
  $(document).foundation();
  
  $(document).ready(function(){  
  	$('#send-form').click(function(e){
  		e.preventDefault();
  		$('#login-form').submit();
  	});
  	
	var monthNames = [<?=lang('dashboard.months_names')?>];
	var dayNames= [<?=lang('dashboard.weekdays_names')?>]
	
	var newDate = new Date();
	$('#date').html(dayNames[newDate.getDay()] + ", " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());
  	
  });
  </script>
</body>
</html>