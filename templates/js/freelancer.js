jQuery(document).ready(function($) {
    jQuery('#bids_area_city').change(function() {
        var bids_area_city = jQuery(this).val();
        jQuery('#bid_area > li').hide();
        jQuery('#bid_area #bid_area-' + bids_area_city).show();
    });
    
    jQuery('#newbidform').validate();
});