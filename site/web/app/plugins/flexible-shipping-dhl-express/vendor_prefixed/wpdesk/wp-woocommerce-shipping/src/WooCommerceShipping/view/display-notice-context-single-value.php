<?php

namespace DhlVendor;

/**
 * @var string $label
 * @var string $value
 */
?><br/>
<div class="flexible-shipping-log">
	<button class="small show"><?php 
echo \sprintf(\__('Show %1$s', 'flexible-shipping-dhl-express'), \ucfirst($label));
?></button>
	<button class="small hide"><?php 
echo \sprintf(\__('Hide %1$s', 'flexible-shipping-dhl-express'), \ucfirst($label));
?></button>
	<button class="small clipboard"><?php 
echo \sprintf(\__('Copy %1$s to clipboard', 'flexible-shipping-dhl-express'), \ucfirst($label));
?></button>
	<pre><?php 
echo \esc_html($value);
?> </pre>
</div>
<?php 
