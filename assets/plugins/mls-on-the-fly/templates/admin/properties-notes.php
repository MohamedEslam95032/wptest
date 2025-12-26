<h2><?php esc_html_e('Properties Notes', 'realtyna-mls-on-the-fly'); ?></h2>

<form method="post" action="<?php echo esc_url(admin_url('admin-post.php?page=mls-on-the-fly-properties-notes')); ?>" class="properties-notes-filter-form">
    <?php wp_nonce_field('mls_on_the_fly_properties_notes'); ?>
    <input type="hidden" name="action" value="mls_on_the_fly_save_properties_notes">

    <label for="filter_field"><strong><?php esc_html_e('Filter Field:', 'realtyna-mls-on-the-fly'); ?></strong></label>
    <select name="filter_field" id="filter_field">
        <option value="ListAgentMlsId" <?php selected($field, 'ListAgentMlsId'); ?>>ListAgentMlsId</option>
        <option value="ListOfficeKey" <?php selected($field, 'ListOfficeKey'); ?>>ListOfficeKey</option>
        <option value="ListOfficeMlsId" <?php selected($field, 'ListOfficeMlsId'); ?>>ListOfficeMlsId</option>
    </select>

    <label for="filter_value"><strong><?php esc_html_e('Value:', 'realtyna-mls-on-the-fly'); ?></strong></label>
    <input type="text" name="filter_value" id="filter_value" value="<?php echo esc_attr($value); ?>"/>

    <button type="submit" class="button button-primary"><?php esc_html_e('Save', 'realtyna-mls-on-the-fly'); ?></button>
</form>

<?php if (!empty($properties)) : ?>
    <div class="properties-notes-wrapper">
        <h3><?php esc_html_e('Matched Properties', 'realtyna-mls-on-the-fly'); ?></h3>

        <div class="properties-notes-list">
            <?php
            /** @var array $properties */
            foreach ($properties as $property) : ?>
                <?php
                $thumb = is_array($property->Media) && isset($property->Media[0]['Thumbnail']) ? $property->Media[0]['Thumbnail'] : '';
                $updated_at = '';

                if (!empty($property->RFModificationTimestamp)) {
                    try {
                        $dt = new DateTime($property->RFModificationTimestamp);
                        $updated_at = $dt->format('M j, Y, g:i A');
                    } catch (Exception $e) {
                        $updated_at = $property->RFModificationTimestamp;
                    }
                }

                $listingKey = $property->ListingKey ?? '';
                $additionalInfo = $RFPropertyAdditionalInfos[$listingKey]->AdditionalInfo ?? [];
                $additionalInfoKey = $RFPropertyAdditionalInfos[$listingKey]->PropertyAdditionalInfoKey ?? '';
                ?>
                <div class="property-note-card">
                    <div class="property-note-left">
                        <?php if ($thumb): ?>
                            <img src="<?php echo esc_url($thumb); ?>" alt="Property Image"/>
                        <?php endif; ?>

                        <p><strong>Listing ID:</strong> <?php echo esc_html($property->ListingId ?? ''); ?></p>
                        <p><strong>Listing Key:</strong> <?php echo esc_html($listingKey); ?></p>
                        <p><strong>Address:</strong> <?php echo esc_html($property->UnparsedAddress ?? ''); ?></p>
                        <p><strong>Price:</strong> <?php echo esc_html($property->ListPrice ?? ''); ?></p>
                        <p><strong>Location:</strong> <?php echo esc_html("{$property->City}, {$property->StateOrProvince} {$property->PostalCode}"); ?></p>
                        <p><strong>Updated At:</strong> <?php echo esc_html($updated_at); ?></p>
                    </div>

                    <div class="property-note-right">
                        <?php if (!empty($additionalInfo)) : ?>
                            <table class="property-notes-table">
                                <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Value</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($additionalInfo as $key => $val): ?>
                                    <tr>
                                        <td>
                                            <?php
                                            echo match ($key) {
                                                'phoneNumber' => 'Phone Number',
                                                'age'         => 'Age',
                                                'name'        => 'Name',
                                                default       => esc_html($key)
                                            };
                                            ?>
                                        </td>
                                        <td><?php echo esc_html($val); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>

                            <button class="button button-secondary edit-notes-btn"
                                    data-originating-system-name="<?php echo esc_attr($property->OriginatingSystemName); ?>"
                                    data-listing-key="<?php echo esc_attr($property->ListingKey); ?>"
                                    data-property-additional-info-phone-number="<?php echo esc_attr($additionalInfo['phoneNumber']); ?>"
                                    data-property-additional-info-age="<?php echo esc_attr($additionalInfo['age']); ?>"
                                    data-property-additional-info-name="<?php echo esc_attr($additionalInfo['name']); ?>"
                                    data-property-additional-info-key="<?php echo esc_attr($additionalInfoKey); ?>">
                                <?php esc_html_e('Edit Notes', 'realtyna-mls-on-the-fly'); ?>
                            </button>
                            <button class="button button-secondary delete-notes-btn"
                                    data-originating-system-name="<?php echo esc_attr($property->OriginatingSystemName); ?>"
                                    data-listing-key="<?php echo esc_attr($property->ListingKey); ?>"
                                    data-property-additional-info-key="<?php echo esc_attr($additionalInfoKey); ?>"
                                    data-nonce="<?php echo esc_attr(wp_create_nonce('delete_property_notes_' . $property->ListingKey)); ?>">
                                <?php esc_html_e('Delete Notes', 'realtyna-mls-on-the-fly'); ?>
                            </button>

                        <?php else: ?>
                            <button class="button button-secondary edit-notes-btn"
                                    data-originating-system-name="<?php echo esc_attr($property->OriginatingSystemName); ?>"
                                    data-listing-key="<?php echo esc_attr($property->ListingKey); ?>">
                                <?php esc_html_e('Add Notes', 'realtyna-mls-on-the-fly'); ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php
        /** @var int $totalProperties */
        /** @var int $per_page */
        /** @var int $paged */
        if (($total_pages = ceil($totalProperties / $per_page)) > 1) : ?>
            <div class="properties-pagination">
                <div class="tablenav-pages">
                    <?php for ($i = 1; $i <= $total_pages; $i++) :
                        $class = $paged == $i ? 'page-numbers current' : 'page-numbers';
                        $url = esc_url(add_query_arg(['paged' => $i]));
                        ?>
                        <a class="<?php echo $class; ?>" href="<?php echo $url; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Modal -->
