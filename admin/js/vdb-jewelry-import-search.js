(function( $ ) {
    'use strict';

    var currentRequest = null;

    $(document).on('ready',  function(){

        $( '.search-containt-card input' ).on( 'change', function(){ alert( 'Input change' ) } );

        $( "#vdb_js_clear_search" ).on( 'click', function(){
            
            $( ".jewelry-type" ).removeClass( 'active' );
            $( ".sub_types.engagment_rings, .sub_types.Wedding_Bands, .sub_types.earrings, .sub_types.Bracelets, .sub_types.Necklaces, .sub_types.Pearl_Jewelry, .sub_types.Watches" ).hide();
            $( ".engagment_rings li.list-style, .Wedding_Bands li.list-style, .earrings li.list-style, .Bracelets li.list-style, .Necklaces li.list-style, .Pearl_Jewelry li.list-style, .Watches li.list-style, .Watches li.list-style .Watches-types, .metals .box-list-2-li" ).removeClass( 'active' );
            $( ".metals .content, .brands .content, .prices .content, .types .contents" ).hide();
            $( ".jewelry_inputs" ).prop( 'checked', false );
            $( "#jewelry_brands option:selected" ).removeAttr("selected");
            $( "#price_total_from, #price_total_to" ).val( '' );
            $( ".price-error" ).hide();
            $( ".price-to-error" ).hide();
            $( ".header span" ).removeClass( 'icon-minus' ).addClass( 'icon-plus' );
            $( ".types .header span" ).removeClass( 'icon-plus' ).addClass( 'icon-minus' );

			$( ".container.types .content" ).show();

            $( "#jewelry_page_number" ).val( "1" );
            $( "#jewelry_load_more" ).val( "true" );

            ajax_call('onload');

        } );

        var $header, $content, $jewelryType, $addOrRemove;

        jQuery( '#jewelry_brands option' ).on( 'click', function(){
            
            if( true === jQuery( this ).prop( 'selected' ) ){
                jQuery( this ).prop( 'selected', false )
            }

        } );
        
        ajax_call('onload');

        /*
        *Collapse and expand    
        */
        $(".header").on('click',  function () {
            $header = $(this);
            $content = $header.next();
            $content.slideToggle(0, function () {
                
                $header.find('span').removeClass('icon-minus icon-plus').addClass(function () {
                    return $content.is(":visible") ? "icon-minus" : "icon-plus";
                });
            });
        });


        /*
        *Ajax call when user click on jewelry type
        */
        $(".jewelry-type").on('click',  function (event) {
            //event.stopPropagation();
            if( event.target.tagName === "INPUT" ) {
                $( '#jewelry_page_number' ).val( '1' );
                $jewelryType = $(this);
                $jewelryType.toggleClass('active');
                hide_show_subtypes($jewelryType);
                ajax_call();
            }
        });

        /*
        *Ajax call on change of checkbox of sub types and metals
        */
        $(".sub_types .custom-checkbox, .metals .custom-checkbox").on('click',  function (event) {
            //event.stopPropagation();
            if( event.target.tagName === "INPUT" ) {
                $( '#jewelry_page_number' ).val( '1' );
                $(this).closest('li').toggleClass('active');

                /*Add active class to Watches size*/
                if($(this).hasClass('radio-circle')){

                    $('div.Watches-types').removeClass('active');
                    $(this).closest('div.Watches-types').addClass('active');
                    
                }

                ajax_call();
            }
           
        });

        $( '.jewelry-type, .Wedding_Bands li.list-style, .earrings li.list-style, .Bracelets li.list-style, .Necklaces li.list-style, .Pearl_Jewelry li.list-style, .Watches li.list-style, .metals .box-list-2-li' ).on( 'click', function(){ 
            $( document ).scrollTop( 0 );
        } );

        /*
        *Ajax call on price change and jewelry brands
        */
        $( '#price_total_from' ).on('change keyup keydown',  function (event) {

            var price_total_from, price_total_to;

            price_total_from    = parseFloat($('#price_total_from').val());
            price_total_to      = parseFloat($('#price_total_to').val());

            if( ! isNaN( price_total_to ) && ( '' !== price_total_to && '' !== price_total_from ) ){

                if( price_total_from <= price_total_to || isNaN( price_total_from ) ){
                    $( '.price-error' ).hide();
                    $( '.price-to-error' ).hide();

                }else{
                    $( '.price-to-error' ).hide();
                    $( '.price-error' ).show();

                }

            }
            
            if( event.type == "change" ){
                $( '#jewelry_page_number' ).val( '1' );
                ajax_call();
                $( document ).scrollTop( 0 );
            }
        });

        $('#price_total_to').on('change keyup keydown',  function (event) {

            var price_total_from, price_total_to;

            price_total_from    = parseFloat($('#price_total_from').val());
            price_total_to      = parseFloat($('#price_total_to').val());

            if( ! isNaN( price_total_from ) && ( '' !== price_total_from && '' !== price_total_to ) ){

                if( price_total_to >= price_total_from || isNaN( price_total_to ) ){
                    $( '.price-error' ).hide();
                    $( '.price-to-error' ).hide();

                }else{
                    $( '.price-error' ).hide();
                    $( '.price-to-error' ).show();

                }

            }
            
            if( event.type == "change" ){
                $( '#jewelry_page_number' ).val( '1' );
                ajax_call();
                $( document ).scrollTop( 0 );
            }

        });

        $( "#jewelry_brands" ).on( 'change', function(){
                $( '#jewelry_page_number' ).val( '1' );
                ajax_call();
                $( document ).scrollTop( 0 );
        } );

        $('.jewelry_select_cbk').on('change',  function(event){
            event.preventDefault();
            $('input.jewelry-single-cbk:checkbox').prop('checked', $(this).prop("checked"));
        });

        /*
        *Schedule Import when click on schedule import button
        */
        $('#jewelry_schedule_import').on('click',  function(event){
            event.preventDefault();
            if(!$('input.jewelry-single-cbk:checkbox').is(':checked')){
                alert('Please select atleast one inventory to import');
                return false;
            }
            
            $(this).addClass('disabled');
            schedule_import();
        });
        
        /*
        *Call Ajax on Scroll
        */ 
        $(window).scroll(function() {
            var canBeLoaded = $('#jewelry_load_more').val();
            if($(window).scrollTop() == $(document).height() - $(window).height()  && canBeLoaded == 'true'  ) {
                ajax_call('lazyload');
            }
        });
    });

    /*
    *Hide show subtypes based on selection of the types
    */
    var hide_show_subtypes = function($jewelryType){
        
        var $active_type, $header, $content, $eachheader, $eachcontent;
        $active_type = $jewelryType.attr('data-types');
        $header = $(".sub_types."+$active_type+" .header");
        $content = $header.next();
        
        if($jewelryType.hasClass('active') ){


            /*Each loop to collapse other active subtypes*/
            $( "#inject_sub_types div.sub_types" ).each(function( index ) {

               // console.log( index + ": " + $( this ).text() );
                $eachheader = $(this).children('.header');
                $eachcontent = $eachheader.next();
                if($eachcontent.is(":visible")){
                    $eachheader.trigger('click');
                }

            });

            $(".sub_types."+$active_type).show();
            $header.trigger('click');
            
        }else{
            $( ".sub_types."+$active_type+ " input:checkbox" ).removeAttr('checked');
            $( ".sub_types."+$active_type+ " .custom-checkbox" ).closest('li').removeClass('active');
            $content = $header.next();
            if($content.is(":visible")){
                $header.trigger('click');
            }
            $(".sub_types."+$active_type).hide();
        }
    }

    /**
     *General Ajax Call to load data
    **/
    var ajax_call = function( executed_on ){

        if (typeof executed_on === "undefined" || executed_on === null) {
            executed_on = "onsearch"; 
        }
        
        jQuery('.jewelry_loader').addClass('center');
        if(executed_on == 'lazyload'){
            jQuery('.jewelry_loader').removeClass('center');
        }

        currentRequest = jQuery.ajax({
                            type : "post",
                            dataType : "json",
                            url : ajaxurl,
                            data : {
                                    action : "vdb_search", 
                                    search_data : $(".jewelry_inputs").serialize(),
                                    executed_on : executed_on
                                },

                            beforeSend : function()    {  
                                jQuery('.jewelry_loader').show();
                                if(currentRequest != null) {
                                    currentRequest.abort();
                                }
                            },    
                            success: function( response ) {
                                // console.log('Success');
                                // console.log(response);
                                
                                if( executed_on == 'lazyload'){
                                    $('#search_result_wrapper').append(response.html);
                                }else{
                                    $('#search_result_wrapper').html(response.html);    
                                }
                                
                                $('#total_jewelry_found_wrapper').html( ( "N" == response.total_jewelry_found ? '0' : response.total_jewelry_found ));
                                
                                $('#jewelry_page_number').val(response.page_number + 1 );

                                console.log( response.page_number );
                                console.log( response.total_pages );

                                /*Enable disable loading of data on scroll*/
                                if(response.page_number == response.total_pages || 0 === response.total_pages){
                                    $('#jewelry_load_more').val('false');
                                    console.log('disable load on scroll');
                                }else{
                                    $('#jewelry_load_more').val('true');
                                    console.log('enable load on scroll');
                                }
                                
                                jQuery('.jewelry_loader').hide();
                            }
                        })
    }


    /**
     *General Ajax Call to Schedule Import
    **/
    var schedule_import = function(){
        
        jQuery('.jewelry_loader').addClass('center');
        jQuery('.jewelry_loader').show();

        currentRequest = jQuery.ajax({
                            type : "post",
                            dataType : "json",
                            url : ajaxurl,
                            data : {
                                    action : "vdb_schedule_import", 
                                    search_data : $(".jewelry_inputs").serialize(),
                                    jewelry_ids : $('input.jewelry-single-cbk:checkbox:checked').serialize(),
                                },

                            beforeSend : function()    {           
                                if(currentRequest != null) {
                                    currentRequest.abort();
                                }
                            },    
                            success: function( response ) {
                                console.log('Success');
                                console.log(response);
                                jQuery('.jewelry_loader').hide();
                                jQuery('#jewelry_schedule_import').removeClass( 'disabled' );
                                $('#jewelry_page_number').val( '1' );
                                ajax_call();
                            }
                        })
    }

})( jQuery );


jQuery( '.search-result-grid-wrapper' ).ready( function(){
    console.log( 'Wrapper ready' );
    // Show hide count when selected the items
    jQuery( '.jewelry-single-cbk' ).on( 'click', function(){ 
        console.log( 'input checked' );
        var vdb_ji_selected_items = jQuery( '.search-result-grid-wrapper input:checked' ).length;

        if( 0 < vdb_ji_selected_items ){
            jQuery( '.vdb_ji_show_count' ).text( vdb_ji_selected_items + 'Items Selected' );
            jQuery( '.vdb_ji_show_count' ).show();
        }else{
            jQuery( '.vdb_ji_show_count' ).hide();
        }

    });
} );