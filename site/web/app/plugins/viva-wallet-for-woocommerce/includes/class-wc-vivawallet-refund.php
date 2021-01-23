<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
/**
 * Class
 *
 * WC_Vivawallet_Refund
 */
class WC_Vivawallet_Refund {


	/**
	 * Process refund
	 *
	 * @param array  $credentials credentials.
	 *
	 * @param int    $order_id Order ID.
	 *
	 * @param float  $amount Refund amount.
	 *
	 * @param string $reason Refund reason.
	 *
	 * @throws Exception WP_Error.
	 *
	 * @return bool|WP_Error
	 */
	public static function process_refund( $credentials, $order_id, $amount = null, $reason = '' ) {
		$order               = wc_get_order( $order_id );
		$refund_amount_cents = round( $amount * 100 );
		$order_total         = $order->get_total();
		$order_total         = round( $order_total * 100 );
		$is_full_refund      = ( $refund_amount_cents === $order_total ) ? true : false;

		try {
			if ( $order->get_status() === WC_Vivawallet_Helper::ORDER_STATUS_REFUNDED || $order->get_status() === WC_Vivawallet_Helper::ORDER_STATUS_CANCELLED ) {
				throw new Exception( __( 'You cannot edit an already refunded or canceled order.', 'woocommerce_vivawallet' ) );
			}
			if ( false === $is_full_refund ) {
				$post_meta_order_paid = get_post_meta( $order_id, WC_Vivawallet_Helper::POST_META_WC_ORDER_PAID );
				$paid_date            = $post_meta_order_paid [0];
				if ( ! self::can_refund_on_vivawallet( $paid_date ) ) {
					throw new Exception( __( 'Partial Refund not available yet. Please try later or contact Viva Wallet support for more info.', 'woocommerce_vivawallet' ) );
				}
			}
			$post_meta = get_post_meta( $order_id, WC_Vivawallet_Helper::POST_META_VW_TXN );
			if ( empty( $post_meta ) ) {
				throw new Exception( __( 'The transaction ID for this order could not be found. Something is wrong!', 'woocommerce_vivawallet' ) );
			}
			$payment_transaction_id = $post_meta [0];
			$body                   = array(
				'amount'     => $refund_amount_cents,
				'SourceCode' => $credentials ['source_code'],
			);
			$args                   = array(
				'headers' => array(
					'Authorization' => 'Bearer ' . $credentials ['token'],
					'Content-Type'  => 'application/json',
				),
				'method'  => 'DELETE',
			);
			$url                    = WC_Vivawallet_Helper::get_api_url( $credentials ['test_mode'] ) . '/nativecheckout/v2/transactions/' . $payment_transaction_id;
			$params                 = http_build_query( $body );
			$url                    = $url . '?' . $params;
			$response               = wp_remote_request( $url, $args );
			$refund_id              = json_decode( $response['body'] );
			$response               = $response['response'];
			if ( ! isset( $response['code'] ) ) {
				throw new Exception( __( 'Error connecting to Viva Wallet services. Please try again!', 'woocommerce_vivawallet' ) );
			}
			if ( 200 !== $response['code'] && ! isset( $refund_id->transactionId ) ) {
				throw new Exception( $response['message'] );
			}
			$refund_data = array(
				'refunded_amount'        => $body ['amount'],
				'refund_transaction_id'  => $refund_id->transactionId,
				'payment_transaction_id' => $payment_transaction_id,
			);
			add_post_meta( $order_id, WC_Vivawallet_Helper::POST_META_VW_REFUND_DATA, $refund_data );
			if ( $is_full_refund ) {
				$note = __( 'Full refund was executed on Viva Wallet with ID: ', 'woocommerce_vivawallet' ) . $refund_id->transactionId;
			} else {
				$note = __( 'Partial refund was executed on Viva Wallet with ID: ', 'woocommerce_vivawallet' ) . $refund_id->transactionId;
			}
			$order->add_order_note( $note, false );
			return true;
		} catch ( Exception $e ) {
			return new WP_Error( 'error', $e->getMessage() );
		}

	}

	/**
	 * Can Refund On Vivawallet
	 *
	 * @param string $paid_date paid date.
	 *
	 * @return bool
	 */
	private static function can_refund_on_vivawallet( $paid_date ) {
		$today    = gmdate( 'Ymd' );
		$tomorrow = gmdate( 'Ymd', strtotime( 'tomorrow' ) );
		if ( gmdate( 'Ymd', strtotime( $paid_date ) ) === $today ) {
			return false;
		} elseif ( gmdate( 'Ymd', strtotime( $paid_date ) ) === $tomorrow ) {
			$valid_tomorrow = $paid_date + ( 2 * 60 * 60 );
			$valid_tomorrow = gmdate( 'Y-m-d H:i:s', strtotime( $valid_tomorrow ) );
			$now            = gmdate();
			$now            = gmdate( 'Y-m-d H:i:s', strtotime( $now ) );
			if ( $now < $valid_tomorrow ) {
				return false;
			}
		}
		return true;
	}
}
