<?php
/**
 * Glav.in
 *
 * A very simple CMS
 *
 *
 * @package		Glav.in
 * @author		Matt Sparks
 * @copyright	Copyright (c) 2013, Matt Sparks (http://www.mattsparks.com)
 * @license		http://opensource.org/licenses/MIT The MIT License (MIT)
 * @link		http://glav.in
 * @since		3.2.0-alpha
 */

// Only Admins can access this page
if ( $user_level == 1 ) {

	$user_email = '';
	$user_password = '';
	$user_level = 2;
	$user_passed = '';
	$updated = false;

	// Do we have a page to edit?
	if ( isset( $_GET['passed'] ) && $_GET['passed'] != '' ) {
		
		$user_passed = $_GET['passed'];

		// See if the page exists
		if ( !$data->file_exist( USERS_DIR . $user_passed ) ) {
			$errors[] = 'Page Not Found';
		} else {
			$content          = $data->get_content( USERS_DIR . $user_passed );
			$user_email       = $user_passed;
			$user_level       = $content['user']['user_level'];

		}
	} else {
		$errors[] = 'No User Selected <a href="'. base_url() .'admin/users" title="Users">Return to Users List</a>';
	}

	// The form has been submitted
	if ( $_POST ) {

		$u = array(
			'user' => array()
		);

		$e_user_level = isset( $_POST['user_level'] ) ? $_POST['user_level'] : '';

		if ( $e_user_level != '' ) {
			$u['user']['user_level'] = $e_user_level;
		}

		if ( isset( $_POST['new_password_1'] ) && isset( $_POST['new_password_2'] ) ) {		

			// Make sure new passwords match
			if ( $_POST['new_password_1'] != $_POST['new_password_2'] ) {
				$errors[] = "Passwords do not match";
			} else {

				// If passwords match and aren't empty, 
				// update the password.
				if ( $_POST['new_password_1'] != '' ) {
					$u['user']['password'] = $_POST['new_password_1'];
				}
			}
		}	
		
		// If there's no errors update the user
		if ( empty( $errors ) ) {
			if ( $user->update( $user_passed, $u ) ) {
				$msgs[] = 'User Updated! <a href="'. base_url() .'admin/users" title="Users">Return to Users List</a>';
				$updated = true;
			}
		}
	}		
?>
<div id="page-description">
	<h1>Edit User</h1>
</div><!-- end page-description -->
<div id="admin-content-body">
	<?php
	// Print out any messages or errors
	foreach( $msgs as $msg ) {
		echo '<div class="msg">' . $msg . '</div>';
	}

	foreach( $errors as $errors ) {
		echo '<div class="error">' . $errors . '</div>';
	}

	if ( isset( $_GET['passed'] ) && $_GET['passed'] != ''  && $data->file_exist( USERS_DIR . $user_passed ) && !$updated ) {
	?>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
		<h2><?php echo $user_email; ?></h2>
		<input type="password" placeholder="New Password" name="new_password_1" />
		<input type="password" placeholder="Repeat New Password" name="new_password_2" />
		<p>
			<strong>User Level</strong><br>
			<select name="user_level">
				<option value="2"<?php echo $user_level == 2 ? ' selected="true"' : ''; ?>>Contributor</option>
				<option value="1"<?php echo $user_level == 1 ? ' selected="true"' : ''; ?>>Admin</option>
			</select>
		</p>
		<input type="submit" value="Submit">
	</form>
	<?php
	}
}
?>