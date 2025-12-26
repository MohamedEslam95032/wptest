<?php
global $post, $ele_thumbnail_size, $image_size; 

$thumbnail_size = !empty($ele_thumbnail_size) ? $ele_thumbnail_size : $image_size;
?>
<div class="listing-image-wrap">
	<div class="listing-thumb">
		<a <?php houzez_listing_link_target(); ?> href="<?php echo esc_url(get_permalink()); ?>" class="listing-featured-thumb hover-effect image-wrap" role="link">
			<?php
			$featured_img_url = get_the_post_thumbnail_url($post->ID, $thumbnail_size);
		    if( $featured_img_url != '' ) {
		        	echo '<img class="img-fluid" src="'.esc_url($featured_img_url).'" alt="">';
		    }else{
		        houzez_image_placeholder( 'large' );
		    }
			?>
		</a><!-- hover-effect -->
	</div>
</div>