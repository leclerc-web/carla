 <?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb ;
global $current_user;
get_currentuserinfo();


$htaccess = sanitize_text_field($_POST['htaccess']);
$content_access = file_get_contents('../.htaccess');
$htaccesschemin = get_home_path().".htaccess";
$wpconfigchemin = get_home_path()."wp-config.php";

// CHECK MAJ
$update_data = wp_get_update_data();
$number_maj  = number_format_i18n( $update_data['counts']['total'] ); 

// CHECK SSL
$SSL_cert = explode(":",site_url());


/* ---------------------------------------------------------------------------------- */	
$wpconf_code = "\n" .'<files wp-config.php>'   . "\n" . 
					  'order allow,deny'       . "\n" . 
					  'deny from all'          . "\n" . 
				    '</files>'			       . "\n\n" ;
			
$nav_code    = "\n" .'Options All -Indexes' . "\n\n";

$htaccess_code = "\n" .'<files ~ "^.*\.([Hh][Tt][Aa])">'. "\n" . 
						'order allow,deny' 			    . "\n" . 
						'deny from all'    			    . "\n" . 
						'satisfy all'      			    . "\n" . 
					  '</files>'		      			. "\n\n";
				 
$include_code = "\n" .'<IfModule mod_rewrite.c>'									. "\n" . 
						'RewriteEngine On'											. "\n" . 
						'RewriteBase /'												. "\n" . 
						'RewriteRule ^wp-admin/includes/ - [F,L]'					. "\n" . 
						'RewriteRule !^wp-includes/ - [S=3]'						. "\n" . 
						'RewriteRule ^wp-includes/[^/]+\.php$ - [F,L]'				. "\n" . 
						'RewriteRule ^wp-includes/js/tinymce/langs/.+\.php - [F,L]'	. "\n" . 
						'RewriteRule ^wp-includes/theme-compat/ - [F,L]'			. "\n" . 
					 '</IfModule>'													. "\n\n"	;		
/* ---------------------------------------------------------------------------------- */		
		

/* ---------------------------------- CHECK HTACCESS ------------------------------------------------ */	

$comparaison_conf     = stristr($content_access, $wpconf_code);
$comparaison_nav      = stristr($content_access, $nav_code);
$comparaison_htaccess = stristr($content_access, $htaccess_code);
$comparaison_include  = stristr($content_access, $include_code);

		
if($comparaison_conf == true && $comparaison_nav == true && $comparaison_htaccess == true && $comparaison_include == true && !file_exists($wpconfigchemin) && ($number_maj <= 0) && ($SSL_cert[0] == "https")) {
	$valids['total'] = '<div class="alert alert-success suppr">
						  <strong>'.__('Everything is secure !','carla').'</strong>
					   </div>';
}else{
	$errors['total'] = '<div class="alert alert-danger suppr">
							  <strong>'.__('Warning ! ','carla').'</strong>'.__('Everything is not secure !','carla').'</div>';
}


/* ---------------------------------- CONDITIONS FORMULAIRES ------------------------------------------------ */		
		
$securiser = $_POST['securiser'];	
		
if(isset($securiser)) {

/* ---------------------------------------------------------------------------------- */	
	
	if($htaccess == "wpconfig"){		
		
		if($comparaison_conf == false){
			insert_with_markers($htaccesschemin, "carla_conf", $wpconf_code);
			$valids['conf'] = '<div class="alert alert-success">
								  <strong>'.__('The wp-config file is now protected !','carla').'</strong>
							   </div>';
		}else{
			$errors['conf'] = '<div class="alert alert-danger">
							  <strong>'.__('Warning ! ','carla').'</strong>'.__('The wp-config file is already protected !','carla').'</div>';
		}
	}

/* ---------------------------------------------------------------------------------- */	

	elseif($htaccess == "navigation") {
		
		if($comparaison_nav == false){
			insert_with_markers($htaccesschemin, "carla_nav", $nav_code);
			$valids['nav'] = '<div class="alert alert-success">
								  <strong>'.__('It is now impossible for an individual to browse the directories of your server !','carla').'</strong>
							  </div>';  
		}else{
			$errors['nav'] = '<div class="alert alert-danger">
								<strong>'.__('Warning ! ','carla').'</strong>'.__('Navigation between directories is no longer possible !','carla').'</div>';
		}
	}

/* ---------------------------------------------------------------------------------- */	
	
	elseif($htaccess == "htaccess") {
		
		if($comparaison_htaccess == false){
			insert_with_markers($htaccesschemin, "carla_htaccess", $htaccess_code);
			$valids['htaccess'] = '<div class="alert alert-success">
									  <strong>'.__('Your .htaccess file is now protected !','carla').'</strong>
								  </div>';  
		}else{
			$errors['htaccess'] = '<div class="alert alert-danger">
									<strong>'.__('Warning ! ','carla').'</strong>'.__('Your .htaccess file is already protected','carla').'</div>';
		}
	}

/* ---------------------------------------------------------------------------------- */
	
	elseif($htaccess == "inclusion") {
		
		if($comparaison_include == false){
			insert_with_markers($htaccesschemin, "carla_includes", $include_code);
			$valids['include'] = '<div class="alert alert-success">
									  <strong>'.__('The include directory is protected !','carla').'</strong>
								  </div>'; 
		}else{
			$errors['include'] = '<div class="alert alert-danger">
									<strong>'.__('Warning ! ','carla').'</strong>'.__('The include directory is already protected !','carla').'</div>';
		}
	}

	
/* ---------------------------------------------------------------------------------- */
	
	elseif(empty($htaccess)) {
		/////////////////////////////////////SI VIDE 
	}
}
?>	

