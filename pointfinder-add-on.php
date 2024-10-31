<?php

/*
Plugin Name: WP All Import - PointFinder Add-On
Plugin URI: https://themeforest.net/user/webbu/portfolio
Description: Import listings into PointFinder.
Version: 1.0.2
Author: Webbu
*/


include "rapid-addon.php";

if ( ! function_exists( 'is_plugin_active' ) ) {

	require_once ABSPATH . 'wp-admin/includes/plugin.php';

}

$pointfindertheme_options = get_option('pointfindertheme_options');
$listing_post_type = (isset($pointfindertheme_options['setup3_pointposttype_pt1']))?$pointfindertheme_options['setup3_pointposttype_pt1']:'pfitemfinder';
$pointfinder_addon = new RapidAddon('PointFinder Add-On', 'pointfinder_addon');


/*
*Start: Create Fields
*/
	$pointfinder_addon->add_field('webbupointfinder_item_featuredmarker', 'Featured Point', 'radio',array('1' => 'Yes','0' => 'No'));
	$pointfinder_addon->add_field('webbupointfinder_item_verified', 'Verified Point', 'radio',array('1' => 'Yes','0' => 'No'));
	$pointfinder_addon->add_field('webbupointfinder_item_point_visibility','Point Visibilty','radio',array('0' => 'Yes','1' => 'No'),'Leave empty for show');


	/*
	*Start: Location/Streetview and Address Lat/Lng
	*/
		$pointfinder_addon->add_field( 'webbupointfinder_items_address','Listing Address','text', null, 'Building number and street name, example: 1206 King St'  );

		$pointfinder_addon->add_options( 
		        $pointfinder_addon->add_field( 'webbupointfinder_item_streetview_angle', 'Google Street View Camera Angle (POV Heading)', 'text'),
		        'Streetview Settings', 
		        array(
		            $pointfinder_addon->add_field( 'webbupointfinder_item_streetview_pitch', 'Pitch (POV Pitch)', 'text'),
		            $pointfinder_addon->add_field( 'webbupointfinder_item_streetview_zoom', 'Zoom (1-20)', 'text')
		        )
		);


		$pointfinder_addon->add_field(
			'location_settings',
			'Listing Location (Coordinates)',
			'radio', 
			array(
				'search_by_address' => array(
					'Search by Address',
					$pointfinder_addon->add_options( 
						$pointfinder_addon->add_field(
							'_property_location_search',
							'Listing Address',
							'text'
						),
						'Google Geocode API Settings', 
						array(
							$pointfinder_addon->add_field(
								'address_geocode',
								'Request Method',
								'radio',
								array(
									'address_no_key' => array(
										'No API Key',
										'Limited number of requests.'
									),
									'address_google_developers' => array(
										'Google Developers API Key - <a href="https://developers.google.com/maps/documentation/geocoding/#api_key">Get free API key</a>',
										$pointfinder_addon->add_field(
											'address_google_developers_api_key', 
											'API Key', 
											'text'
										),
										'Up to 2,500 requests per day and 5 requests per second.'
									),
									'address_google_for_work' => array(
										'Google for Work Client ID & Digital Signature - <a href="https://developers.google.com/maps/documentation/business">Sign up for Google for Work</a>',
										$pointfinder_addon->add_field(
											'address_google_for_work_client_id', 
											'Google for Work Client ID', 
											'text'
										), 
										$pointfinder_addon->add_field(
											'address_google_for_work_digital_signature', 
											'Google for Work Digital Signature', 
											'text'
										),
										'Up to 100,000 requests per day and 10 requests per second'
									)
								) // end Request Method options array
							) // end Request Method nested radio field 
						) // end Google Geocode API Settings fields
					) // end Google Gecode API Settings options panel
				), // end Search by Address radio field
				'search_by_coordinates' => array(
					'Enter Coordinates',
					$pointfinder_addon->add_field(
						'_property_latitude', 
						'Latitude', 
						'text', 
						null, 
						'Example: 34.0194543'
					),
					$pointfinder_addon->add_field(
						'_property_longitude',
						'Longitude',
						'text',
						null, 
						'Example: -118.4911912'
					) // end coordinates Option panel
				) // end Search by Coordinates radio field
			) // end Property Location radio field
		);
	/*
	*End: Location/Streetview and Address Lat/Lng
	*/



	/*
	*Start: Custom Fields Array
	*/	
		$custom_fields_array = array();
		$pointfindertheme_options = get_option('pointfindertheme_options');
		$pointfinder_addon->add_text( '<strong>Custom Fields Info:</strong>  Please configure your custom fields from Options Panel > System Settings > Custom Fields section.' );

		if (isset($pointfindertheme_options['setup1_slides'])) {
			foreach ($pointfindertheme_options['setup1_slides'] as &$value) {
				if($value['select'] != 10 && $value['select'] != 16){
					$custom_fields_array[] = $pointfinder_addon->add_field( $value['url'], $value['title'], 'text');
				}
			}
		}

		if (!empty($custom_fields_array)) {
			$pointfinder_addon->add_options( 
		        null,
		        'Custom Fields',
		        $custom_fields_array
			);
		}else{
			$pointfinder_addon->add_title( 'Custom Fields','If you can not find your custom fields please configure your fields from Options Panel > System Settings > Custom Fields section.' );
		}

	/*
	*End: Custom Fields Array
	*/

	/*
	*Start: Image Data
	*/	
		$pointfinder_addon->add_field('property_featured_img', 'Featured Image', 'image');
		$pointfinder_addon->import_images('pointfinderaddon_item_images', 'PointFinder Gallery Images');
		$pointfinder_addon->add_field('webbupointfinder_item_headerimage', 'Header Image', 'image');
		$pointfinder_addon->add_field('webbupointfinder_item_sliderimage', 'Slider Image', 'image');
	/*
	*End: Image Data
	*/



	/*
	*Start: Video Data
	*/	
		$pointfinder_addon->add_field(
		        'lvideo_settings',
		        'Listing Video',
		        'radio', 
		        array(
		            'sel1' => array(
	                    'Import Solution 1',
	                    $pointfinder_addon->add_field( 'pointfinder_video_type', 'Video from:', 'radio', 
							array(
								'youtube' => 'YouTube',
								'vimeo' => 'Vimeo'
						) ),
						$pointfinder_addon->add_field( 'pointfinder_video_id', 'Embed Video ID', 'text', null, 'Embed ID from http://www.youtube.com/watch?v=dQw4w9WgXcQ would be: dQw4w9WgXcQ' ),
		            ),
		            'sel2' => array(
	                    'Import Solution 2',
	                    $pointfinder_addon->add_field( 'listing_video_url', 'Video URL', 'text' ),
		            )
		        )
		);
	/*
	*End: Video Data
	*/	



	/*
	*Start: Opening Hours
	*/	
		$pointfinder_addon->add_text('<strong>Opening Hours Info:</strong> For Type 1: 23:45-24:00 / For Type 2 : 23:45-24:00' );
		$pointfinder_addon->add_options( 
	        null,
	        'Opening Hours', 
	        array(
	        	$pointfinder_addon->add_field( 'webbupointfinder_items_o_o1','Monday','text'),
				$pointfinder_addon->add_field( 'webbupointfinder_items_o_o2','Tuesday','text'),
				$pointfinder_addon->add_field( 'webbupointfinder_items_o_o3','Wednesday','text'),
				$pointfinder_addon->add_field( 'webbupointfinder_items_o_o4','Thursday','text'),
				$pointfinder_addon->add_field( 'webbupointfinder_items_o_o5','Friday','text'),
				$pointfinder_addon->add_field( 'webbupointfinder_items_o_o6','Saturday','text'),
				$pointfinder_addon->add_field( 'webbupointfinder_items_o_o7','Sunday','text')
	        )
		);
	/*
	*End: Opening Hours
	*/


	/*
	*Start: Custom Tabs
	*/	
		$pointfinder_addon->add_options( 
	        null,
	        'Custom Tabs', 
	        array(
	        	$pointfinder_addon->add_field( 'webbupointfinder_item_custombox1','Custom Tab 1','text' ),
	        	$pointfinder_addon->add_field( 'webbupointfinder_item_custombox2','Custom Tab 2','text' ),
	        	$pointfinder_addon->add_field( 'webbupointfinder_item_custombox3','Custom Tab 3','text' )
	        )
		);
	/*
	*End: Custom Tabs
	*/




	/*
	*Start: File Data
	*/	
		$pointfinder_addon->import_files( 'pointfinder_item_files', 'PointFinder Attached Files');
	/*
	*End: File Data
	*/


	/*
	*Start: Point Options
	*/
		$pointfinder_addon->add_field(
	        'webbupointfinder_item_point_type',
	        'Point Options',
	        'radio', 
	        array(
	        	'3' => array('None (Use Category)'),
	            '1' => array('Custom Image',
	                $pointfinder_addon->add_field( 'webbupointfinder_item_custom_marker', 'Point Icon', 'image',null,'Upload custom point icon. Default icon size: 84x101 px' )
	            ), 
	            '2' => array(
	                'Predefined Icon',
	                $pointfinder_addon->add_field( 'webbupointfinder_item_cssmarker_icontype', 'Point Icon Type', 'radio',array(
	                    '1' => 'Round',
	                    '2' => 'Square',
	                    '3' => 'Dot'
	                ) ),
	                $pointfinder_addon->add_field( 'webbupointfinder_item_cssmarker_iconsize', 'Point Icon Size', 'radio',array(
	                    'small' => 'Small',
	                    'middle' => 'Middle',
	                    'large' => 'Large',
	                    'xlarge' => 'X-Large',
	                ) ),
	                $pointfinder_addon->add_field( 'webbupointfinder_item_cssmarker_bgcolor','Point Color','text' ),
	                $pointfinder_addon->add_field( 'webbupointfinder_item_cssmarker_bgcolorinner','Point Inner Color','text' ),
	                $pointfinder_addon->add_field( 'webbupointfinder_item_cssmarker_iconcolor','Point Icon Color','text' ),
	                $pointfinder_addon->add_field( 'webbupointfinder_item_cssmarker_iconname','Point Icon','text' )
	            )
	        ),
	        'Default: None (Use Category)'
		);
	/*
	*End: Point Options
	*/


	$pointfinder_addon->add_field( 'webbupointfinder_item_agents','Listing Agent','text',null,'Supports only single Agent ID number' );
	$pointfinder_addon->add_field( 'webbupointfinder_items_favorites','Favorites Count','text' );
	$pointfinder_addon->add_field( 'webbupointfinder_items_mesrev','Message to Reviewer','text' );

