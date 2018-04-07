<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb ;
global $current_user;
get_currentuserinfo();
?>

<div class="container">
	<div class="row">
		<div class="col-sm-6">
			<div class="card">
				<div class="card-block">
					<h3 class="card-title"><?php echo __('Encrypt password','carla');?></h3>
					<p class="card-text">
						<table>
							<tr>
								<td>
									<form method="POST" action="">
										<input type="password"  id="exampleInputEmail1" name="password" placeholder="<?php echo __('Password...','carla');?>">
										<!-- NONCE -->
										<input type="hidden" name="hash_nonce" value="<?php echo wp_create_nonce('security_hash'); ?>"/>
										<!-- ----- -->
										<button type="submit" name="crypter" class="btn btn-primary"><?php echo __('Encrypt password','carla');?></button>
									</form>	
								</td>
							</tr>
						</table>
					</p>
					<div class="row">
						<div class="col-sm-4">
						<!-- Button trigger modal -->
							<button type="button" class="btn btn-danger btn-lg" data-toggle="modal" data-target="#hash">
								<?php echo __('Read more','carla');?>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>

<?php
$verify_hash = wp_verify_nonce($_POST['hash_nonce'], 'security_hash' );
$cryptform   = $_POST['crypter'];
$password  	 = sanitize_text_field($_POST['password']);		
	
$md4 		 = hash('md4', $password);
$md5 		 = md5($password);
$sha1 		 = sha1($password);
$bcrypt		 = password_hash($password, PASSWORD_DEFAULT);

$cryptage 	 = array (
					'MD4'    => $md4,
					'MD5'    => $md5,
					'SHA-1'  => $sha1,
					'BCRYPT' => $bcrypt
				  );
				  
if(isset($cryptform)){
	if( ! $verify_hash) {
			die( 'Security check' );	 
	}else{
?>
	
		<div class="col-sm-6">
			<div class="margincard">
					<?php 
						if(!empty($password)) {
							echo   '<div class="alert alert-success"><strong>'.__('you chose :','carla') .'"'. $password .'"</strong> </div>';				   
						}else{
							echo '<div class="alert alert-danger"><strong>'.__('Warning !','carla').'</strong> '.__('You have not configured a password !','carla').'</div>';
						}
					?>		
			</div>
		</div>
	</div>
	
<?php
		if(!empty($password)) {
			echo '<table class="table table-striped margetab" >
				<thead>
					<tr>
						<th>'.__('Method','carla').'</th>
						<th>'.__('Value','carla').'</th>
					</tr>
				</thead>
				<tbody>';

			foreach($cryptage as $cle => $element)
			{	
				echo '<form method="POST" action=""><tr>';
				if($element == $md5 || $element == $bcrypt){
						echo
						'<tr>
							<td  class="success">'.$cle.'</td>
							<td  class="success">'.$element.'</td>
							<td>
								<form method="POST" action="">
									<input type="hidden" name="cle" value="'.$cle.'" />
									<input type="hidden" name="element" value="'.$element.'" />
									<input type="hidden" name="password_nonce" value="'.wp_create_nonce('security-nonce').'"/>
									<button name="envoyer" class="btn btn-default" type="submit">'.__('Edit','carla').'</button>
								</form>
							</td>
						</tr>';
				}elseif($element == $sha1 || $element == $md4){
					echo
						'<tr>
							<td  class="danger">'.$cle.'</td>
							<td  class="danger">'.$element.'</td>
							<td>
								<form method="POST" action="">
									<input type="hidden" name="cle" value="'.$cle.'" />
									<input type="hidden" name="element" value="'.$element.'" />
									<button name="envoyer" class="btn btn-default" type="submit" disabled="disabled">'.__('Edit','carla').'</button>
								</form>
							</td>
						</tr>';
				}
			}
			echo '</tbody> </table>';
		 }
	}
}

$element = sanitize_text_field($_POST['element']);
$verify_hash = wp_verify_nonce($_POST['password_nonce'], 'security-nonce' );

if(isset($_POST['envoyer']) && !empty($element)) {
	
	if( ! $verify_hash) {
		die( 'Security check' );	
	}
	elseif(isset($verify_hash) && !empty($element)){
		$results = $wpdb->get_results("UPDATE {$wpdb->prefix}users SET user_pass = '$element' WHERE user_email='$current_user->user_email'", OBJECT );
		$enregistrement['oui'] = '<div class="alert alert-success"><strong>'.__('It\'s ok !','carla').'</strong> '.__('You have just changed your password','carla').'</div>';							  
	}
	elseif(isset($verify_hash) && empty($element)){	
		$enregistrement['non'] = '<div class="alert alert-danger"><strong>'.__('Warning !','carla').'</strong> '.__('The password is not changed','carla').'</div>';
	}
}		
?>

</div>

<div class="margincard">				
	<?php 
		if (is_array($enregistrement)){
				foreach ($enregistrement as $enregistrements){
					echo $enregistrements."<br />"; 
				}
		}
	?>
</div>

<!--  ----------------------------------------------------------- MODAL ------------------------------------------------------------>
<div class="modal fade" id="hash" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h1 class="modal-title" id="myModalLabel"><?php echo __('encryption / password change','carla');?></h1>
			</div>
			<div class="modal-body">
				<p><?php echo __('By clicking on "edit" the password of the account you use will be changed.','carla');?></p>
				
				<span class="valids"><?php echo __('Green color','carla');?> </span><?php echo __('means that it\'s a hash mode compatible with Wordpress.','carla');?><br />
				<span class="errors"><?php echo __('Red color','carla');?> </span><?php echo __('means that it is a hash mode that is incompatible with Wordpress (for information)','carla');?><br /><br />
				<span class="attention"><?php echo __('It is strongly recommended to choose the BCRYPT hash mode for the reasons below.','carla');?></span>
				
				<h2><?php echo __('What is bcrypt?','carla');?></h2>
				<p>
					<?php echo __('Bcrypt is a hashing algorithm that is scalable depending on the server that launches it. Bcrypt uses the Eksblowfish hashing algorithm. Whoever decides to hack your password for example will have a lot to do and will have to invest in expensive materials.','carla');?>
					<br />
					<?php echo __('Add to this that the algorithm always uses salt (grains of salts) which adds further complicity.','carla');?>
				</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close','carla');?></button>
			</div>
		</div>
	</div>
</div>
<!--  ------------------------------------------------------------------------------------------------------------------------------>