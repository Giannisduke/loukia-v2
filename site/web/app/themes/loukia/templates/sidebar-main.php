<div class="d-flex flex-column justify-content-between navwrap">
  <div><a class="btn btn-primary btn-sm mail"></a></div>

  <div>
    <div class="d-flex flex-column">
      <div><a href="<?php echo get_permalink( get_option('woocommerce_myaccount_page_id') ); ?>" class="btn btn-primary btn-sm profile"></a></div>
      <div><a href="<?php echo wc_get_cart_url(); ?>" class="btn btn-primary btn-sm cart"></a></div>
    </div>
  </div>
    <div></div>
  <div></div>
</div>