/*
*End: Create Fields
*/




$pointfinder_addon->set_import_function('pointfinder_addon_import_function');
$pointfinder_addon->disable_default_images();

$pointfinder_addon->admin_notice(
    "PointFinder Import Add-On requires WP All Import, the WP All Export Plugin and Pointfinder theme.", 
	array(
		"themes"  => array( "Pointfinder" ),
        "plugins" => array( "wp-all-export/wp-all-export.php" )
    )   
);


function pointfinder_addon_import_function($post_id, $data, $import_options){
	global $pointfinder_addon;


	/*
	*Start: Set featured Image
	*/
		$pointfinder_addon->log( 'Updating Listing Featured Image' );
		if ($pointfinder_addon->can_update_meta( 'property_featured_img', $import_options )) {
			$attachment_id = $data['property_featured_img']['attachment_id'];
			set_post_thumbnail( $post_id, $attachment_id );
		}
	/*
	*End: Set featured Image
	*/


	/*
	*Start: Set Slider Image
	*/
		$pointfinder_addon->log( 'Updating Listing Slider Image' );
		if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_sliderimage', $import_options )) {
			if (!empty($data['webbupointfinder_item_sliderimage']['attachment_id'])) {
				$attachment_array = array();
				$attachment_id = $data['webbupointfinder_item_sliderimage']['attachment_id'];

				$attachment_detail = wp_get_attachment_image_src($attachment_id,'full');
				$attachment_thumb = wp_get_attachment_thumb_url($attachment_id);

				$attachment_array['url'] = $attachment_detail['url'];
				$attachment_array['id'] = $attachment_id;
				$attachment_array['width'] = $attachment_detail['width'];
				$attachment_array['height'] = $attachment_detail['height'];
				$attachment_array['thumbnail'] = $attachment_thumb;

				update_post_meta($post_id, "webbupointfinder_item_sliderimage", $attachment_array);
			}
		}
	/*
	*End: Set Slider Image
	*/


	/*
	*Start: Set header Image
	*/
		$pointfinder_addon->log( 'Updating Listing Header Image' );
		if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_headerimage', $import_options )) {
			
			if (!empty($data['webbupointfinder_item_headerimage']['attachment_id'])) {
				$attachment_array = array();
				$attachment_id = $data['webbupointfinder_item_headerimage']['attachment_id'];

				$attachment_detail = wp_get_attachment_image_src($attachment_id,'full');
				$attachment_thumb = wp_get_attachment_thumb_url($attachment_id);

				$attachment_array['url'] = $attachment_detail['url'];
				$attachment_array['id'] = $attachment_id;
				$attachment_array['width'] = $attachment_detail['width'];
				$attachment_array['height'] = $attachment_detail['height'];
				$attachment_array['thumbnail'] = $attachment_thumb;

				update_post_meta($post_id, "webbupointfinder_item_headerimage", $attachment_array);
			}
		}
	/*
	*End: Set header Image
	*/



	/*
	*Start: Location/Streetview and Address Lat/Lng
	*/

		$pointfinder_addon->log( 'Updating Listing Address' );
		if ($pointfinder_addon->can_update_meta( 'webbupointfinder_items_address', $import_options )) {
			update_post_meta($post_id, "webbupointfinder_items_address", $data['webbupointfinder_items_address']);
		}

		$pointfinder_addon->log( 'Updating Listing Street View' );
		if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_streetview_angle', $import_options ) && !empty($data['webbupointfinder_item_streetview_angle'])) {
			update_post_meta($post_id, "webbupointfinder_item_streetview", array(
				'heading'=>$data['webbupointfinder_item_streetview_angle'],
				'pitch' =>(!empty($data['webbupointfinder_item_streetview_pitch']))?$data['webbupointfinder_item_streetview_pitch']:"",
				'zoom' =>(!empty($data['webbupointfinder_item_streetview_zoom']))?$data['webbupointfinder_item_streetview_zoom']:""
			));
		}

		$pointfinder_addon->log( 'Updating Listing Location' );
		if ($pointfinder_addon->can_update_meta( 'location_settings', $import_options )) {
			switch ($data['location_settings']) {
				case 'search_by_address':
					$search = ( !empty( $data['_property_location_search'] ) ? 'address=' . rawurlencode( $data['_property_location_search'] ) : null );

					if ( $data['address_geocode'] == 'address_google_developers' && !empty( $data['address_google_developers_api_key'] ) ) {
        
				        $api_key = '&key=' . $data['address_google_developers_api_key'];
				    
				    } elseif ( $data['address_geocode'] == 'address_google_for_work' && !empty( $data['address_google_for_work_client_id'] ) && !empty( $data['address_google_for_work_signature'] ) ) {
				        
				        $api_key = '&client=' . $data['address_google_for_work_client_id'] . '&signature=' . $data['address_google_for_work_signature'];

				    }   
			        // build $request_url for api call
			        $request_url = 'https://maps.googleapis.com/maps/api/geocode/json?' . $search . $api_key;
			        $curl = curl_init();
			        curl_setopt( $curl, CURLOPT_URL, $request_url );
			        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );
			        $pointfinder_addon->log( '- Getting location data from Geocoding API: ' . $request_url );
			        $json = curl_exec( $curl );
			        curl_close( $curl );			      
			        // parse api response
			        if ( !empty( $json ) ) {
			            $details = json_decode( $json, true );
			            if (!empty($details[results][0][geometry][location][lat]) && !empty($details[results][0][geometry][location][lng])) {
			            	$pointfinder_addon->log( '- Updating latitude and longitude' );
			            	update_post_meta($post_id, "webbupointfinder_items_location", trim($details[results][0][geometry][location][lat]).','.trim($details[results][0][geometry][location][lng]));
			            }
			        }
					break;
				
				case 'search_by_coordinates':
				default:
					update_post_meta($post_id, "webbupointfinder_items_location", $data['_property_latitude'].','.$data['_property_longitude']);
					break;
			}
		}
	/*
	*End: Location/Streetview and Address Lat/Lng
	*/


	/*
	*Start: Custom Fields Array
	*/	
		$pointfinder_addon->log( 'Updating Custom Fields' );
		$pointfindertheme_options = get_option('pointfindertheme_options');
		if (isset($pointfindertheme_options['setup1_slides'])) {
			foreach ($pointfindertheme_options['setup1_slides'] as &$value) {
				if($value['select'] != 10 && $value['select'] != 16){
					if ($pointfinder_addon->can_update_meta( $value['url'], $import_options ) && $data[$value['url']] != '') {
						update_post_meta($post_id, "webbupointfinder_item_".$value['url'], $data[$value['url']]);
					}
				}
			}
		}
	/*
	*End: Custom Fields Array
	*/


	/*
	*Start: Video Data
	*/	
		$pointfinder_addon->log( 'Updating Listing Video'.$data['lvideo_settings'] );
		if ($pointfinder_addon->can_update_meta( 'lvideo_settings', $import_options )) {
			if (!empty($data['lvideo_settings'])) {
				switch ($data['lvideo_settings']) {
					case 'sel1':
						switch ($data['pointfinder_video_type']) {
							case 'youtube':
								update_post_meta($post_id, "webbupointfinder_item_video", 'https://www.youtube.com/watch?v='.$data['pointfinder_video_url']);
								break;
							case 'vimeo':
								update_post_meta($post_id, "webbupointfinder_item_video", 'https://player.vimeo.com/video/'.$data['pointfinder_video_url']);
								break;
							default:
								update_post_meta($post_id, "webbupointfinder_item_video", 'https://www.youtube.com/watch?v='.$data['pointfinder_video_url']);
								break;
						}
						break;
					case 'sel2':
						update_post_meta($post_id, "webbupointfinder_item_video", $data['listing_video_url']);
						break;
				}
			}
		}
	/*
	*End: Video Data
	*/


	/*
	*Start: Custom Tabs
	*/	
		$pointfinder_addon->log( 'Updating Custom Tabs' );
		if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_custombox1', $import_options )) {
			update_post_meta($post_id, "webbupointfinder_item_custombox1", $data['webbupointfinder_item_custombox1']);
		}
		if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_custombox2', $import_options )) {
			update_post_meta($post_id, "webbupointfinder_item_custombox2", $data['webbupointfinder_item_custombox2']);
		}
		if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_custombox3', $import_options )) {
			update_post_meta($post_id, "webbupointfinder_item_custombox3", $data['webbupointfinder_item_custombox3']);
		}
	/*
	*End: Custom Tabs
	*/


	/*
	*Start: Opening Hours
	*/	
		$pointfinder_addon->log( 'Updating Listing Opening Hours' );
		if ($pointfinder_addon->can_update_meta( 'webbupointfinder_items_o_o1', $import_options )) {

			if (!empty($data['webbupointfinder_items_o_o1'])) {
				update_post_meta($post_id, "webbupointfinder_items_o_o1", $data['webbupointfinder_items_o_o1']);
			}

			if (!empty($data['webbupointfinder_items_o_o1'])) {
				update_post_meta($post_id, "webbupointfinder_items_o_o1", $data['webbupointfinder_items_o_o1']);
			}
			if (!empty($data['webbupointfinder_items_o_o2'])) {
				update_post_meta($post_id, "webbupointfinder_items_o_o2", $data['webbupointfinder_items_o_o2']);
			}
			if (!empty($data['webbupointfinder_items_o_o3'])) {
				update_post_meta($post_id, "webbupointfinder_items_o_o3", $data['webbupointfinder_items_o_o3']);
			}
			if (!empty($data['webbupointfinder_items_o_o4'])) {
				update_post_meta($post_id, "webbupointfinder_items_o_o4", $data['webbupointfinder_items_o_o4']);
			}
			if (!empty($data['webbupointfinder_items_o_o5'])) {
				update_post_meta($post_id, "webbupointfinder_items_o_o5", $data['webbupointfinder_items_o_o5']);
			}
			if (!empty($data['webbupointfinder_items_o_o6'])) {
				update_post_meta($post_id, "webbupointfinder_items_o_o6", $data['webbupointfinder_items_o_o6']);
			}
			if (!empty($data['webbupointfinder_items_o_o7'])) {
				update_post_meta($post_id, "webbupointfinder_items_o_o7", $data['webbupointfinder_items_o_o7']);
			}
		}
	/*
	*End: Opening Hours
	*/



	/*
	*Start: Point Options
	*/
		$pointfinder_addon->log( 'Updating Listing Custom Point Style' );
		if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_point_type', $import_options )) {

			update_post_meta($post_id, "webbupointfinder_item_point_type", $data['webbupointfinder_item_point_type']);

			if (!empty($data['webbupointfinder_item_point_type'])) {
				if ($data['webbupointfinder_item_point_type'] == 2) {
					update_post_meta($post_id, "webbupointfinder_item_cssmarker_icontype", $data['webbupointfinder_item_cssmarker_icontype']);
					update_post_meta($post_id, "webbupointfinder_item_cssmarker_iconsize", $data['webbupointfinder_item_cssmarker_iconsize']);
					update_post_meta($post_id, "webbupointfinder_item_cssmarker_bgcolor", $data['webbupointfinder_item_cssmarker_bgcolor']);
					update_post_meta($post_id, "webbupointfinder_item_cssmarker_bgcolorinner", $data['webbupointfinder_item_cssmarker_bgcolorinner']);
					update_post_meta($post_id, "webbupointfinder_item_cssmarker_iconcolor", $data['webbupointfinder_item_cssmarker_iconcolor']);
					update_post_meta($post_id, "webbupointfinder_item_cssmarker_iconname", $data['webbupointfinder_item_cssmarker_iconname']);
				}
			}
		}
	/*
	*End: Point Options
	*/


	$pointfinder_addon->log( 'Updating Additional Data' );
	if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_featuredmarker', $import_options )) {
		update_post_meta($post_id, "webbupointfinder_item_featuredmarker", $data['webbupointfinder_item_featuredmarker']);
	}

	if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_verified', $import_options )) {
		update_post_meta($post_id, "webbupointfinder_item_verified", $data['webbupointfinder_item_verified']);
	}

	if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_point_visibility', $import_options )) {
		if(!empty($data['webbupointfinder_item_point_visibility'])){
			update_post_meta($post_id, "webbupointfinder_item_point_visibility", $data['webbupointfinder_item_point_visibility']);
		}
	}

	if ($pointfinder_addon->can_update_meta( 'webbupointfinder_item_agents', $import_options )) {
		update_post_meta($post_id, "webbupointfinder_item_agents", $data['webbupointfinder_item_agents']);
	}

	if ($pointfinder_addon->can_update_meta( 'webbupointfinder_items_favorites', $import_options )) {
		update_post_meta($post_id, "webbupointfinder_items_favorites", $data['webbupointfinder_items_favorites']);
	}

	if ($pointfinder_addon->can_update_meta( 'webbupointfinder_items_mesrev', $import_options )) {
		update_post_meta($post_id, "webbupointfinder_items_mesrev", $data['webbupointfinder_items_mesrev']);
	}
}


function pointfinderaddon_item_images( $post_id, $attachment_id, $image_filepath, $import_options ) {
	$images_array = array();
	$images_array[] = $attachment_id;
	foreach ($images_array as $aid) {
		add_post_meta( $post_id, 'webbupointfinder_item_images', $aid );
	}
}

function pointfinder_item_files( $post_id, $attachment_id, $image_filepath, $import_options ) {
	$files_array = array();
	$files_array[] = $attachment_id;
	foreach ($files_array as $aid) {
		add_post_meta( $post_id, 'webbupointfinder_item_files', $aid );
	}
}


$pointfinder_addon->run(
	array(
		"post_types" => array( $listing_post_type ),
	)
);