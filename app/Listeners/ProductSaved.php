<?php

namespace Solunes\Product\App\Listeners;

class ProductSaved
{

    /**
     * Handle the event.
     *
     * @param  PodcastWasPurchased  $event
     * @return void
     */
    public function handle($event) {
        if(!$product_bridge = \Solunes\Business\App\ProductBridge::where('product_type','product')->where('product_id', $event->id)->first()){
            $product_bridge = new \Solunes\Business\App\ProductBridge;
            $product_bridge->product_type = 'product';
            $product_bridge->product_id = $event->id;
        }
        if($event->currency_id){
            $product_bridge->currency_id = $event->currency_id;
        } else {
            $product_bridge->currency_id = 1;
        }
        $product_bridge->category_id = $event->category_id;
        $product_bridge->price = $event->price;
        $product_bridge->name = $event->name;
        $product_bridge->content = $event->description;
        $image = \Asset::get_image_path('product-image','normal',$event->image);
        $product_bridge->image = \Asset::upload_image(asset($image),'product-bridge-image');
        $product_bridge->content = $event->description;
        $product_bridge->delivery_type = $event->delivery_type;
        if(config('product.product_agency')){
            $product_bridge->agency_id = $event->agency_id;
        }
        if(config('product.seller_user')){
            $product_bridge->seller_user_id = $event->seller_user_id;
        }
        if(config('product.product_url')){
            $product_bridge->product_url = $event->product_url;
        }
        if(config('product.sold_content')){
            $product_bridge->sold_content = $event->sold_content;
        }
        $product_bridge->active = $event->active;
        if(config('payments.sfv_version')>1||config('payments.discounts')){
            $product_bridge->discount_price = $event->discount_price;
        }
        if(config('payments.sfv_version')>1){
            $product_bridge->economic_sin_activity = $event->economic_sin_activity;
            $product_bridge->product_sin_code = $event->product_sin_code;
            $product_bridge->product_internal_code = $event->product_internal_code;
            $product_bridge->product_serial_number = $event->product_serial_number;
        }
        if(config('solunes.inventory')){
            $product_bridge->stockable = $event->stockable;
            if(config('inventory.basic_inventory')){
                $product_bridge->quantity = $event->quantity;
            }
        }
        if(config('customer.credit_wallet_points')){
            $product_bridge->points_active = $event->points_active;
            $product_bridge->points_price = $event->points_price;
        }
        $product_bridge->save();
        if($product_bridge&&!$event->product_bridge_id){
            $event->product_bridge_id = $product_bridge->id;
            $event->save();
        }
        if(config('solunes.inventory')&&!config('inventory.basic_inventory')&&$event->stockable==1){
            $added_variations = 0;
            $agencies = \Solunes\Business\App\Agency::where('stockable', 1)->get();
            foreach($agencies as $agency){
                \Inventory::increase_inventory($agency, $product_bridge, 0);
            }
        }
        return $event;
    }
}

