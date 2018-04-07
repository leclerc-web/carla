 <?php
 if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb ;
global $current_user;
get_currentuserinfo();


$htaccess = sanitize_text_field($_POST['htaccess']);
$content_access = file_get_contents('../.htaccess');
$htaccesschemin = get_home_path().".htaccess";

/* ---------------------------------------------------------------------------------- */	
$gzip = "\n" .'AddOutputFilterByType DEFLATE text/plain'				 . "\n" .
			  'AddOutputFilterByType DEFLATE text/html'					 . "\n" .
			  'AddOutputFilterByType DEFLATE text/xml'					 . "\n" .
			  'AddOutputFilterByType DEFLATE text/css'					 . "\n" . 
			  'AddOutputFilterByType DEFLATE application/xml'			 . "\n" .
			  'AddOutputFilterByType DEFLATE application/xhtml+xml'	     . "\n" . 
			  'AddOutputFilterByType DEFLATE application/rss+xml'        . "\n" .
			  'AddOutputFilterByType DEFLATE application/javascript'     . "\n" .
			  'AddOutputFilterByType DEFLATE application/x-javascript'   . "\n" .
			  'SetOutputFilter DEFLATE'									 . "\n" .
			  
			  '<ifmodule mod_deflate.c>'																				. "\n" .
					'AddOutputFilterByType DEFLATE text/text text/plain text/html text/xml text/css text/javascript'	. "\n" .
					'AddOutputFilterByType DEFLATE application/xml'														. "\n" .
					'AddOutputFilterByType DEFLATE application/xhtml+xml'												. "\n" .
					'AddOutputFilterByType DEFLATE application/rss+xml'													. "\n" .
					'AddOutputFilterByType DEFLATE application/javascript'												. "\n" .
					'AddOutputFilterByType DEFLATE application/x-javascript'											. "\n" .
				'</IfModule>'																							. "\n\n" ;
			
$vitesse = "\n" .'SetEnv REGISTER_GLOBALS 0'																			. "\n" .
				 'SetEnv ZEND_OPTIMIZER 1'																				. "\n" .
				 'SetEnv MAGIC_QUOTES 0'																				. "\n" .
				 'SetEnv PHP_VER 5'																						. "\n\n" ;
				 
				 
$cache  = "\n" .'<IfModule mod_expires.c>'											. "\n" .
					'ExpiresActive on'													. "\n" .
					'ExpiresDefault "access plus 1 month"'								. "\n" .
					'ExpiresByType image/x-icon "access plus 1 year"'					. "\n" .
					'ExpiresByType image/gif "access plus 1 month"'						. "\n" .
					'ExpiresByType image/png "access plus 1 month"'						. "\n" .
					'ExpiresByType image/jpg "access plus 1 month"'						. "\n" .
					'ExpiresByType image/jpeg "access plus 1 month"'					. "\n" .
					'ExpiresByType video/mp4 "access plus 1 month"'						. "\n" .
					'ExpiresByType text/x-component "access plus 1 month"'				. "\n" .
					'ExpiresByType font/truetype "access plus 1 month"'					. "\n" .
					'ExpiresByType font/opentype "access plus 1 month"'					. "\n" .
					'ExpiresByType application/x-font-woff "access plus 1 month"'		. "\n" .
					'ExpiresByType application/vnd.ms-fontobject "access plus 1 month"' . "\n" .
					'ExpiresByType text/css "access plus 1 year"'						. "\n" .
					'ExpiresByType application/javascript "access plus 1 year"'			. "\n" .
					'ExpiresByType text/javascript "access plus 1 year"'				. "\n" .
					'ExpiresByType text/js "access plus 1 year"'						. "\n" .
					'ExpiresByType application/x-javascript "access plus 1 year"'		. "\n" .
				  '</IfModule>'															. "\n\n" .
				  				  
					'<IfModule mod_headers.c>'. "\n" .
						'<FilesMatch "\.(ico|ttf|tpl|otf|jpg|jpeg|png|gif|js|css|xml|woff)$">'	. "\n" .
							'Header set Cache-Control "max-age=2592000, private"'				. "\n" .
						 '</FilesMatch>'														. "\n" .
						'<FilesMatch "\\.(ico|jpe?g|JPE?G|png|gif|swf|css|gz)$">'				. "\n" .
							'Header set Cache-Control "max-age=2592000, public"'				. "\n" .
						 '</FilesMatch>'														. "\n" .
						'<FilesMatch "\\.(js)$">'												. "\n" .
							'Header set Cache-Control "max-age=2592000, private"'				. "\n" .
						'</FilesMatch>'															. "\n" .
						'<filesMatch "\\.(html|htm)$">'											. "\n" .
							'Header set Cache-Control "max-age=7200, public"'					. "\n" .
						'</filesMatch>'															. "\n" .
					'</IfModule>'																. "\n\n" ;
				 
		
