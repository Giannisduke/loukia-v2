<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
?><table class="table widefat fixed mtable woo-feed-ftp">
	<?php if ( ! checkFTP_connection() && ! checkSFTP_connection() ) { ?>
		<tbody>
		<tr>
			<th><?php esc_attr_e( 'FTP/SFTP module is not found in your server. Please contact your service provider or system administrator to install/enable FTP/SFTP module.', 'woo-feed' ); ?></th>
		</tr>
		</tbody>
	<?php } else { ?>
		<tbody>
		<tr>
			<td><label for="ftpenabled"><?php esc_html_e( 'Enabled', 'woo-feed' ); ?></label></td>
			<td>
				<select name="ftpenabled" id="ftpenabled">
					<option <?php echo ( '0' == $feedRules['ftpenabled'] ) ? 'selected="selected" ' : ''; ?>value="0"><?php esc_html_e( 'Disabled', 'woo-feed' ); ?></option>
					<option <?php echo ( '1' == $feedRules['ftpenabled'] ) ? 'selected="selected" ' : ''; ?>value="1"><?php esc_html_e( 'Enabled', 'woo-feed' ); ?></option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="ftporsftp"><?php esc_html_e( 'Server Type', 'woo-feed' ); ?></label></td>
			<td>
				<select name="ftporsftp" id="ftporsftp" class="ftporsftp">
					<option <?php echo ( 'ftp' == $feedRules['ftporsftp'] ) ? 'selected="selected" ' : ''; ?> value="ftp"><?php esc_html_e( 'FTP', 'woo-feed' ); ?></option>
					<option <?php echo ( 'sftp' == $feedRules['ftporsftp'] ) ? 'selected="selected" ' : ''; ?>value="sftp"><?php esc_html_e( 'SFTP', 'woo-feed' ); ?></option>
				</select>
				<span class="ssh2_status"></span>
			</td>
		</tr>
		<tr>
			<td><label for="ftphost"><?php esc_html_e( 'Host Name', 'woo-feed' ); ?></label></td>
			<td><input type="text" id="ftphost" value="<?php echo esc_attr( $feedRules['ftphost'] ); ?>" name="ftphost" autocomplete="off"/></td>
		</tr>
		<tr>
			<td><label for="ftpport"><?php esc_html_e( 'Port', 'woo-feed' ); ?></label></td>
			<td><input type="text" id="ftpport" value="<?php echo isset( $feedRules['ftpport'] ) ? esc_attr( $feedRules['ftpport'] ) : 21; ?>" name="ftpport" autocomplete="off"/></td>
		</tr>
		<tr>
			<td><label for="ftpuser"><?php esc_html_e( 'User Name', 'woo-feed' ); ?></label></td>
			<td><input type="text" id="ftpuser" value="<?php echo esc_attr( $feedRules['ftpuser'] ); ?>" name="ftpuser" autocomplete="off"/></td>
		</tr>
		<tr>
			<td><label for="ftppassword"><?php esc_html_e( 'Password', 'woo-feed' ); ?></label></td>
			<td><input type="password" id="ftppassword" value="<?php echo esc_attr( $feedRules['ftppassword'] ); ?>" name="ftppassword" autocomplete="off"/></td>
		</tr>
		<tr>
			<td><label for="ftppath"><?php esc_html_e( 'Path', 'woo-feed' ); ?></label></td>
			<td><input type="text" id="ftppath" value="<?php echo esc_attr( $feedRules['ftppath'] ); ?>" name="ftppath" autocomplete="off"/></td>
		</tr>
        <tr>
            <td><label for="ftpmode"><?php esc_html_e( 'Connection Mode', 'woo-feed' ); ?></label></td>
            <td>
                <select name="ftpmode" id="ftpmode" class="ftpmode">
                    <option <?php echo (isset($feedRules['ftpmode']) && 'active' == $feedRules['ftpmode'] ) ? 'selected="selected" ' : ''; ?> value="active"><?php esc_html_e( 'Active', 'woo-feed' ); ?></option>
                    <option <?php echo (isset($feedRules['ftpmode']) && 'passive' == $feedRules['ftpmode'] ) ? 'selected="selected" ' : ''; ?>value="passive"><?php esc_html_e( 'Passive', 'woo-feed' ); ?></option>
                </select>
            </td>
        </tr>
        </tbody>
	<?php } ?>
</table>
