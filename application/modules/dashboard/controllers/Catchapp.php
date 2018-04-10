<?php

include 'simplehtmldom_1_5/simple_html_dom.php';

class Catchapp extends Admin_Controller 
{
	var $data;
	
	function __construct() {
		parent::__construct();
		
	}

	public function generate()
	{
		$url="https://app.shopping-feed.com/lib/import/ShopifySku.php?shop=jasonl4.myshopify.com&token=4be9c684ccd5770657dde3967892713b";
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);    // get the url contents

		$data = curl_exec($ch); // execute curl request
		curl_close($ch);

		$xml = simplexml_load_string($data, 'SimpleXMLElement', LIBXML_NOCDATA);
		$xmlJson = json_encode($xml);
		$productsdata = json_decode($xmlJson, 1); // Returns associative array
		// print_r($productsdata);
		// die();
		// $xml = simplexml_load_string($data);
		// $productsdata = (object)json_decode(json_encode((array)$xml), TRUE);

		/* create a dom document with encoding utf8 */
		$domtree = new DOMDocument('1.0', 'UTF-8');

		/* create the root element of the xml tree */
		$xmlRoot = $domtree->createElement("import");
		/* append it to the document created */
		$xmlRoot = $domtree->appendChild($xmlRoot);

		$majornode = $domtree->createElement("products");
		$majornode = $xmlRoot->appendChild($majornode);

