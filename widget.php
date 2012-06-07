<?php
/**
 * @package EBay Plugins
 */

function widget_ebay_flash_seller_register() {
  function widget_ebay_flash_seller($args) {
      $params = get_option(EBAY_FLASH_SELLER_PLUGIN_VAR_NAME, array());
      if(is_string($params))
        $params = unserialize(base64_decode($params));
    
      if(!is_array($params) || empty($params)){
        echo __("EBay Flash Seller is not properly configured");
        return;
      }
      if(trim($params['sellerID']) == ""){
        echo __("EBay Flash Seller is not properly configured, missing seller id");
        return;
      }
      $content = '<div id="ebay_flash_seller">';

      if(isset($params['intro']) && trim($params['intro']) != "")
        $content .= "<p>".str_replace(array("\r\n", "\n"),"<br>",$params['intro'])."</p>";
   
      $content .=   ebay_flash_seller_generate_javascript($params);
      $content .= '</div>';
      echo $content;
  }

  function ebay_flash_seller_generate_javascript($params) {
    $callUrl = ebay_flash_seller_get_proxy_url();

    $resp = array(
            "requestType"     =>  "EBayFlashSeller",
            "duration_value"  =>  isset($params['duration'])   ? $params['duration']   : "5000",
            "global_id"       =>  isset($params['sourceSite']) ? $params['sourceSite'] : "0",
            "itemType"        =>  isset($params['itemType'])   ? $params['itemType']   : "AllItemTypes",
            "itemSort"        =>  isset($params['itemSort'])   ? $params['itemSort']   : "Bidcount",
            "maxEntries"      =>  isset($params['maxEntries']) ? $params['maxEntries'] : "3",
            "categoryId"      =>  isset($params['categoryId']) ? $params['categoryId'] : "",
            "sellerID"        =>  isset($params['sellerID'])   ? $params['sellerID']   : "",
            "lang"            =>  isset($params['languageEB']) ? $params['languageEB'] : "eng",
            "backStatus"      =>  isset($params['backStatus']) ? $params['backStatus'] : "white",
            "sizeStatus"      =>  isset($params['sizeStatus']) ? $params['sizeStatus'] : "square",
        );

    $bid_sentance = isset($params['bidSentance']) ? $params['bidSentance'] : false;
    $coupon_code  = isset($params['couponcode'])  ? $params['couponcode']  : false;

    if ($coupon_code != false && $coupon_code !== ""){
      $resp['couponcode'] = $coupon_code;
    }
    if ($bid_sentance != false && $bid_sentance !== ""){
      $resp['bid_sentance'] = $bid_sentance;
    }
    $first = true;
    foreach ($resp as $key=>$param){
        if($first){
           $first = false;
           $callUrl .= $key . '=' . $param;
        } else
           $callUrl .= '&' . $key . '=' . $param;
    }

    return '<script type="text/javascript" src="'.$callUrl.'"></script>';
  }

  function widget_ebay_flash_seller_control(){
      $content = "";
      $content .= __('Please configure your widget from');
      $content .= ': <a href="plugins.php?page=ebay-flash-seller-config">';
      $content .= __("here");
      $content .= '</a>';

      echo $content;
  }

  function widget_ebay_flash_seller_include_css(){
      echo '<style type="text/css">'.file_get_contents(EBAY_FLASH_SELLER_PLUGIN_URL."front.css").'</style>';
  }

  if(function_exists('register_sidebar_widget') ){
    if(function_exists('wp_register_sidebar_widget')){
      wp_register_sidebar_widget( 'ebay_flash_seller', 'EBay Flash Seller', 'widget_ebay_flash_seller', null, 'ebay_flash_seller');
      wp_register_widget_control( 'ebay_flash_seller', 'EBay Flash Seller', 'widget_ebay_flash_seller_control', null, 75, 'ebay_flash_seller');
    }elseif(function_exists('register_sidebar_widget')){
      register_sidebar_widget('EBay Flash Seller', 'widget_ebay_flash_seller', null, 'ebay_flash_seller');
      register_widget_control('EBay Flash Seller', 'widget_ebay_flash_seller_control', null, 75, 'ebay_flash_seller');
    }
  }
  
  if(is_active_widget('widget_ebay_flash_seller'))
    add_action('wp_head', 'widget_ebay_flash_seller_include_css');

}

add_action('init', 'widget_ebay_flash_seller_register');

