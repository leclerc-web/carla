<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb ;
global $current_user;
get_currentuserinfo();
?>

<body>

<div class="container">

	<div class="row">
		<div class="col-lg-12">
			<div class="jumbotron">
				<h1 class="text-center">Carla Plugin</h1>
			</div>
		</div>			
	</div>
	<!-- GREEN BUTTON -->
	<div class="row">		
		<div class="col-lg-4  col-md-4  col-sm-4  col-xs-12">
			<div class="card text-white bg-primary mb-3">
				<div class="card-body">
					<h4 class="card-title"><?php echo __('Secure','carla');?></h4>
					<p class="card-text"><?php echo __('Secure sensitive files / directories.','carla');?></p>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
			<div class="card text-white bg-success mb-3">
				<div class="card-body">
					<h4 class="card-title"><?php echo __('optimization','carla');?></h4>
					<p class="card-text"><?php echo __('Optimization of server response time.','carla');?></p>
				</div>
			</div>
		</div>

		<div class="col-lg-4 col-md-4 col-sm-4  col-xs-12">
			<div class="card text-white bg-danger mb-3">
				<div class="card-body">
					<h4 class="card-title"><?php echo __('Location','carla');?></h4>
					<p class="card-text"><?php echo __('IP registration of visitors and geolocation.','carla');?></p>
				</div>
			</div>
		</div>
	<!-- END ROW GREEN BUTTON -->
	</div>
		                
<!-- -------------------------------------------------------------------------------------------- CARD ---------------------------------------------------- -->
	<div class="row">
		<div class="col-sm-offset-1 col-sm-5">
			<div class="card">
				<div class="card-block">
					<h3 class="card-title"><?php echo __('Deleting the version number.','carla');?></h3>
					<p class="card-text"><?php echo __('Deleting the Wordpress version number can slow down, see block a hacker. It will no longer be able to target the potential flaws of a specific version.','carla');?></p>
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#wp_version"><?php echo __('Before','carla');?></button>
				</div>
			</div>
		</div>
		<div class="col-sm-5">
			<div class="card">
				<div class="card-block">
					<h3 class="card-title"><?php echo __('Stop enumeration.','carla');?></h3>
					<p class="card-text"><?php echo __('Many tools exist for enumerate a wordpress site and know the names of the users registered on it. It will no longer be possible to obtain results via a scanner.','carla');?></p>
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#Enumeration_before"><?php echo __('Before','carla');?></button>
					<button type="button" class="btn btn-success" data-toggle="modal" data-target="#Enumeration_after"><?php echo __('Now','carla');?></button>			
				</div>
			</div>
		</div>
	<!-- END ROW -->
	</div>

	<div class="row">
		<div class="col-sm-offset-3 col-sm-6 col-sm-offset-3">
			<div class="card">
				<div class="card-block">
					<h3 class="card-title"><?php echo __('Modification of the login error message.','carla');?></h3>
					<p class="card-text"><?php echo __('In the event of an attack by brute force, the hacker will have no clues as to the validity of the username tested during the attack, the message will indicate only a connection error.','carla');?></p>
					<button type="button" class="btn btn-danger" data-toggle="modal" data-target="#login_before"><?php echo __('Before','carla');?></button>
					<button type="button" class="btn btn-success" data-toggle="modal" data-target="#login_after"><?php echo __('Now','carla');?></button>	
				</div>
			</div>
		</div>
	<!-- END ROW -->
	</div>

<!-- ---------------------------------- MODAL CARD ---------------------------------- -->

<!-- ------------ VERSION------------ -->
	<div id="wp_version" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="dimension">
				<div class="modal-content">
					<div class="modal-body">
						<img src="<?php echo esc_url( plugins_url( '/img/wp_version.png', __FILE__ ) ); ?>" class="img-responsive">
					</div>
				</div>
			</div>
		</div>
	</div>

<!-- ------------ ENUMERATION ------ -->
	<div id="Enumeration_before" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="dimension">
				<div class="modal-content">
					<div class="modal-body">
						<img src="<?php echo esc_url( plugins_url( '/img/Enumeration_before.png', __FILE__ ) ); ?>" class="img-responsive">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="Enumeration_after" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="dimension">
				<div class="modal-content">
					<div class="modal-body">
						<img src="<?php echo esc_url( plugins_url( '/img/Enumeration_after.png', __FILE__ ) ); ?>" class="img-responsive">
					</div>
				</div>
			</div>
		</div>
	</div>

<!-- --------- LOGIN ERROR ------ -->
	<div id="login_before" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="dimension">
				<div class="modal-content">
					<div class="modal-body">
						<img src="<?php echo esc_url( plugins_url( '/img/login_before.png', __FILE__ ) ); ?>" class="img-responsive">
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id="login_after" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="dimension">
				<div class="modal-content">
					<div class="modal-body">
						<img src="<?php echo esc_url( plugins_url( '/img/login_after.png', __FILE__ ) ); ?>" class="img-responsive">
					</div>
				</div>
			</div>
		</div>
	</div>
<!-- -------------------------------------------------------------------------------------------- END CARD ---------------------------------------------------- -->


	<div class="marge_button">
		<a tabindex="0" class="btn btn-lg btn-success popover-dismiss pull-right" role="button" data-placement="top" data-toggle="popover" data-trigger="focus" title="<?php echo __('Help the creator','carla');?>" data-content="<?php echo __('By doing a DON, you participate in the improvement of the extension as well as the security of your site, think there ;-)','carla');?>"><?php echo __('Information','carla');?></a>
		<br />
		<br />
		<br />
		<a class="btn btn-lg btn-primary pull-right" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=WQD4PC6WKHPBA" type="button" class="btn btn-primary"><?php echo __('Make a donation','carla');?></a>
	</div>
	
</div>

<script>
	jQuery(document).ready(function( $ ) {	
		$('.popover-dismiss').popover({
		trigger: 'focus'
		})
	});
</script>

</body>