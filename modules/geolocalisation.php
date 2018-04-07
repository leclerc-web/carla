<?php
if ( ! defined( 'ABSPATH' ) ) exit;
global $wpdb ;
global $current_user;
get_currentuserinfo();
?>


<div class="block">
	<div class="container-fluid">

		<!-- BUTTON -->	
		<div class="text-center">
			
			<form method="POST">
				<input type="hidden" name="delete_sql" value="<?php echo wp_create_nonce('delete_form'); ?>"/>
				<button name="delete" type="submit" class="btn btn-danger button_form_asc"><?php echo __('Delete the ips','carla');?></button>
			</form><br />

			<div class="inline-ip">
				<form method="POST">
					<input type="hidden" name="asc_sql" value="<?php echo wp_create_nonce('asc_form'); ?>"/>
					<button name="asc" type="submit" class="btn btn-success button_form_asc"><?php echo __('Ascending','carla');?></button>
				</form>
			</div>
			
			<div class="inline-ip">	
				<form method="POST">
					<input type="hidden" name="day_sql" value="<?php echo wp_create_nonce('day_form'); ?>"/>
					<button name="day" type="submit" class="btn btn-success button_form_asc"><?php echo __('IP of the day','carla');?></button>
				</form>
			</div>
			
			<div class="inline-ip">
				<form method="POST">
					<input type="hidden" name="desc_sql" value="<?php echo wp_create_nonce('desc_form'); ?>"/>
					<button name="desc" type="submit" class="btn btn-success button_form_asc"><?php echo __('Descending','carla');?></button>
				</form>
			</div>	
			
		</div>
<!-- END BUTTON -->
	
<?php

// REQUËTE DE BASE
$resultats = $wpdb->get_results("SELECT *,DATE_FORMAT(date_log, '%d/%m/%Y %H:%i:%s') AS date FROM {$wpdb->prefix}ip_log ORDER BY date DESC ") ;


/*******************FORM DELETE***************************/
$delete 	= $_POST['delete'];
$delete_sql = $_POST['delete_sql'];

if(isset($delete)){
	if( ! wp_verify_nonce($delete_sql, 'delete_form' )) {
		 die( 'Security check' ); 		
	}else{
		$sql = "DELETE FROM {$wpdb->prefix}ip_log;";
		$wpdb->query($sql);	
	}
}

/********************FORM ASC**************************/

$asc 	 = $_POST['asc'];
$asc_sql = $_POST['asc_sql'];

if(isset($asc)){
	if( ! wp_verify_nonce($asc_sql, 'asc_form' )) {
		 die( 'Security check' ); 		
	}else{
		$resultats = $wpdb->get_results("SELECT *,DATE_FORMAT(date_log, '%d/%m/%Y %H:%i:%s') AS date FROM {$wpdb->prefix}ip_log ORDER BY date ASC") ;	
	}
}

/********************FORM DAY**************************/

$day 	 	= $_POST['day'];
$day_sql 	= $_POST['day_sql'];

if(isset($day)){	
	if( ! wp_verify_nonce($day_sql, 'day_form' )) {
		 die( 'Security check' ); 		
	}else{
		// FORMATAGE DE DATE POUR COMPARAISON TOUT EN GARDANT LE FORMAT AVEC HEURES MINUTES SECONDES
		$resultats      = $wpdb->get_results("SELECT *,DATE_FORMAT(date_log, '%d/%m/%Y %H:%i:%s') AS date FROM {$wpdb->prefix}ip_log HAVING DATE_FORMAT(date_log, '%d/%m/%Y') = DATE_FORMAT(NOW(), '%d/%m/%Y')");
	}
}

/********************FORM DESC**************************/

$desc 	  = $_POST['desc'];
$desc_sql = $_POST['desc_sql'];

if(isset($desc)){
	if( ! wp_verify_nonce($desc_sql, 'desc_form' )) {
		 die( 'Security check' ); 		
	}else{
		$resultats = $wpdb->get_results("SELECT *,DATE_FORMAT(date_log, '%d/%m/%Y %H:%i:%s') AS date FROM {$wpdb->prefix}ip_log ORDER BY date DESC") ;
	}
}

/********************************************************************************************************/

echo '<table class="table table-striped ip">
		<thead>
			<tr>
				<th>IP du visiteur</th>
				<th>Date / Heure</th>
				<th>Détails</th>
			</tr>
		</thead>
		<tbody>';

	foreach ($resultats as $infos) {
	
	 echo '
		<form method="POST" action="">
            <tr>
				<td>'.$infos->ip.'</td>
				<td>'.$infos->date.'</td>
				<td>
					<form method="POST" action="">
						<input type="hidden" name="ipinfos" value="'.$infos->ip.'" />
						<input type="hidden" name="date" value="'.$infos->date.'" />
						<input type="hidden" name="see_details" value="'.wp_create_nonce('details_nonce').'"/>
						<button  name="envoyer" class="btn btn-primary" type="submit">'.__('Details','carla').'</button>
					</form>
				</td>
			</tr>
		</form>
		  ';
	}
echo '</tbody> </table>';

/********************************************************************************************************/

	$ipinfos = sanitize_text_field($_POST['ipinfos']);
	/* http://ip-api.com/docs/api:returned_values */
	$verify_details = wp_verify_nonce($_POST['see_details'], 'details_nonce' );
	
	
	if(isset($_POST['envoyer'])) {
		if( ! $verify_details) {
			die( 'Security check' );	 
		}
		elseif (isset($verify_details)){
			$query = @unserialize(file_get_contents('http://ip-api.com/php/'.$ipinfos)); 
			if($query && $query['status'] == 'success') {	
?>


<!-- ---------- MODAL IP----------  -->
	<div class="modal fade" id="important" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
				  <h1 class="text-center"><?php echo __('Geolocation','carla');?></h1>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				</div> 
					<div class="container-fluid">
						<table class="table">
							<tbody>
								<tr>
									<th scope="row"><?php echo __('Country','carla');?></th>
									<td><?= $query['country'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('Postal Code','carla');?></th>
									<td><?= $query['zip'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('Region','carla');?></th>
									<td><?= $query['regionName'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('City','carla');?></th>
									<td><?= $query['city'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('Zip','carla');?></th>
									<td><?= $query['countryCode'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('Latitude','carla');?></th>
									<td><?= $query['lat'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('Longitude','carla');?></th>
									<td><?= $query['lon'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('Time zone','carla');?></th>
									<td><?= $query['timezone'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('Org','carla');?></th>
									<td><?= $query['org'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('IP','carla');?></th>
									<td><?= $query['query'] ?></td>
								</tr>
								<tr>
									<th scope="row"><?php echo __('Web browser','carla');?></th>
									<td><?= $_SERVER['HTTP_USER_AGENT'] ?></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo __('Close','carla');?></button>
					</div>
			</div>
		</div>
	</div>		
	
<?php
		}
	}
}
?>
	<!-- END CONTAIER + BLOC -->
	</div>
</div>

<script type="text/javascript">
	jQuery(document).ready(function( $ ) {
		
		$(window).load(function(){
		   $('#important').modal('show');
		});
		
	});
</script>