/* ---------------------------------- CHECK HTACCESS ------------------------------------------------ */	

$comparaison_gzip    = stristr($content_access, $gzip);
$comparaison_vitesse = stristr($content_access, $vitesse);
$comparaison_cache 	 = stristr($content_access, $cache);

$version = phpversion();
$array   = explode(".",$version);

if($comparaison_gzip == true && $comparaison_vitesse== true && $comparaison_cache == true && ($array[0] > 5)) {
	$valids['total'] = '<div class="alert alert-success">
						  <strong>'.__('Everything is optimized !','carla').'</strong>
					   </div>';
}else{
	$errors['total'] = '<div class="alert alert-danger">
							  <strong>'.__('Warning ! ','carla').'</strong>'.__('Everything is not optimized !','carla').'</div>';
}
			
/* ---------------------------------------------------------------------------------- */					
				
if(isset($_POST['optimiser'])) {
	
/* ---------------------------------------------------------------------------------- */	
	
	if($htaccess == "gzip"){
		
		if($comparaison_gzip == false){
			  insert_with_markers($htaccesschemin, "carla_gzip", $gzip);
			  $valids['gzip'] = '<div class="alert alert-success">
									<strong>'.__('GZIP cache enabled!','carla').'</strong>
							   </div>';  
		}else{
			$errors['gzip'] = '<div class="alert alert-danger">
									<strong>'.__('Warning ! ','carla').'</strong>'.__(' GZIP compression is already enabled!','carla').' </div>';
		}
	}

/* ---------------------------------------------------------------------------------- */	

	elseif($htaccess == "cache") {
		
		if($comparaison_cache == false){
				insert_with_markers($htaccesschemin, "carla_cache", $cache);
				$valids['cache'] = '<div class="alert alert-success">
									  <strong>'.__('Caching enabled!','carla').'</strong>
								   </div>';  
		}else{
			$errors['cache'] = '<div class="alert alert-danger">
								  <strong>'.__('Warning ! ','carla').'</strong> '.__(' Caching is already enabled!','carla').'</div>';
								
		}
	}

/* ---------------------------------------------------------------------------------- */	
	
	elseif($htaccess == "vitesse") {
		
		if($comparaison_vitesse == false){
			  insert_with_markers($htaccesschemin, "carla_vitesse", $vitesse);
			  $valids['vitesse'] = '<div class="alert alert-success">
									  <strong>'.__('The speed of your site is optimized!','carla').'</strong>
								   </div>';  
		}else{
			$errors['vitesse'] = '<div class="alert alert-danger">
									 <strong>'.__('Warning ! ','carla').'</strong>'.__(' The speed of your site is already optimized!','carla').'</div>';
								  
		}
	}
	
/* ---------------------------------------------------------------------------------- */
	
	elseif(empty($htaccess)) {
		/////////////////////////////////////SI VIDE 
	}
}
?>	

