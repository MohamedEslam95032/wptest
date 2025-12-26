<?php
$agent_address = get_post_meta( get_the_ID(), 'fave_agent_address', true );
if(!empty($agent_address)) {
?>
<address itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
	<i class="houzez-icon icon-pin"></i> <span itemprop="streetAddress"><?php echo esc_html($agent_address); ?></span>
</address>
<?php
}
?>