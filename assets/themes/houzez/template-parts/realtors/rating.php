<?php 
if(is_author()) {
	global $author_id;
	$rating_id = $author_id;
} else {
	$rating_id = get_the_ID();
}
$total_ratings = get_post_meta($rating_id, 'houzez_total_rating', true);

if( empty( $total_ratings ) ) {
	$total_ratings = 0;
}

// Get review count for schema markup - only on single pages
$review_count = 0;
$is_single_page = is_singular(array('houzez_agent', 'houzez_agency'));
if ($is_single_page && function_exists('houzez_reviews_count')) {
	
	$post_type = get_post_type($rating_id);
	if ($post_type === 'houzez_agent') { 
		$review_count = houzez_reviews_count('review_agent_id');
	} elseif ($post_type === 'houzez_agency') {
		$review_count = houzez_reviews_count('review_agency_id');
	}
}

$is_single_realtor = $args['is_single_realtor'] ?? false;

if( $is_single_realtor ) {
	$rating_score_wrap_class = 'rating-score-wrap d-flex align-items-center gap-2 mb-1';
	$star_class = 'star d-flex align-items-center gap-1';
} else {
	$rating_score_wrap_class = 'rating-score-wrap flex-grow-1';
	$star_class = 'star d-flex align-items-center';
}
?>
<div class="<?php echo esc_attr($rating_score_wrap_class); ?>" role="complementary"<?php if ($is_single_page && $review_count > 0 && $total_ratings > 0) { ?> itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating"<?php } ?>>
	<?php if ($is_single_page && $review_count > 0 && $total_ratings > 0) : ?>
		<meta itemprop="ratingValue" content="<?php echo esc_attr( round( (float) $total_ratings, 2 ) ); ?>">
		<meta itemprop="ratingCount" content="<?php echo esc_attr($review_count); ?>">
		<meta itemprop="bestRating" content="5">
		<meta itemprop="worstRating" content="1">
	<?php endif; ?>
	<span class="<?php echo esc_attr($star_class); ?>" role="img">
	    <?php if ( is_singular( array( 'houzez_agent', 'houzez_agency' ) ) && $total_ratings > 0 ) : ?>
		    <span class="rating-score-text"><?php echo esc_attr( round( (float) $total_ratings, 2 ) ); ?></span>
		<?php elseif ( is_author() && $total_ratings > 0 ) : ?>
		    <span class="rating-score-text"><?php echo esc_attr( round( (float) $total_ratings, 2 ) ); ?></span>
		<?php endif; ?>

		<?php echo houzez_get_stars($total_ratings, false); ?>

	    <?php if(is_singular( array('houzez_agent', 'houzez_agency', 'fts_builder') ) || is_author() ) { ?>
	        <a class="all-reviews" href="#review-scroll"><?php echo houzez_option('agency_lb_all_reviews', esc_html__('See all reviews', 'houzez')); ?></a>
	    <?php } ?>
	</span>
</div><!-- rating-score-wrap -->