<div class="block">
	<div class="container">
		<div class="row" class="optimisation_container" >
	 	
			<div class="col-sm-5">
				<div class="card">
					<div class="card-block">
						<button type="button"  class="btn btn-success center-block" onClick="window.location.reload()"><i class="fa fa-circle-o-notch fa-spin"></i> Mettre à jour</button><br />
					<?php
					
					/* *********************************************************************************** */
					if($comparaison_gzip == false){
						 echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('GZIP is not activated !','carla').'</div>';
					}else{
						echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('GZIP is enabled !','carla').'</div>';
					}
					
					/* *********************************************************************************** */
					if($comparaison_cache == false){
						echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('Caching is not enabled !','carla').'</div>';  
					}else{
						echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('The cache is enabled !','carla').'</div>';
											
					}
				
					/* *********************************************************************************** */
					if($comparaison_vitesse == false){
						echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('Speed is not optimized !','carla').'</div>';
					}else{
						echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('The speed of your site is optimized !','carla').'</div>';
											  
					}
					/* *********************************************************************************** */
					if($array[0] <= 5) {
						echo '<div class="check_prob"><i class="fa fa-check" aria-hidden="true"></i> '.__('Your PHP version is too old !','carla').' (PHP '.$version.')</div>';
					}else{
						echo '<div class="check_ok"><i class="fa fa-check" aria-hidden="true"></i> '.__('Your PHP version is up to date !','carla').'</div>';
					}
					/* *********************************************************************************** */
					
					?>
					</div>
				</div>
			</div>
				
			<div class="col-sm-offset-1 col-sm-6">
				<div class="card">
					<div class="card-block">
						<h3 class="card-title"><?php echo __('Optimize the speed of your site','carla');?></h3>
						<p class="card-text">
							<form method="POST" action="">
								<select name="htaccess" class="responsive">
									<option value="gzip" <?php if($comparaison_gzip == true){ echo 'disabled'; }?>>
										<?php echo __('Enable GZIP compression','carla');?></option>
									<option value="cache" <?php if($comparaison_cache == true){ echo 'disabled'; }?>>
										<?php echo __('Caching','carla');?></option>
									<option value="vitesse" <?php if($comparaison_vitesse == true){ echo 'disabled'; }?>>
										<?php echo __('Boost site speed','carla');?></option>
								</select>
							<input type="submit" name="optimiser" value="<?php echo __('Optimize','carla');?>" <?php if($comparaison_gzip == true && $comparaison_vitesse== true && $comparaison_cache == true) { echo "disabled";} ?> class="btn btn-primary marge_input"/>
							</form>
						</p>
						<div class="row">
							<div class="col-sm-4">
								<div class="margebutton">
									<!-- Button trigger modal -->
									<button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#optimisation">
										<?php echo __('Read more','carla');?>
									</button>
								</div>
							</div>
							<div class="col-sm-8">
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
		<div class="modal fade" id="optimisation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h1 class="modal-title" id="myModalLabel"><?php echo __('Optimization','carla');?></h1>
					</div>
					<div class="modal-body">
						<h2><?php echo __('Enable GZIP compression','carla');?></h2>
							<p><?php echo __('Compression is a simple and effective way to save bandwidth and speed up the loading speed of your site.','carla');?></p>
						<br />
						<h2><?php echo __('Caching','carla');?></h2>
							<p><?php echo __('The .htaccess file lets you cache some of your site\'s files in your visitors\' browser for faster loading.','carla');?><br />
								<?php echo __('Indeed, the browser will not need to re-download the files present in its cache.','carla');?></p>
						<br />
						<h2><?php echo __('Server response time optimization','carla');?></h2>
							<p>
								<?php echo __('The directive is deactivated','carla');?> « <span class="valids">REGISTER_GLOBALS</span> » <?php echo __('which does not add anything to WordPress, just like the directive','carla');?> « <span class="valids">MAGIC_QUOTES</span> » <?php echo __('with','carla');?> « <span class="valids">SetEnv MAGIC_QUOTES 0</span> ».<br />
								<?php echo __('However, the module','carla');?> « <span class="valids">ZEND_OPTIMIZER</span> » <?php echo __('is astivated, which makes it possible to optimize the PHP code and to cache the most used queries.','carla');?><br />
								<?php echo __('We also activate the PHP version 5 which will be useful for quite a lot of WordPress module.','carla');?>
							</p>
						<br />
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close','carla');?></button>
					</div>
				</div>
			</div>  
		</div>
<!--  ------------------------------------------------------------------------------------------------------------------------------>

	<!-- END CONTAINER + BLOC -->
	</div>
</div>