<?php 
$agency_address = get_post_meta( get_the_ID(), 'fave_agency_address', true );
if(!empty($agency_address)) {
	echo '<address class="mb-1" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress"><i class="houzez-icon icon-pin"></i> <span itemprop="streetAddress">'.$agency_address.'</span></address>';
}