<!--  CHECK FA-CHECK -->
<div class="block">
	<div class="container">
		<div class="row">
		
			<div class=" col-sm-5">
				<div class="card">
					<div class="card-block">
						<button type="button"  class="btn btn-success center-block" onClick="window.location.reload()"><i class="fa fa-circle-o-notch fa-spin"></i> Mettre à jour</button><br>
					  <?php
						if($comparaison_conf == false){
							echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('Your wp-config file is not protected !','carla').'</div>';							  
						}else{
							echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('The wp-config file is now protected !','carla').'</div>';	
						}
						
						
						if($comparaison_nav == false){
							echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('Navigation in your server is possible !','carla').'</div>'; 
						}else{
							echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('It is now impossible for an individual to browse the directories of your server !','carla').'</div>';	
						}
						
					
						if($comparaison_htaccess == false){
							echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('Your .htaccess file is not protected !','carla').'</div>';
						}else{
							echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('Your .htaccess file is now protected !','carla').'</div>';	
						}
						
						
						if($comparaison_include == false){
							echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('The includes directory is not protected !','carla').'</div>';
						}else{
							echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('The include directory is protected !','carla').'</div>';	
						}	 
						
						
						if(file_exists($wpconfigchemin)) {
							echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('You can move your wp-config.php file one step below the root ! (ftp server)','carla').'</div>';
						}else{
							echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('The wp-config.php file is no longer accessible to hackers !','carla').'</div>';	
						}
						
						
						if($number_maj > 0) {
							echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('Your plugins/templates aren\'t all up to date.','carla').'</div>';
						}else{
							echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('Your plugins are all up to date !','carla').'</div>';	
						}
						
						
						if($SSL_cert[0] == "http") {
							echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('Your site can be secured with an SSL certificate (httpS) !','carla').'</div>';
						}else{
							echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('Your site is secure with the httpS protocol !','carla').'</div>';	
						}	
					 ?>
					</div>
				</div>
			</div>	 
	 
			<div class="col-sm-offset-1 col-sm-6">
				<div class="card">
					<div class="card-block">
						<h3 class="card-title"><?php echo __('Server security','carla');?></h3>
						<p class="card-text">
							<form method="POST" action="">
								<div class="selectform">
									<select name="htaccess">
										<option   value="wpconfig" <?php if($comparaison_conf == true ){
											echo 'disabled'; }?>><?php echo __('Protect the wp-config file','carla');?></option>
										<option   value="navigation" <?php if($comparaison_nav == true){
											echo 'disabled'; }?>><?php echo __('Prevent listing your directories','carla');?></option>
										<option   value="htaccess" <?php if($comparaison_htaccess == true){
											echo 'disabled'; }?>><?php echo __('Protect the .htaccess file','carla');?></option>
										<option   value="inclusion" <?php if($comparaison_include == true){
											echo 'disabled'; }?>><?php echo __('Block access to wp-includes','carla');?></option>
									</select>
								</div>
								<input  type="submit" name="securiser" value="<?php echo __('To secure !','carla');?>" <?php if($comparaison_conf == true && $comparaison_nav == true && $comparaison_htaccess == true && $comparaison_include == true ) {echo 'disabled';} ?> class="btn btn-primary marge_input"/>
						   </form>
						</p>
						<div class="row">
							<div class="col-sm-2">
								<div class="margebutton">
									<!-- Button trigger modal -->
									<button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#htaccess">
										<?php echo __('Read more','carla');?>
									</button>
								</div>
							</div>
							<div class="col-sm-offset-3 col-sm-7">
								<?php 
									if (is_array($errors)){
											foreach ($errors as $error){
												echo $error."<br />"; 
											}
									}
									if (is_array($valids)){
											foreach ($valids as $valid){
												echo $valid."<br />"; 
											}
									}
								?>
							</div>
						</div>
					</div> 
				</div>
			</div>
		<!-- END ROW -->
		</div>

<!--  ----------------------------------------------------------- MODAL ---------------------------------------------------------- -->
	<div class="modal fade" id="htaccess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h1 class="modal-title" id="myModalLabel"><?php echo __('Protection','carla');?></h1>
				</div>
					<div class="modal-body">
						<h2><?php echo __('File protection wp-config.php','carla');?></h2>
							<p><?php echo __('The file','carla');?> <span class="valids"><?php echo __('wp-config','carla');?></span> <?php echo __('contains the accesses to your database, it is therefore imperative to block access to it.','carla');?></p>
						<br />
						<h2><?php echo __('Prevent listing your directories','carla');?></h2>
							<p><?php echo __('By default under Apache, when there is no index page, what you see in your browser is the list of files and directories contained on your server.','carla');?>
							<br />
							<?php echo __('it will be impossible for an attacker to know the contents of your directories.','carla');?>
							</p>
						<br />
						<h2><?php echo __('Protecting the .htaccess file','carla');?></h2>
							<p><?php echo __('The file','carla');?> <span class="valids"><?php echo __('.htaccess','carla');?></span> <?php echo __('handles many things like protecting your server, so think about protecting it too.','carla');?>.</p>
							<br />
						<h2><?php echo __('Wp-includes directory protection','carla');?></h2>
							<p><?php echo __('Some files should not be accessed, so it is imperative to block access to visitors or attackers.','carla');?></p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close','carla');?></button>
					</div>
			</div>
		</div>
	</div>
<!--  ------------------------------------------------------------------------------------------------------------------------------>

	<!-- END CONTAINER + BLOCK -->
	  </div>
</div>