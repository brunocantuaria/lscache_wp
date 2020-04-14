<?php
namespace LiteSpeed;
defined( 'WPINC' ) || exit;

$__cloud = Cloud::get_instance();

// This will drop QS param `qc_res` and `domain_hash` also
$__cloud->update_is_linked_status();

$permalink_structure = get_option( 'permalink_structure' );

$cloud_summary = Cloud::get_summary();

$can_token = $__cloud->can_token();

$apply_btn_txt = __( 'Apply Domain Key', 'litespeed-cache' );
if ( Conf::val( Base::O_API_KEY ) ) {
	$apply_btn_txt = __( 'Refresh Domain Key', 'litespeed-cache' );
	if ( ! $can_token && ! empty( $cloud_summary[ 'token_ts' ] ) && ! empty( $cloud_summary[ 'apikey_ts' ] ) && $cloud_summary[ 'token_ts' ] > $cloud_summary[ 'apikey_ts' ] ) {
		$apply_btn_txt = __( 'Waiting for Refresh', 'litespeed-cache' );
	}
}
elseif ( ! $can_token ) {
	$apply_btn_txt = __( 'Waiting for Approval', 'litespeed-cache' );
}

$apply_ts_txt = '';
if ( ! empty( $cloud_summary[ 'token_ts' ] ) ) {
	$apply_ts_txt .= ' ' . __( 'Requested', 'litespeed-cache' ) . ': <code>' . Utility::readable_time( $cloud_summary[ 'token_ts' ] ) . '</code>';
}
if ( ! empty( $cloud_summary[ 'apikey_ts' ] ) ) {
	$apply_ts_txt .= ' ' . __( 'Approved', 'litespeed-cache' ) . ': <code>' . Utility::readable_time( $cloud_summary[ 'apikey_ts' ] ) . '</code>';
}

$this->form_action();
?>

<h3 class="litespeed-title-short">
	<?php echo __( 'General Settings', 'litespeed-cache' ); ?>
	<?php $this->learn_more( 'https://www.litespeedtech.com/support/wiki/doku.php/litespeed_wiki:cache:lscwp:configuration:general', false, 'litespeed-learn-more' ); ?>
</h3>

<table class="wp-list-table striped litespeed-table"><tbody>
	<?php if ( ! $this->_is_multisite ) : ?>
		<?php require LSCWP_DIR . 'tpl/general/settings_inc.auto_upgrade.tpl.php'; ?>
	<?php endif; ?>

	<tr>
		<th>
			<?php $id = Base::O_API_KEY; ?>
			<?php $this->title( $id ); ?>
		</th>
		<td>
			<?php $this->build_input( $id ); ?>

			<?php if ( $permalink_structure && $can_token ) : ?>
				<?php $this->learn_more( Utility::build_url( Router::ACTION_CLOUD, Cloud::TYPE_GEN_KEY ), $apply_btn_txt, 'button litespeed-btn-success', true ); ?>
			<?php else: ?>
				<?php $this->learn_more( 'javascript:;', $apply_btn_txt, 'button disabled', true ); ?>
			<?php endif; ?>
			<?php if ( $apply_ts_txt ) : ?>
				<span class="litespeed-desc"><?php echo $apply_ts_txt; ?></span>
			<?php endif; ?>

			<?php if ( ! empty( $cloud_summary[ 'is_linked' ] ) ) : ?>
				<?php $this->learn_more( Cloud::CLOUD_SERVER_DASH, __( 'Visit My Dashboard on QUIC.cloud', 'litespeed-cache' ), 'button litespeed-btn-success litespeed-right', false ); ?>
			<?php elseif ( $__cloud->can_link_qc() ) : ?>
				<?php $this->learn_more( Utility::build_url( Router::ACTION_CLOUD, Cloud::TYPE_LINK ), __( 'Link to QUIC.cloud', 'litespeed-cache' ), 'button litespeed-btn-warning litespeed-right', true ); ?>
			<?php else: ?>
				<?php $this->learn_more( 'javascript:;', __( 'Link to QUIC.cloud', 'litespeed-cache' ), 'button disabled litespeed-btn-warning litespeed-right', true ); ?>
				<div class="litespeed-callout notice notice-error inline">
					<h4><?php echo __( 'Warning', 'litespeed-cache' ); ?>:</h4>
					<p><?php echo sprintf( __( 'You must have %1$s first before linking to QUIC.cloud.', 'litespeed-cache' ), '<code>' . Lang::title( Base::O_API_KEY ) . '</code>' ); ?></p>
				</div>
			<?php endif; ?>

			<?php if ( ! $permalink_structure ) : ?>
				<div class="litespeed-callout notice notice-error inline">
					<h4><?php echo __( 'Warning', 'litespeed-cache' ); ?>:</h4>
					<p><?php echo sprintf( __( 'You must set WordPress %1$s to a value other than %2$s before generating an Domain key.', 'litespeed-cache' ), '<code>' . __( 'Permalink Settings' ) . '</code>', '<code>' . __( 'Plain' ) . '</code>' ); ?>
						<?php echo '<a href="options-permalink.php">' . __( 'Click here to config', 'litespeed-cache' ) . '</a>'; ?>
					</p>
				</div>
			<?php endif; ?>

			<div class="litespeed-desc">
				<?php echo __( 'An Domain key is necessary for security when communicating with our QUIC.cloud servers. Required for online services.', 'litespeed-cache' ); ?>
				<br /><?php Doc::notice_ips(); ?>
				<div class="litespeed-callout notice notice-success inline">
					<h4><?php echo __( 'Current Cloud Nodes in Service','litespeed-cache' ); ?>
						<a class="litespeed-right" href="<?php echo Utility::build_url( Router::ACTION_CLOUD, Cloud::TYPE_CLEAR_CLOUD ); ?>" data-balloon-pos="up" data-balloon-break aria-label='<?php echo __( 'Click to clear all nodes for further redetection.', 'litespeed-cache' ); ?>' data-litespeed-cfm="<?php echo __( 'Are you sure to clear all cloud nodes?', 'litespeed-cache' ) ; ?>"><i class='litespeed-quic-icon'></i></a>
					</h4>
					<p>
						<?php
						$has_service = false;
						foreach ( Cloud::$SERVICES as $svc ) {
							if ( isset( $cloud_summary[ 'server.' . $svc ] ) ) {
								$has_service = true;
								echo '<p><b>Service:</b> <code>' . $svc . '</code> <b>Node:</b> <code>' . $cloud_summary[ 'server.' . $svc ] . '</code> <b>Connected Date:</b> <code>' . Utility::readable_time( $cloud_summary[ 'server_date.' . $svc ] ) . '</code></p>';
							}
						}
						if ( ! $has_service ) {
							echo __( 'No cloud services currently in use', 'litespeed-cache' );
						}
						?>
					</p>
				</div>

			</div>
		</td>
	</tr>

	<tr>
		<th>
			<?php $id = Base::O_NEWS; ?>
			<?php $this->title( $id ); ?>
		</th>
		<td>
			<?php $this->build_switch( $id ); ?>
			<div class="litespeed-desc">
				<?php echo __( 'Turn this option ON to show latest news automatically, including hotfixes, new releases, available beta versions, and promotions.', 'litespeed-cache' ); ?>
			</div>
		</td>
	</tr>

</tbody></table>

<?php
$this->form_end();

