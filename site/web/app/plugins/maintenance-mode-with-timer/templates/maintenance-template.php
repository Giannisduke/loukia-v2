<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

include('header.php');
?>
<div class="mtm-main-wrapper mtm-design-4">
	<div class="mtm-container">
		<div class="mtm-container-content">
			<div class="mtm-container-bg">
				<div class="mtm-company-logo" <?php echo $mtm_company_logo_width; ?>>
					<img src="<?php echo $mtm_company_logo; ?>">
				</div>
				<?php if($mtm_title != '') { ?>
					<div class="mtm-title">
						<span><?php echo $mtm_title; ?></span>
					</div>
				<?php } ?>
				<?php if($mtm_content != '') { ?>
					<div class="mtm-content">
						<?php echo $mtm_content; ?>
					</div>
				<?php } ?>		
				<div class="mtm-countdown-wrp mtm-clock-wrapper">
					<div id="mtm-datecount-<?php echo $unique; ?>" class="mtm-countdown-timer-design">
						<?php
								include( 'clock-design.php' );
						?>
						<div class="mtm-date-conf"><?php echo json_encode( $date_conf ); ?></div>
					</div>
				</div>

				<div class="mtm-socials">
					<?php if($mtm_facebook){ ?>
						<a href="<?php echo $mtm_facebook; ?>" target="_blank">
							<i class="demo-icon icon-facebook"></i>
						</a>
					<?php } ?>
					<?php if($mtm_twitter){ ?>
						<a href="<?php echo $mtm_twitter; ?>" target="_blank">
							<i class="demo-icon icon-twitter"></i>
						</a>
					<?php } ?>
					<?php if($mtm_linkedin){ ?>
						<a href="<?php echo $mtm_linkedin; ?>" target="_blank">
							<i class="demo-icon icon-linkedin"></i>
						</a>
					<?php } ?>
					<?php if($mtm_github){ ?>
						<a href="<?php echo $mtm_github; ?>" target="_blank">
							<i class="demo-icon icon-github-circled"></i>
						</a>
					<?php } ?>
					<?php if($mtm_youtube){ ?>
						<a href="<?php echo $mtm_youtube; ?>" target="_blank">
							<i class="demo-icon icon-youtube-play"></i>
						</a>
					<?php } ?>
					<?php if($mtm_pinterest){ ?>
						<a href="<?php echo $mtm_pinterest; ?>" target="_blank">
							<i class="demo-icon icon-pinterest-circled"></i>
						</a>
					<?php } ?>
					<?php if($mtm_instagram){ ?>
						<a href="<?php echo $mtm_instagram; ?>" target="_blank">
							<i class="demo-icon icon-instagram"></i>
						</a>
					<?php } ?>
					<?php if($mtm_google_plus){ ?>
						<a href="<?php echo $mtm_google_plus; ?>" target="_blank">
							<i class="demo-icon icon-gplus"></i>
						</a>
					<?php } ?>
					<?php if($mtm_tumblr){ ?>
						<a href="<?php echo $mtm_tumblr; ?>" target="_blank">
							<i class="demo-icon icon-tumbler"></i>
						</a>
					<?php } ?>
					<?php if($mtm_email){ ?>
						<a href="mailto:<?php echo $mtm_email; ?>" target="_blank">
							<i class="demo-icon icon-mail-alt"></i>
						</a>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
include('footer.php');