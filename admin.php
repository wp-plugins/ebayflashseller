<?php
add_action('admin_init', 'ebay_flash_seller_admin_init');
add_action('admin_menu', 'ebay_flash_seller_add_sub_menu_page' );

function ebay_flash_seller_admin_init() {
	wp_register_style('ebay_flash_seller_back.css', EBAY_FLASH_SELLER_PLUGIN_URL . 'back.css');
	wp_enqueue_style('ebay_flash_seller_back.css');
}

function ebay_flash_seller_admin_configuration() {

  
  $page_content = "";
  $page_content .= '<div class="ebay_flash_seller">';
  $page_content .=  '<h2>'.__("EBay Flash Seller - Configuration.").'</h2>';

  $data = array();
  if(isset($_POST['submit']) && isset($_POST['ebay_configuration'])){
    $data = $_POST['ebay_configuration'];
    
    update_option(EBAY_FLASH_SELLER_PLUGIN_VAR_NAME, base64_encode(serialize($data)));
    $page_content .= '<div class="announce">'.__("Successfully updated").'</div>';
  } else {
    $data = get_option(EBAY_FLASH_SELLER_PLUGIN_VAR_NAME, array());
    if(is_string($data))
      $data = unserialize(base64_decode($data));
  }

  $page_content .=  ebay_flash_seller_get_form($data);
  $page_content .= "</div>";

  echo $page_content;
}

function ebay_flash_seller_add_sub_menu_page(){
  if ( function_exists('add_submenu_page') )
    add_submenu_page('plugins.php', __('EBay Flash Seller Configuration'), __('EBay Flash Seller'), 'manage_options', 'ebay-flash-seller-config', 'ebay_flash_seller_admin_configuration');
}

