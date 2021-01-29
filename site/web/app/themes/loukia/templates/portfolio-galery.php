<?php if( get_field('portfolio_photos') ) {
echo '<ul class="gallery list-group">';
echo '<div class="grid-sizer"></div>';
$gallery = get_field( 'portfolio_photos' ); // get the gallery object
$i = 0;
$len = count($gallery);
foreach( $gallery as $img ) { // for each item in the gallery object, build a gallery thumbnail
  if ($i == 0) {
    // first
    echo '<li class="gallery-item list-group-item first">';
    echo '<a href="'.$img['url'].'" class="gallery-image img-fluid" data-toggle="lightbox" data-title="'.$img['title'].'">';
    echo '<img src="'.$img['sizes']['medium'].'">';
    echo '</a>';
    echo '</li>';

} else {
    // last
    echo '<li class="gallery-item list-group-item">';
    echo '<a href="'.$img['url'].'" class="gallery-image img-fluid" data-toggle="lightbox" data-title="'.$img['title'].'">';
    echo '<img src="'.$img['sizes']['medium'].'">';
    echo '</a>';
    echo '</li>';

}
// …
$i++;
}
echo '</ul>';
}
