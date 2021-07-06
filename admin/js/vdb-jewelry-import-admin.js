(function( $ ) {
	'use strict';

	$(document).ready(function() {

        $(".accordion-wrapper .toggle").click(function () {
        
            $(this).parent().toggleClass("open").siblings().removeClass("open");
        
            if ($('.accordion-content').is(':visible')) {
                $(".accordion-content").slideUp(300);           
            }
        
            if ($(this).next(".accordion-content").is(':visible')) {
                $(this).next(".accordion-content").slideUp(300);
            } else {
                $(this).next(".accordion-content").slideDown(300);            
            }
        });
        
        $('ul.tabs li').click(function () {
            var tab_id = $(this).attr('data-tab');
            $('ul.tabs li').removeClass('current');
            $('.tab-content').removeClass('current');
            $(this).addClass('current');
            $("#" + tab_id).addClass('current');
        });
        
        $(".search-option-dropdown-header").on('click', function(){
            $(this).parent().toggleClass("active").siblings().removeClass("active");
        }); 
        
        $(".diamond-type-grid").on('click', function(){
            $(this).toggleClass("active");
        });
        
        $(".sort-ic").on("click", function () {
            $(".search-result-filter-popup").addClass("open");
            $("body").addClass("filter-open"); 
        });
        
        $(".popup-header-close").on("click", function () {
            $(".search-result-filter-popup").removeClass("open");
            $("body").removeClass("filter-open");
        });
        
        $(".search-option-tab").on("click", function () {
            $(this).toggleClass("search-tab-active");
            $(".search-result-middle").toggleClass("middle-full-section");
            $(".search-result-sidebar").removeClass("hide");
        });
        
        $(".view-result-mobile").on("click", function () {
            $(".search-option-tab").removeClass("search-tab-active");
            $(".search-result-middle").removeClass("middle-full-section");
        });
    });
})( jQuery );