function ebay_flash_seller_get_form($form_values = array()){
    // Prevent invalid $_POST .
    if(!is_array($form_values))
      exit(__("Invalid form values"));

    $ebay_source_site = array(
                              "EBAY-US"   => "USA",
                              "EBAY-ENCA" => "Canada",
                              "EBAY-GB"   => "United Kingdom",
                              "EBAY-AU"   => "Australia",
                              "EBAY-AT"   => "Austria",
                              "EBAY-FR"   => "France",
                              "EBAY-DE"   => "Germany",
                              "EBAY-IT"   => "Italy",
                              "EBAY-NL"   => "Netherlands",
                              "EBAY-ES"   => "Spain",
                              "EBAY-CH"   => "Switzerland",
                              "EBAY-IE"   => "Ireland",
                              "EBAY-FRBE" => "Belgium-fr",
                              "EBAY-NLBE" => "Belgium-nl",
                          );

    $ebay_item_type = array(
      "All"             => "All",
      "Auction"         => "Auction",
      "AuctionWithBIN"  => "AuctionWithBIN",
      "FixedPrice"      => "FixedPrice",
      "StoreInventory"  => "StoreInventory",
    );

    $ebay_item_sort = array(
      "EndTimeSoonest"            => "EndTimeSoonest",
      "StartTimeNewest"           => "StartTimeNewest",
      "PricePlusShippingHighest"  => "PricePlusShippingHighest",
      "PricePlusShippingLowest"   => "PricePlusShippingLowest",
      "CurrentPriceHighest"       => "CurrentPriceHighest",
      "BidCountFewest"            => "BidCountFewest",
      "BidCountMost"              => "BidCountMost",
    );

    $ebay_language_eb  = array(
            "eng"=> "En US/UK/AU",
            "deu"=> "German",
            "fra"=> "French",
            "spa"=> "Spanish",
            "ita"=> "Italian",
            "dut"=> "Dutch");
  
    $ebay_back_status  = array(
            "white"       => "White",
            "transparent" => "Transparent");
  
    $ebay_size_status = array(
             "square"       => "Rectangular (vertical: 250x300)",
             "skyscraper"   => "Skyscraper (vertical: 160x500)",
             "leaderboard"  => "Leaderboard (horizontal: 160x500)",
  			 "big_panel"   	=> "Big panel (horizontal: 800x590)",
             "small_panel"	=>	"Small panel (square: 600x590)");


                      

    $ebay_country_source_params = array("0"   => "USA",
                                        "2"   => "Canada",
                                        "3"   => "United Kingdom",
                                        "15"  => "Australia",
                                        "16"  => "Austria",
                                        "71"  => "France",
                                        "77"  => "Germany",
                                        "101" => "Italy",
                                        "146" => "Netherlands",
                                        "186" => "Spain",
                                        "193" => "Switzerland",
                                        "205" => "Ireland",
                                        "23"  => "Belgium-fr",
                                        "123" => "Belgium-nl"
                                  );
  
    $ret = "";
    $ret .= '<form class="ebay_flash_seller" method="post">';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("SellerID").'</label>';
    $ret .= ' <input type="text" value="%%%sellerID%%%" name="ebay_configuration[sellerID]">';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Ebay country source").'</label>';
    $ret .=   ebay_flash_seller_generateSelectFromArray($ebay_source_site, 'ebay_configuration[sourceSite]', isset($form_values['sourceSite']) ? $form_values['sourceSite'] : "");
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Maximum items").'</label>';
    $ret .=   '<input type="text" value="%%%maxEntries%%%" name="ebay_configuration[maxEntries]">';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Type of items").'</label>';
    $ret .=   ebay_flash_seller_generateSelectFromArray($ebay_item_type, 'ebay_configuration[itemType]', isset($form_values['itemType']) ? $form_values['itemType'] : "");
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Category ID from eBay (optional)").'</label>';
    $ret .=   '<input type="text" value="%%%categoryId%%%" name="ebay_configuration[categoryId]">';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Enter your intro text").'</label>';
    $ret .=   '<textarea name="ebay_configuration[intro]">'."%%%intro%%%".'</textarea>';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Sort order").'</label>';
    $ret .=   ebay_flash_seller_generateSelectFromArray($ebay_item_sort, 'ebay_configuration[itemSort]', isset($form_values['itemSort']) ? $form_values['itemSort'] : "");
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Enter slideshow duration in ms").'</label>';
    $ret .=   '<input type="text" value="%%%duration%%%" name="ebay_configuration[duration]">';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Text to replace Bid now").'</label>';
    $ret .=   '<input type="text" value="%%%bidSentance%%%" name="ebay_configuration[bidSentance]">';
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Language used for display").'</label>';
    $ret .=   ebay_flash_seller_generateSelectFromArray($ebay_language_eb, 'ebay_configuration[languageEB]', isset($form_values['languageEB']) ? $form_values['languageEB'] : "");
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Background").'</label>';
    $ret .=   ebay_flash_seller_generateSelectFromArray($ebay_back_status, 'ebay_configuration[backStatus]', isset($form_values['backStatus']) ? $form_values['backStatus'] : "");
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Display size").'</label>';
    $ret .=   ebay_flash_seller_generateSelectFromArray($ebay_size_status, 'ebay_configuration[sizeStatus]', isset($form_values['sizeStatus']) ? $form_values['sizeStatus'] : "");
    $ret .= '</div>';
    $ret .= '<div class="field">';
    $ret .=   '<label>'.__("Special Coupon code (optional)").'</label>';
    $ret .=   '<input type="text" value="%%%couponcode%%%" name="ebay_configuration[couponcode]">';
    $ret .= '</div>';
    $ret .= '<div class="clear"></div>';
    $ret .= '<input type="submit" name="submit" value="Save"/>';
    $ret .= '</form>';

    $ret = str_replace("%%%sellerID%%%", isset($form_values['sellerID']) ? $form_values['sellerID'] : "", $ret);
    $ret = str_replace("%%%maxEntries%%%", isset($form_values['maxEntries']) ? $form_values['maxEntries'] : "", $ret);
    $ret = str_replace("%%%categoryId%%%", isset($form_values['categoryId']) ? $form_values['categoryId'] : "", $ret);
    $ret = str_replace("%%%intro%%%", isset($form_values['intro']) ? $form_values['intro'] : "", $ret);
    $ret = str_replace("%%%duration%%%", isset($form_values['duration']) ? $form_values['duration'] : "5000", $ret);
    $ret = str_replace("%%%bidSentance%%%", isset($form_values['bidSentance']) ? $form_values['bidSentance'] : "", $ret);
    $ret = str_replace("%%%couponcode%%%", isset($form_values['couponcode']) ? $form_values['couponcode'] : "", $ret);

    return $ret;
}

function ebay_flash_seller_generateSelectFromArray($options , $select_name , $selected_option = null){
    $return = "";
    $return .= '<select id="'.$select_name.'" name="'.$select_name.'">';
    foreach($options as $value=>$name){
        $return .= '<option value="'.$value.'"';

        if($value == $selected_option)
            $return .= 'selected="selected"';

        $return .= '>'.$name.'</option>';
    }
    $return .= '</select>';

    return $return;
}
