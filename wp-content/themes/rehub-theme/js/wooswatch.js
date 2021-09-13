jQuery( '.variations_form' ).on( 'woocommerce_update_variation_values', function () {
    var rhswatches = jQuery('.rh-var-selector');
    rhswatches.find('.rh-var-label').removeClass('rhhidden');
    rhswatches.each(function(){
        var variationselect = jQuery(this).prev();
        jQuery(this).find('.rh-var-label').each(function(){
            if (variationselect.find('option[value="'+ jQuery(this).attr("data-value") +'"]').length <= 0) {
                jQuery(this).addClass('rhhidden');
            }
        });
    });
});
jQuery( '.variations_form' ).on( 'click', '.reset_variations', function () {
    var rhswatches = jQuery('.rh-var-selector');
    rhswatches.find('.rh-var-label').removeClass('rhhidden');
    rhswatches.each(function(){
        jQuery(this).find('.rh-var-input').each(function(){
            jQuery(this).prop( "checked", false );
        });
    });
});