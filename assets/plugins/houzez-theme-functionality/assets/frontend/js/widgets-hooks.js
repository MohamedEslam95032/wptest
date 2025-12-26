(function ($) {
    'use strict';

    window.addEventListener('elementor/frontend/init', () => {
        const initListener = ($scope) => {
            const type = $scope.data('widget_type');

            let mapSystem;
            if (typeof houzez_vars !== 'undefined') {
                mapSystem = houzez_vars.houzez_map_system;
            }

            if (!type) return;
            try {
                const [name, skin] = type.split('.');
                switch (name) {
                    case 'houzez-property-section-map':
                        if (mapSystem === 'google') {
                            houzez.SinglePropertyMap.init();
                        } else if (mapSystem === 'mapbox') {
                            houzez.SinglePropertyMapbox.init();
                        } else if (mapSystem === 'osm') {
                            houzez.SinglePropertyOSM.init();
                        } else {
                            console.warn('Unknown map system:', mapSystem);
                        }
                        break;
                    case 'houzez-property-overview-v2':
                        if (mapSystem === 'google') {
                            houzez.SinglePropertyOverviewMap.init();
                        } else if (mapSystem === 'mapbox') {
                            houzez.SinglePropertyOverviewMapbox.init();
                        } else if (mapSystem === 'osm') {
                            houzez.SinglePropertyOverviewOSM.init();
                        }
                        break;
                    // case 'houzez-agent-location-map':
                    // case 'houzez-agency-location-map':
                    //     if (mapSystem === 'google') {
                    //         houzez.SingleAgentMap.init();
                    //     } else if (mapSystem === 'mapbox') {
                    //         houzez.SingleAgentMapbox.init();
                    //     } else if (mapSystem === 'osm') {
                    //         houzez.SingleAgentOSM.init();
                    //     }
                    //     break;
                    case 'houzez_properties_mapbox':
                        houzez.Mapbox.init();
                        break;
                    case 'houzez_properties_google_map':
                        houzez.Maps.init();
                        break;
                    case 'houzez_properties_osm_map':
                        houzez.OSMMap.init();
                        break;
                    case 'houzez_elementor_partners':
                        houzez.Sliders.partnersCarousel();
                        break;

                    case 'houzez_elementor_properties_slider':
                        houzez.Sliders.propertyBannerSlider();
                        break;
                    case 'houzez_properties_tabs':
                        houzez.PropertiesTabs.init();
                        break;
                    case 'houzez_custom_carousel':
                        houzez.Sliders.customCarousel();
                        break;

                    case 'houzez_elementor_testimonials':
                        houzez.Sliders.testimonialsSliderV1();
                        break;
                    case 'houzez_elementor_testimonials_v2':
                        houzez.Sliders.testimonialsSliderV2();
                        break;
                    case 'houzez_elementor_testimonials_v3':
                        houzez.Sliders.testimonialsSliderV3();
                        break;

                    case 'houzez-agent-stats':
                    case 'houzez-agency-stats':
                    case 'houzez-agent-single-stat':
                    case 'houzez-agency-single-stat':
                        houzez.RealtorStats.init();
                        break;

                    case 'houzez-property-detail-gallery-v2':
                    case 'houzez-property-toparea-v3':
                        houzez.Sliders.propertyDetailGallery();
                        break;

                    case 'houzez-property-toparea-v5':
                    case 'houzez-property-detail-gallery-v3':
                        houzez.Sliders.variableWidthSlider();
                        break;

                    case 'houzez-property-section-schedule-tour-v2':
                        houzez.Sliders.propertyScheduleTourDayFormSlide();
                        break;

                    default:
                        return;
                }
            } catch (error) {
                console.warn(
                    'Error in Elementor widget initialization:',
                    error
                );
            }
        };

        elementorFrontend.hooks.addAction(
            'frontend/element_ready/widget',
            initListener
        );
    });
})(jQuery);
