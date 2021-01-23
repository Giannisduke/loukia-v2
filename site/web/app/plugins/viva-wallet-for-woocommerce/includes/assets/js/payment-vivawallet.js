/**
 * Viva Wallet for WooCommerce
 *
 * Copyright: (c) 2020 VivaWallet.com
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package WC_Vivawallet
 */

jQuery(
	function ($) {

		var maxInstallments = 0;
		var requiresCvv     = false;

		var maxInstallmentsFromAdmin = vivawallet_params.maxInstallments;

		var allowInstallments = vivawallet_params.allowInstallments;

		var default_payment_jquery_cards = [];

		function submit_error(error_message){
			$( '.woocommerce-NoticeGroup-checkout, .woocommerce-error, .woocommerce-message' ).remove();
			checkout_form.prepend( '<div class="woocommerce-NoticeGroup woocommerce-NoticeGroup-checkout">' + error_message + '</div>' ); // eslint-disable-line max-len.
			checkout_form.removeClass( 'processing' ).unblock();
			checkout_form.find( '.input-text, select, input:checkbox' ).trigger( 'validate' ).blur();
			scroll_to_notices();
			function scroll_to_notices(){
				var scrollElement = $( '.woocommerce-NoticeGroup-updateOrderReview, .woocommerce-NoticeGroup-checkout' );
				if ( ! scrollElement.length ) {
					scrollElement = $( '.form.checkout' );
				}
				if ( scrollElement.length ) {
					$( 'html, body' ).animate(
						{
							scrollTop: ( scrollElement.offset().top - 100 )
						},
						1000
					);
				}
			}
			$( document.body ).trigger( 'checkout_error' );
		}

		function ajaxCallCheckout( successAction, key  ){

			$( '#VWloader' ).css( {"display":"flex"} );

			var data = $( ".woocommerce-checkout" ).serialize();

			if ( typeof 'undefined' !== key && null !== key ) {
				data += '&' + key + '=true';
			}
			$.ajax(
				{
					url:  window.location.origin + '/?wc-ajax=checkout', // (use that wc-ajax=checkout url to send via post the data of the Checkout form fields and then woocommerce will put the order as complete)
					type: "POST",
					data: data,
					success: function( data ) {
						$( '#VWloader' ).hide();
						if (data.resultApi === 'success') {
							successAction();
						}
						if (data.result === 'failure') {
							submit_error( data.messages );
						}
					},
					error: function() {
						$( '#VWloader' ).hide();
						submit_error( '<ul class="woocommerce-error" role="alert"><li>' + vivawallet_params.labelForAJAXerror + '</li></ul>' );
					}
				}
			);
		}

		var afterWooSuccessFormSubmit = function (){
			// woo form passed validation..
			// check for cc details.
			if ( false === checkCreditCardFields() ) {
				submit_error( '<ul class="woocommerce-error" role="alert"><li>' + vivawallet_params.labelForCCerror + '</li></ul>' );
			} else {

				// if card details are ok, proceed to 3d secure.
				VivaPayments.cards.setup(
					{
						authToken: vivawallet_params.token,
						baseURL: vivawallet_params.scriptUrl,
						cardHolderAuthOptions: {
							cardHolderAuthPlaceholderId: 'VWpaymentContainer',
							cardHolderAuthInitiated: function () {
								$( '#VWsecureModal' ).css( {"display":"flex"} )
								$( '#VWloader' ).css( {"display":"flex"} )
							},
							cardHolderAuthFinished: function () {
								$( '#VWsecureModal' ).hide();
								$( '#VWloader' ).hide();
							}
						}
					}
				);

				VivaPayments.cards.requestToken(
					{
						amount:  Number( vivawallet_params.amount ) * 100, // amount is in currency's minor unit of measurement.
						installments: installments,
					}
				).done(
					function ( responseData ) {

						$( 'input[data-vp="chargeToken"]' ).val( responseData.chargeToken );
						$( '#VWloader' ).css( {"display":"flex"} )
						ajaxCallCheckout( afterSuccessFormCreditCard, 'testCCForm' )
					}
				).fail(
					function ( responseData ) {
						console.log( 'Here is the reason it failed: ' + responseData.Error.toString() );
						console.dir( responseData );
					}
				);
			}
		}

		var afterSuccessFormCreditCard = function (){
			$( '#VWloader' ).css( {"display":"flex"} );
			checkout_form.off( 'checkout_place_order', tokenRequest );
			checkout_form.submit();
		}

		var tokenRequest = function (e) {

			// fix values to send.
			var firstNameVal = $( '#billing_first_name' ).val().trim();
			var lastNameVal  = $( '#billing_last_name' ).val().trim();

			installments = $( "#drpInstallments" ).val();

			if ( isNaN( installments ) || 1 >= installments || installments === null) {
					installments = "1";
			}

			$( 'input[data-vp="cardholder"]' ).val( firstNameVal + ' ' + lastNameVal );
			$( 'input[data-vp="accessToken"]' ).val( vivawallet_params.token );
			$( 'input[data-vp="installments"]' ).val( installments );

			ajaxCallCheckout( afterWooSuccessFormSubmit, 'testWooForm' );

			return false;

		};

		var changeFormInputs = function (e) {

			checkCreditCardFields();
			if ( 'vivawallet_native-card-number' !== e.target.id ) {
				return;
			}

			var cardInput = $( 'input[data-vp="cardnumber"]' ).val();
			cardInput     = cardInput.replace( / /g, "" );

			if ( validCard && cardNumber !== cardInput ) { // check the old card input.. only call ajax when it is a valid card and the card input has changed.
				cardNumber = cardInput;

				$.ajax(
					{
						type: "GET",
						beforeSend: function(xhr){
							xhr.setRequestHeader( 'CardNumber', cardNumber );
							xhr.setRequestHeader( 'Authorization', 'Bearer ' + vivawallet_params.token );
							xhr.setRequestHeader( 'Content-Type', 'application/json' );
						},
						url: vivawallet_params.installmentsUrl ,
						success: function ( data ) {
							if ( true === data.requiresCvv ) {
								$( "#vivawallet_native-card-cvc" ).show();
								$( "#wc-vivawallet_native-cc-form label[for=vivawallet_native-card-cvc]" ).show();
								requiresCvv = true;
							} else {
								$( "#vivawallet_native-card-cvc" ).hide();
								$( "#wc-vivawallet_native-cc-form label[for=vivawallet_native-card-cvc]" ).hide();
								requiresCvv = false;
							}
							maxInstallments = data.maxInstallments; // get values from admin settings.
							if ( undefined !== maxInstallments && 1 < maxInstallments && "1" === allowInstallments ) { // first check admin values.
								if ( maxInstallments > maxInstallmentsFromAdmin ) {
									maxInstallments = maxInstallmentsFromAdmin;
								}
								if ( 1 < maxInstallments  ) {
									showInstallments( maxInstallments )
								} else {
									hideInstallments();
								}
							} else {
								hideInstallments();
							}
						},
						error: function ( data) {
							console.error( "Connection to Viva Wallet API Failed" )
							console.log( data );
							submit_error( '<ul class="woocommerce-error" role="alert"><li>' + vivawallet_params.labelForAPIerror + '</li></ul>' );
						}
					}
				);

			}
		};

		function checkCreditCardFields(){

			var $cardInput    = $( "#vivawallet_native-card-number" );
			var $expDateInput = $( "#vivawallet_native-card-expiry" );
			var $cvvInput     = $( "#vivawallet_native-card-cvc" );

			$cardInput.parent().removeClass( "woocommerce-invalid woocommerce-invalid-required-field" );
			$expDateInput.parent().removeClass( "woocommerce-invalid woocommerce-invalid-required-field" );
			$cvvInput.parent().removeClass( "woocommerce-invalid woocommerce-invalid-required-field" );

			var cardInput = $( 'input[data-vp="cardnumber"]' ).val();
			validCard     = $.payment.validateCardNumber( cardInput );

			var res = true;

			if ( ! validCard ) {
				$cardInput.parent().addClass( "woocommerce-invalid woocommerce-invalid-required-field" );
				res = false;
			}

			// check expdate input.
			var expDate = $( 'input[data-vp="expdate"]' ).val();
			expDate     = $.payment.cardExpiryVal( expDate );

			var validExp = $.payment.validateCardExpiry( expDate );
			if ( ! validExp) {
				$expDateInput.parent().addClass( "woocommerce-invalid woocommerce-invalid-required-field" );
				res = false;
			}

			// check cvv input.
			if ( requiresCvv ) {
				var validCVC = $.payment.validateCardCVC( $( 'input[data-vp="cvv"]' ).val() );
				if ( ! validCVC ) {
					$cvvInput.parent().addClass( "woocommerce-invalid woocommerce-invalid-required-field" );
					res = false;
				}
			}

			return res;
		}

		function changePaymentCardsData( target ){
			if ( $.payment === undefined ) {
				console.warn( 'VivaPayments: jquery.payments.js is required but not found on page load. Please  update your WooCommerce plugin.' )
				return;
			}
			var ln = $.payment.cards.length;

			var ln2 = target.length;

			for ( var x = ln; x > 0; x-- ) {
				$.payment.cards.splice( x - 1, 1 );
			}

			// add from target table.
			for ( var y = 0; y < ln2; y++ ) {
				$.payment.cards.push( target[y] );
			}

		}

		function showInstallments ( maxInstallments ) {
			var $drpInstallments = $( '#drpInstallments' );
			$drpInstallments.show();
			$( "#wc-vivawallet_native-cc-form label[for=drpInstallments]" ).show();
			for ( var i = 1; i <= maxInstallments; i++ ) {
				var label = i;
				if ( label === 1 ) {
					label = '0';
				}
				if ( i <= maxInstallments ) {
					$drpInstallments.append( $( "<option>" ).val( i ).text( label ) );
				}
			}
		}

		function hideInstallments () {
			$( '#drpInstallments' ).hide();
			$( "#wc-vivawallet_native-cc-form label[for=drpInstallments]" ).hide();
		}

		function init() {

			// remove event listeners if already there.
			// and add anew.

			checkout_form.off( 'checkout_place_order', tokenRequest );
			checkout_form.off( 'blur change', changeFormInputs );

			checkout_form.on( 'checkout_place_order', tokenRequest );
			checkout_form.on( 'blur change', changeFormInputs );

			// store the old values in a var.
			if ( default_payment_jquery_cards.length === 0 ) {
				var ln = $.payment.cards.length;
				for ( var x = 0; x < ln; x++ ) {
					default_payment_jquery_cards.push( $.payment.cards[x] );
				}
			}
			changePaymentCardsData( VW_cards );

			// inject helper elements.

			if ( 0 === $( '#VWinstallments' ).length ) {

				var res = '<p class="form-row form-row-wide" id="VWinstallments">';
				res    += '<label for="drpInstallments">';
				res    += 'Installments <span class="required">*</span>';
				res    += '<select id="drpInstallments" name="drpInstallments"></select>';
				res    += '</label>';
				res    += '</p>';

				$( '#wc-vivawallet_native-cc-form' ).append( res );
			}

			if ( 0 === $( '#VWhiddenFields' ).length  ) {
				var res = '<div id="VWhiddenFields" style="clear: both">';
				res    += '<input type="hidden" data-vp="cardholder" placeholder="cardholder name" name="txtCardHolder" />';
				res    += '<input type="hidden" data-vp="accessToken" placeholder="card access token" name="accessToken" autocomplete="off"/>';
				res    += '<input type="hidden" data-vp="chargeToken" placeholder="card charge token" name="chargeToken" autocomplete="off"/>';
				res    += '<input type="hidden" data-vp="installments" placeholder="installments" name="installments" autocomplete="off"/>';
				res    += '</div>';

				$( '#wc-vivawallet_native-cc-form' ).append( res );
			}

			if ( vivawallet_params.showVWLogo && 0 === $( '#VWlogoContainer' ).length  ) {
				var res = '<div class="VWLogoContainer" style="clear: both" id="VWlogoContainer">';
				res    += '<p>';
				res    += vivawallet_params.labelLogoTxt;
				res    += '<a href="https://www.vivawallet.com/" target="_blank"><img src="' + vivawallet_params.logoPath + '"></a>';
				res    += '</p>';
				res    += '</div>';

				$( '#wc-vivawallet_native-cc-form' ).append( res );
			}

			if ( 0 === $( '#VWsecureModal' ).length  ) {
				var res = '<div id="VWsecureModal">';
				res    += '<div id="VWpaymentContainer">';
				res    += '</div>';
				res    += '</div>';

				$( 'body' ).append( res );
			}

			if ( 0 === $( '#VWloader' ).length  ) {
				var res = '<div id="VWloader">';
				res    += '<span><i></i></span>';
				res    += '<div>';
				res    += '<p>';
				res    += vivawallet_params.labelForLoader;
				res    += '</p>';
				res    += '</div>';
				res    += '</div>';

				$( '#wc-vivawallet_native-cc-form' ).append( res );
			}

		}

		function destroy () {
			// remove event listeners.
			checkout_form.off( 'checkout_place_order', tokenRequest );
			checkout_form.off( 'blur change', changeFormInputs );

			// remove injected elements.
			changePaymentCardsData( default_payment_jquery_cards );
			var vivawalletFormDiv = $( '#wc-vivawallet_native-cc-form' );
			vivawalletFormDiv.find( '#VWinstallments' ).remove();
			vivawalletFormDiv.find( '#VWhiddenFields' ).remove();
			vivawalletFormDiv.find( '#VWlogoContainer' ).remove();
			vivawalletFormDiv.find( '#VWsecureModal' ).remove();
			vivawalletFormDiv.find( '#VWloader' ).remove();

		}

		function checkStatus(){
			if ( $( "input#payment_method_vivawallet_native" ).is( ':checked' ) ) {
				init();
			} else {
				destroy();
			}
		}

		var defaultFormat = /(\d{1,4})/g;

		var validCard;
		var cardNumber = '';
		var installments;

		let VW_cards = [
		{
			type: 'maestro',
			patterns: [5018, 502, 503, 506, 56, 58, 606005, 639, 6220, 67 ],
			format: defaultFormat,
			length: [12, 13, 14, 15, 16, 17, 18, 19],
			cvcLength: [3],
			luhn: true
		},
		{
			type: 'forbrugsforeningen',
			patterns: [600],
			format: defaultFormat,
			length: [16],
			cvcLength: [3],
			luhn: true
		},
		{
			type: 'dankort',
			patterns: [5019],
			format: defaultFormat,
			length: [16],
			cvcLength: [3],
			luhn: true
		},
		{
			type: 'visa',
			patterns: [4],
			format: defaultFormat,
			length: [13, 16, 19],
			cvcLength: [3],
			luhn: true
		},
		{
			type: 'mastercard',
			patterns: [5, 51, 52, 53, 54, 55, 59, 22, 23, 24, 25, 26, 27],
			format: defaultFormat,
			length: [16, 18, 19],
			cvcLength: [3],
			luhn: true
		},
		{
			type: 'amex',
			patterns: [34, 37],
			format: defaultFormat,
			length: [15],
			cvcLength: [3, 4],
			luhn: true
		},
		{
			type: 'dinersclub',
			patterns: [30, 36, 38, 39],
			format: defaultFormat,
			length: [14],
			cvcLength: [3],
			luhn: true
		},
		{
			type: 'discover',
			patterns: [6011],
			format: defaultFormat,
			length: [16],
			cvcLength: [3],
			luhn: true
		},
		{
			type: 'unionpay',
			patterns: [62, 88],
			format: defaultFormat,
			length: [16, 17, 18, 19],
			cvcLength: [3],
			luhn: false
		},
		{
			type: 'jcb',
			patterns: [35],
			format: defaultFormat,
			length: [16],
			cvcLength: [3],
			luhn: true
		}
		];

		var checkout_form = $( 'form.woocommerce-checkout' );

		$( checkStatus );// on load check status.

		checkout_form.on(
			'change',
			function (e){
				checkStatus();
			}
		);
	}
);