		$count = 1;
		$offerdata = array();
		foreach($productsdata['product'] as $product){
			//category mapping : Shopify cat => catch category

		    // Office chairs seating => furniture > office furniture > office chairs
		    // Office Desks, Office workstation, Stand-Up Desks, Tables => furniture > office furniture > desks &amp; computer tables
		    // Office Partition Screens => furniture > office furniture > cubicle &amp; partitions
		    // Office storage => furniture > office furniture > office storage
		    // Sofas and Lounges, Whiteboards, cafe furniture, Electrical & Accessories => furniture > office furniture > other
			$category = '';
			if(array_key_exists('caracteristiques', $product)){
				if(is_array($product['caracteristiques'])){
					if(array_key_exists('collection', $product['caracteristiques'])){
						$category = $product['caracteristiques']['collection'];
					}
				}
			}
			if(!is_array($category)){ // valid collection returs string / if collection blank returs the blank array

				// default catch category
				$catchcategory = 'furniture > office furniture > other';
				if($category){
					$categories = explode('/', $category);
					$catgrp1 = array('Office Chairs Seating','Visitor Chairs');
					$catgrp2 = array('Home Office Desks', 'Office Workstations', 'Stand-Up Desks', 'Tables','Reception Desks','Standing Up Desks','Corner Workstations');
					$catgrp3 = array('Office Partition Screens');
					$catgrp4 = array('Office Storage');
					$catgrp5 = array('Sofas And Lounges', 'Cafe Furniture');
					$catgrp6 = array('Whiteboards');
					$catgrp7 = array('Electrical & Accessories');
					//map category from shopify to catchoftheday
					foreach ($categories as $singlecat) {
						$singlecat = trim($singlecat);
						if(in_array($singlecat, $catgrp1)){
							$catchcategory = 'furniture > office furniture > office chairs';
						}elseif (in_array($singlecat, $catgrp2)) {
							$catchcategory = 'furniture > office furniture > desks &amp; computer tables';
						}elseif (in_array($singlecat, $catgrp3)) {
							$catchcategory = 'furniture > office furniture > cubicle &amp; partitions';
						}elseif (in_array($singlecat, $catgrp4)) {
							$catchcategory = 'furniture > office furniture > office storage';
						}elseif (in_array($singlecat, $catgrp6)) {
							$catchcategory = 'home office &amp; media > stationery > whiteboards';
						}elseif (in_array($singlecat, $catgrp7)) {
							$catchcategory = 'electronics &amp; appliances > cameras &amp; video cameras > accessories > cables, chargers &amp; batteries';
						}elseif (in_array($singlecat, $catgrp5)) {
							$catchcategory = 'furniture > office furniture > other';
						}
					}
				}
				//images, multiple or single image case
				if(array_key_exists('images', $product)){
					if(array_key_exists('image', $product['images'])){
						$mainproductimages = $product['images']['image'];
					}
				}
				//filter product description
				$productdesc = str_get_html($product['description']);
				//$productdesc = htmlspecialchars($productdesc->find('div[class=pro-desc]', 0));
				$productdesc = htmlspecialchars(preg_replace('#<a.*?>.*?</a>#i', '', $productdesc->find('div[class=pro-desc]', 0)));

				//First handle for products with no variants
				if(!array_key_exists('declinaisons', $product)){ // product with no variant
					$productnode = $majornode->appendChild($domtree->createElement('product'));
					// $productnode = $domtree->createElement("product");
					// $productnode = $xmlRoot->appendChild($productnode);
					
					$productnode->appendChild($domtree->createElement('category', $catchcategory));

					/* you should enclose the following two lines in a cicle */
					$productname = htmlspecialchars($product['name']);
					$productid = $product['id'];
					// title = product name
					$productnode->appendChild($domtree->createElement('title', $productname));
					//internal-sku = SKU
					$productnode->appendChild($domtree->createElement('internal-sku', $productid));
					//variant-id = shopify variant id
					//$productnode->appendChild($domtree->createElement('variant-id', $productid));
					//product-reference-type = mpn
					$productnode->appendChild($domtree->createElement('product-reference-type', 'mpn'));
					// product-reference-value = jl-SKU
					$productnode->appendChild($domtree->createElement('product-reference-value', 'jl'.$count.'-'.$productid));
					//Parsed description via xml parsing
					$productnode->appendChild($domtree->createElement('product-description', $productdesc));
					$productnode->appendChild($domtree->createElement('brand', 'JasonL'));
					$productnode->appendChild($domtree->createElement('adult', 'no'));
					$count++;
					$k = 1;
					if(is_array($mainproductimages) && !empty($mainproductimages)){
						foreach ($mainproductimages as $productimage) {
							$productnode->appendChild($domtree->createElement('image-'.$k, $productimage)); $k++;
						}
					}elseif (!is_array($mainproductimages) && !empty($mainproductimages)) {
						$productnode->appendChild($domtree->createElement('image-1', $mainproductimages));
					}
					array_push($offerdata, array('internal-sku'=>$productid,'price'=>$product['price'],'quantity'=>$product['quantity']));
				}else{ // PRODUCTS WITH VARIANTS EXISTING [MORE THAN 1]
					$variants = $product['declinaisons']['declinaison'];
				   /* Same with products
					* description, category, internal sku, product reference type, brand, adult, 
					*
					* Different
					* title (append with variant value), variant id, image(if no variant image, take first image of main product), product reference value (Should be unique so, jl-{{variant_id}} ) 
					*/
					if(!array_key_exists('id', $variants)) { //if id exists inside declination then it's single variant product
						$lowestpricevar = ''; $j=1;
						foreach ($variants as $variant) { // each variant will be a single product with attribute value appended on title
							if(is_array($variant['attributs'])){
								if($j==1){
									$lowestpricevar = $variant;
								}else{
									//compare if variant has lowest price
									if($variant['price'] < $lowestpricevar['price']){
										$lowestpricevar = $variant;
									}
								} $j++;
								// $variantid = $variant['attributs']['shopify_id'];
								// //filter out attributes only excluding non attribute from attribute key inside declination
								// $nonattributes = array('inventory_management','grams','sku','shopify_id');
								// $ptitle = $product['name'];
								// $extendtitle = '';
								// foreach ($variant['attributs'] as $attributekey=>$attributeval) {
								// 	if(!in_array($attributekey, $nonattributes)) {
								// 		//append these to product title
								// 		if($attributeval!='none'){
								// 			$extendtitle .= ' - '.$attributeval;
								// 		}
								// 	}
								// }
								// $extendedproductitle = $ptitle.$extendtitle;

								// $productnode = $domtree->createElement("product");
								// $productnode = $xmlRoot->appendChild($productnode);
								
								// $productnode->appendChild($domtree->createElement('category', $catchcategory));

								// /* you should enclose the following two lines in a cicle */
								// $productname = htmlspecialchars($extendedproductitle);
								// $productid = $product['id'];
								// // title = product name
								// $productnode->appendChild($domtree->createElement('title', $productname));
								// //internal-sku = SKU
								// $productnode->appendChild($domtree->createElement('internal-sku', $productid));
								// //variant-id = shopify variant id
								// $productnode->appendChild($domtree->createElement('variant-id', $variantid));
								// //product-reference-type = mpn
								// $productnode->appendChild($domtree->createElement('product-reference-type', 'mpn'));
								// // product-reference-value = jl-SKU
								// $productnode->appendChild($domtree->createElement('product-reference-value', 'jl-'.$variantid));
								// //Parsed description via xml parsing
								// $productnode->appendChild($domtree->createElement('description', $productdesc));
								// $productnode->appendChild($domtree->createElement('brand', 'JasonL'));
								// $productnode->appendChild($domtree->createElement('adult', 'no'));

								// //variant images : if image exists then use variant image else parent image
								// $variantimage = '';
								// if(array_key_exists('images', $variant)) {
								// 	if(array_key_exists('image', $variant['images'])){
								// 		$variantimage = $variant['images']['image'];
								// 	}
								// }
								// if($variantimage!=''){ //use variant image. only single image exists for a variant
								// 	$productnode->appendChild($domtree->createElement('image-1', $variantimage));
								// }else{
								// 	//if no variant image exists case
								// 	$k = 1;
								// 	if(is_array($mainproductimages) && !empty($mainproductimages)){
								// 		foreach ($mainproductimages as $productimage) {
								// 			$productnode->appendChild($domtree->createElement('image-'.$k, $productimage)); $k++;
								// 		}
								// 	}
								// }
							}
						}
						$nonattributes = array('inventory_management','grams','sku','shopify_id');
						$ptitle = $product['name'];
						$extendtitle = '';
						foreach ($lowestpricevar['attributs'] as $attributekey=>$attributeval) {
							if(!in_array($attributekey, $nonattributes)) {
								//append these to product title
								if($attributeval!='none'){
									$extendtitle .= ' - '.$attributeval;
								}
							}
						}
						$variantid = $lowestpricevar['attributs']['shopify_id'];
						$extendedproductitle = $ptitle.$extendtitle;

						$productnode = $majornode->appendChild($domtree->createElement('product'));
						// $productnode = $domtree->createElement("product");
						// $productnode = $xmlRoot->appendChild($productnode);
						
						$productnode->appendChild($domtree->createElement('category', $catchcategory));

						/* you should enclose the following two lines in a cicle */
						$productname = htmlspecialchars($extendedproductitle);
						$productid = $product['id'];
						// title = product name
						$productnode->appendChild($domtree->createElement('title', $productname));
						//internal-sku = SKU
						$productnode->appendChild($domtree->createElement('internal-sku', $productid));
						//variant-id = shopify variant id
						//$productnode->appendChild($domtree->createElement('variant-id', $variantid));
						//product-reference-type = mpn
						$productnode->appendChild($domtree->createElement('product-reference-type', 'mpn'));
						// product-reference-value = jl-SKU
						$productnode->appendChild($domtree->createElement('product-reference-value', 'jl'.$count.'-'.$variantid));
						//Parsed description via xml parsing
						$productnode->appendChild($domtree->createElement('product-description', $productdesc));
						$productnode->appendChild($domtree->createElement('brand', 'JasonL'));
						$productnode->appendChild($domtree->createElement('adult', 'no'));
						$count++;
						//variant images : if image exists then use variant image else parent image
						$variantimage = '';
						if(array_key_exists('images', $lowestpricevar)) {
							if(array_key_exists('image', $lowestpricevar['images'])){
								$variantimage = $lowestpricevar['images']['image'];
							}
						}
						if($variantimage!=''){ //use variant image. only single image exists for a variant
							$productnode->appendChild($domtree->createElement('image-1', $variantimage));
						}else{
							//if no variant image exists case
							$k = 1;
							if(is_array($mainproductimages) && !empty($mainproductimages)){
								foreach ($mainproductimages as $productimage) {
									$productnode->appendChild($domtree->createElement('image-'.$k, $productimage)); $k++;
								}
							}elseif (!is_array($mainproductimages) && !empty($mainproductimages)) {
								$productnode->appendChild($domtree->createElement('image-1', $mainproductimages));
							}
						}
						array_push($offerdata, array('internal-sku'=>$productid,'price'=>$lowestpricevar['price'],'quantity'=>$lowestpricevar['quantity']));
						//echo 'start '.count($product['declinaisons']['declinaison']).' count<br/>';
					}
				}
				

			}

		}
		$productnode = $domtree->createElement("offers");
		$productnode = $xmlRoot->appendChild($productnode);

