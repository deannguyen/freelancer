<?php

/**
 * Get the text of bid status from status number
 * 
 * @param int $status status number
 * 
 * @author Nguyen Dat
 * @since  1.0.1
 * @return string Status text or false if don't exist the status number in defause array
 */
function bids_get_bid_status($status) {
    //get the bid status
    $arg_bid_status = get_option('bids_post_status');

    //if the key exist in bid status array
    if (array_key_exists($status, $arg_bid_status)) {
        return $arg_bid_status[$status];
    }
    return false;
}