<div id="edit-notes-modal" class="edit-notes-modal-wrapper" style="display: none;">
    <div class="edit-notes-modal">
        <h3><?php esc_html_e('Edit Notes', 'realtyna'); ?></h3>
        <form id="edit-notes-form">
            <div class="note-rows">
                <div class="note-row">
                    <label for="note-phoneNumber-value">Phone Number:
                        <input type="tel" name="notes[phoneNumber]" id="property-additional-info-phone-number-value" placeholder="Value">
                    </label>
                </div>
                <div class="note-row">
                    <label for="note-age-value">Age:
                        <input type="number" name="notes[age]" id="property-additional-info-age-value" placeholder="Value">
                    </label>
                </div>
                <div class="note-row">
                    <label for="note-name-value">Name:
                        <input type="text" name="notes[name]" id="property-additional-info-name-value" placeholder="Value">
                    </label>
                </div>
            </div>

            <input type="hidden" name="listing_key" id="edit-notes-listing-key">
            <input type="hidden" name="originating_system_name" id="edit-notes-originating-system">
            <input type="hidden" name="property_additional_info_key" id="edit-notes-property-additional-info-key">
            <input type="hidden" name="action" value="save_property_notes">
            <?php wp_nonce_field('save_property_notes_nonce', 'security'); ?>

            <div style="margin-top: 15px;">
                <button type="submit" class="button button-primary"><?php esc_html_e('Save Notes', 'realtyna'); ?></button>
                <button type="button" class="button close-modal"><?php esc_html_e('Cancel', 'realtyna'); ?></button>
            </div>
        </form>
    </div>
    <div class="modal-backdrop"></div>
</div>