		if(!empty($offerdata)){
			foreach ($offerdata as $offer){
				$offernode = $productnode->appendChild($domtree->createElement('offer'));
				//$productnode = $xmlRoot->appendChild($productnode);
				
				$offernode->appendChild($domtree->createElement('logistic-class', 'FLAT'));
				$offernode->appendChild($domtree->createElement('price', $offer['price']));
				$offernode->appendChild($domtree->createElement('product-id', $offer['internal-sku']));
				$offernode->appendChild($domtree->createElement('product-id-type', 'SHOP_SKU'));
				$offernode->appendChild($domtree->createElement('quantity', $offer['quantity']));
				$offernode->appendChild($domtree->createElement('sku', $offer['internal-sku']));
				$offernode->appendChild($domtree->createElement('state', '11'));
				$offernode->appendChild($domtree->createElement('update-delete', 'UPDATE'));
				$suboffer = $offernode->appendChild($domtree->createElement('offer-additional-fields'));
					$inneroffer = $suboffer->appendChild($domtree->createElement('offer-additional-field'));
						$inneroffer->appendChild($domtree->createElement('code', 'club-catch-eligible'));
						$inneroffer->appendChild($domtree->createElement('value', 'true'));
					$inneroffer = $suboffer->appendChild($domtree->createElement('offer-additional-field'));
						$inneroffer->appendChild($domtree->createElement('code', 'tax-au'));
						$inneroffer->appendChild($domtree->createElement('value', '10'));
			}
		}

		$domtree->save("assets/products.xml");
		$data = array('state'=>1);
		Template::render('admin/catchdashboard', $data);

	}

	public function index(){
		$data = array('state'=>0);
        Template::render('admin/catchdashboard', $data);
	
	}
	
	
	
}
