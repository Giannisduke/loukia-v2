<div class="d-flex flex-row bd-highlight mb-3">
  <div class="pr-2"><time class="updated" datetime="<?= get_post_time('c', true); ?>"><?= get_the_date(); ?></time></div>

    <?php $categories = get_the_category(); ?>
  <?php   $separator = '.  ';
    $output = '';
    if ( ! empty( $categories ) ) { ?>
      <div class="px-2 bd-highlight">|</div>
      <div class="px-2 bd-highlight">
      <?php echo '<ul class="list-group list-group-horizontal">';
        foreach( $categories as $category ) {
            $output .= '<li class="list-group-item"><a href="' . esc_url( get_category_link( $category->term_id ) ) . '" alt="' . esc_attr( sprintf( __( 'View all posts in %s', 'textdomain' ), $category->name ) ) . '">' . esc_html( $category->name ) . '</a></li>' . $separator;
        }
        echo trim( $output, $separator );
        echo '</ul>';
        ?>
      </div>
    <?php } ?>

    <?php
        $rows = get_field('portfolio_credits');
        $separator = ',  ';
          $output = '';
if($rows)
{
    echo '<div class="px-2 bd-highlight">|</div>
    <div class="px-2 bd-highlight">
    <ul class="list-group list-group-horizontal"><span>Credits:</span>';

    foreach($rows as $row)
    {
        $output .= '<li class="list-group-item"><a href="'. $row['credit_link'] .'" alt="'. $row['credit_name'] .'">' . $row['credit_name'] . '</a></li>'. $separator;
    }
    echo trim( $output, $separator );
    echo '</ul></div>';
}
     ?>
</div>
