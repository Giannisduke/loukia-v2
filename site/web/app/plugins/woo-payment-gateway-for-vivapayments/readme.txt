=== Viva Payments - Viva Wallet WooCommerce Payment Gateway ===
Tags: woocommerce, payment gateway, payment gateways
Stable tag: 1.3.9
Contributors: enartia,g.georgopoulos,georgekapsalakis
Author URI: https://www.papaki.com
Requires at least: 4.0
Tested up to: 5.5.3
WC tested up to: 4.6.1
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html



== Description ==

Woocommerce Viva Payments - Viva Wallet payment gateway plug-in.

Provides pre-auth transactions and free instalments.

Based on original plugin "Viva Wallet Woocommerce Payment Gateway By emspace.gr" [https://wordpress.org/plugins/woo-payment-gateway-viva-wallet/]


== Installation ==

Just follow the standard [WordPress plugin installation procedure](http://codex.wordpress.org/Managing_Plugins).

And the [instructions](https://plugins.svn.wordpress.org/woo-payment-gateway-for-vivapayments/assets/instructions.pdf)

= Return url for Viva Dashboard =
 
yourdomainname.gr/wc-api/WC_Papaki_Vivapayments_Gateway
 
or 
 
yourdomainname.gr?wc-api=WC_Papaki_Vivapayments_Gateway if permalinks are disabled.

== Frequently asked questions ==
= Does it work? =
Yes

= How =
Just follow the [instructions](https://plugins.svn.wordpress.org/woo-payment-gateway-for-vivapayments/assets/instructions.pdf) to create a demo or live account in Viva dashboard and then use the necessary credentials in the Woocommerce payment gateway options.




== Changelog ==

= 1.3.9 = 
Updated Texts and compatibility with Woocommerce 4.6.1

= 1.3.8 = 
sanitize data
update compatibility with Woocommerce 4.3.0

= 1.3.7 = 
update compatibility with Woocommerce 4.1.0

= 1.3.6 = 
update compatibility with wordpress 5.4 and woocommerce 4.0.1

= 1.3.5 = 
Update translations

= 1.3.4 = 
Update translations
Added option to display or not VivaWallet's logo in checkout page.


= 1.3.3 = 
For downloadable products, don't auto mark the order as completed, unless all the products are downloadable
Fixes an issue with VivaWallet demo environment.
Updated VivaWallet logo in checkout page.


= 1.3.2 = 
Fixes an issue with VivaPayments Apikey 

= 1.3 = 
Redirect to english vivawallet redirect page if language is english.
You can now have instalments either deeping on order total amount or not.


= 1.0.2 =
WooCommerce 3.0 compatible

= 1.0.0 =
Initial Release