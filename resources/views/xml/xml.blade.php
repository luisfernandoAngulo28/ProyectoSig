<?php

$xml = <<<XML
<?xml version='1.0' encoding="UTF-8"?>
    <rss xmlns:g="http://base.google.com/ns/1.0" version="2.0"></rss>
XML;

$rss = new SimpleXMLElement($xml);
$channel = $rss->addChild('channel');
$channel->addChild('title', 'La Ganga');
$channel->addChild('description', 'Tienda online de La Ganga');
  foreach ($products as $product) {

    if($product->category){
        $brand = $product->category->name;
    } else {
        $brand = 'Sin categoría';
    }
    $item = $channel->addChild('item');
    $item->addChild('g:id', $product->id );
    $item->addChild('g:title', ucfirst(mb_strtolower($product->name, 'UTF-8')));
    $item->addChild('g:description', mb_strtolower(preg_replace('/[^A-Za-z0-9 \-]/', '', $product->summary)));
    $item->addChild('g:link', url('producto/'.$product->product_bridge->slug));
    $item->addChild('g:image_link', Asset::get_image_path('product-image','normal', $product->image));
    $item->addChild('g:brand', $brand);
    $item->addChild('g:condition', "new");
    if($product->price>10){
        if($product->quantity>0){
            $item->addChild('g:availability', "in stock");
        } else {
            $item->addChild('g:availability', "out of stock");
        }
        $item->addChild('g:price', $product->price." BOB");
    } else {
        $item->addChild('g:availability', "out of stock");
        $item->addChild('g:price', "0 BOB");
    }
  }

echo $rss->asXML();

?>
