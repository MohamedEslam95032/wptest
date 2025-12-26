<?php
global $settings, $author_id;
$num_of_review = houzez_option('num_of_review', 5);
$section_title = isset($settings['section_title']) && !empty($settings['section_title']) ? $settings['section_title'] : '';

$meta_key = 'review_agency_id';
$meta_value = get_the_ID();

$args = array(
    'post_type' => 'houzez_reviews',
    'meta_key' => $meta_key,
    'meta_value' => $meta_value,
    'posts_per_page' => $num_of_review,
    'post_status' => 'publish',
);

$review_qry = new WP_Query( $args );

$total_review = $review_qry->found_posts;

$total_pages = $review_qry->max_num_pages;

if($total_review > 1) {
    $review_label = esc_html__('Reviews', 'houzez');
} else {
    $review_label = esc_html__('Review', 'houzez');
}

$total_ratings = get_post_meta(get_the_ID(), 'houzez_total_rating', true);
?>
<div class="property-review-wrap property-review-ele-wrap property-section-wrap mt-5" id="property-review-wrap">
	<div class="block-title-wrap review-title-wrap d-flex align-items-start align-items-md-center justify-content-between flex-column flex-md-row m-0 pb-1 gap-3">
		<div class="d-flex align-items-center gap-3">
			<h2 itemprop="reviewCount"><?php echo esc_attr($total_review); ?> <?php echo esc_attr($review_label); ?></h2>
			<div class="rating-score-wrap flex-grow-1" role="complementary">
				<span class="star d-flex align-items-center" role="img">
					<?php echo houzez_get_stars($total_ratings, false); ?>

					<?php if(!empty($total_ratings)) { ?>
						<span class="star-text star-text-right ms-2">
							(<span itemprop="ratingValue"><?php echo esc_attr(round($total_ratings, 2)); ?></span> <?php esc_html_e('out of', 'houzez'); ?> <span itemprop="bestRating">5</span>)
						</span>
					<?php } ?>
				</span>
			</div>
		</div>
		<div class="d-flex align-items-center gap-3">
			<?php get_template_part('template-parts/reviews/sortby'); ?>
			<a class="btn hz-btn-lreview btn-primary btn-sm" href="#property-review-form"><?php esc_html_e('Leave a Review', 'houzez'); ?></a>
		</div>	
	</div>

	<input type="hidden" name="review_paged" id="review_paged" value="1">
	<input type="hidden" name="total_pages" id="total_pages" value="<?php echo intval($total_pages); ?>">
	
	<ul id="houzez_reviews_container" class="review-list-wrap list-unstyled my-4 p-0" itemprop="review">
		<?php 
        if ( $review_qry->have_posts() ) :
            while ( $review_qry->have_posts() ) : $review_qry->the_post();

                get_template_part('template-parts/reviews/review'); 

            endwhile;
        else:
            get_template_part('template-parts/listing/review', 'none');
        endif;
        wp_reset_postdata();
		

		?>
	</ul>

	<?php if($total_review > $num_of_review) { ?>
	<div class="pagination-wrap">
		<nav>
			<ul class="pagination justify-content-center">
				<li class="page-item">
					<a class="page-link" disabled id="review_prev" aria-label="<?php esc_html_e('Previous', 'houzez'); ?>">
						<i class="houzez-icon icon-arrow-left-1"></i>
					</a>
				</li>
				<li class="page-item">
					<a class="page-link" id="review_next" aria-label="<?php esc_html_e('Next', 'houzez'); ?>">
						<i class="houzez-icon icon-arrow-right-1"></i>
					</a>
				</li>
			</ul><!-- pagination -->
		</nav>
	</div>
	<?php } ?>

	<div class="block-wrap" id="property-review-form">
		<?php if( ! empty($section_title) ) { ?>
		<div class="block-title-wrap">
			<h3><?php echo esc_attr($section_title); ?></h3>
		</div>
		<?php } ?>

		<?php get_template_part('template-parts/reviews/form'); ?>
	</div>
</div>