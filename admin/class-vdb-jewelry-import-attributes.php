<?php

/**
 * The admin-specific functionality of the plugin. Provides attributes where needed.
 *
 * @link       https://www.vdbapp.com/
 * @since      1.0.0
 *
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Vdb_Jewelry_Import
 * @subpackage Vdb_Jewelry_Import/admin
 * @author     Virtual Diamond Boutique <info@vdbapp.com>
 */

// Preventing to direct access
defined('ABSPATH') OR die( ( new Vdb_Jewelry_Import_Constants )->DIE_MESSAGE );

class Jewelry_Import_Attributes {
    
    /*Attributes of Rings Start Here*/
    public $rings_search_setings      = [ 
                                            'Jewelry Style', 
                                            'Metal Type', 
                                            'Price' 
                                        ];

    public $rings_details_settings    = [ 
                                            'Type', 
                                            'Metal', 
                                            'Brand', 
                                            'Item Location' 
                                        ];

    public $rings_ring_style_array    = [ 
                                            'Solitaire', 
                                            'Three Stone', 
                                            'Side Stone', 
                                            'Halo', 
                                            'Gemstone', 
                                            'Vintage'
                                        ];

    public $rings_metal_type_array    = [ 
                                            '10K Rose Gold', 
                                            '10K White Gold', 
                                            '10K Yellow Gold', 
                                            '14K Rose Gold', 
                                            '14K White Gold', 
                                            '14K Yellow Gold', 
                                            '18K Rose Gold', 
                                            '18K White Gold', 
                                            '18K Yellow Gold', 
                                            '21K Rose Gold', 
                                            '21K White Gold', 
                                            '22K Yellow Gold', 
                                            '24K Yellow Gold', 
                                            'Platinum', 
                                            'Silver'
                                        ];
    /*Attributes of Rings End Here*/


    public $jewelry_styles = array(
                                    'engagment_rings'   => 'Engagement Rings', 
                                    'Wedding_Bands'     => 'Wedding Bands', 
                                    'Fashion_Women'     => 'Fashion Women', 
                                    'Fashion_Men'       => 'Fashion Men',
                                    'earrings'          => 'Earrings', 
                                    'Bracelets'         => 'Bracelets', 
                                    'Necklaces'         => 'Necklaces', 
                                    'Pearl_Jewelry'     => 'Pearl Jewelry', 
                                    'Watches'           => 'Watches',
                                    'Accessories'       => 'Accessories'
                            );

    public $jewelry_sub_types = array(
                                    'engagment_rings'   => array(
                                                                'solitaire'     => 'Solitaire',
                                                                'three-stone'   => 'Three Stone',
                                                                'side-stone'    => 'Side Stone',
                                                                'halo'          => 'Halo',
                                                                'gemstones'     => 'Gemstone',
                                                                'vintage'       => 'Vintage',
                                                                // 'other'         => 'Other',
                                                            ), 

                                    'Wedding_Bands'      => array(
                                                                'diamond'       => 'Diamond',
                                                                'plain'         => 'Plain',
                                                                'eternity'      => 'Eternity',
                                                                'men'           => 'Men',
                                                                'stacking'      => 'Stacking',
                                                                'gemstones'     => 'Gemstones',
                                                                // 'other'         => 'Other',
                                                            ),

                                    'earrings'           => array(
                                                                'studs'         => 'Studs',
                                                                'fashion-studs' => 'Fashion Studs',
                                                                'drops'         => 'Drops',
                                                                'chandeliers'   => 'Chandeliers',
                                                                'hoops'         => 'Hoops',
                                                                // 'other'         => 'Other',
                                                            ),

                                    'Bracelets'          => array(
                                                                'tennis'        => 'Tennis',
                                                                'bangle'        => 'Bangle',
                                                                'cuff'          => 'Cuff',
                                                                'link'          => 'Link',
                                                                'fashion'       => 'Fashion',
                                                                // 'other'         => 'Other',
                                                            ),

                                    'Necklaces'          => array(
                                                                'pendants'      => 'Pendants',
                                                                'tennis'        => 'Tennis',
                                                                'lariat'        => 'Lariat',
                                                                'choker'        => 'Choker',
                                                                'wreath'        => 'Wreath',
                                                                // 'other'         => 'Other',
                                                            ),

                                    'Pearl_Jewelry'             => array(
                                                                'single-strand-necklace'    => 'Single Strand Necklace',
                                                                'fashion-necklace'          => 'Fashion Necklace',
                                                                'bracelet'                  => 'Bracelet',
                                                                'earrings'                  => 'Earrings',
                                                                'pendants'                  => 'Pendants',
                                                                'rings'                     => 'Rings',
                                                                // 'other'                     => 'Other',
                                                            ),

                                    'Watches'             => array(
                                                                'watch_size'    => array(
                                                                                        'men'       => 'Men',
                                                                                        'women'     => 'Women',
                                                                                        'mid'       => 'Mid Size'
                                                                                    ),
                                                                'pendants'      => 'Diamond Bezel',
                                                                'tennis'        => 'Diamond Dial',
                                                                'lariat'        => 'Leather Strap',
                                                                'choker'        => 'Metal Bracelet',
                                                                
                                                            ),

                            );


    public $metals =   array(
                            'MIX Metal'           => 'MIX Metal',
                            'Platinum'            => 'Platinum',
                            'Silver'              => 'Silver',
                            'White Gold'          => array('10','14', '18', '21'),
                            'Yellow Gold'         => array('10','14', '18', '22', '24'),
                            'Rose Gold'           => array('10','14', '18', '21'),
                            // 'other'               => 'Other',
                        );


    public $brands =   array(
                            'Samsung'           => 'Samsung',
                            'Rado'              => 'Rado',
                            'Armani'            => 'Armani',
                            // 'other'             => 'Other',
                        );
}