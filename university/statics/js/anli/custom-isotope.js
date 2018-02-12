jQuery(document).ready(function() {

    // PORTFOLIO GRID IN AND OUT EFFECT //
    jQuery('.grid-item-on-hover').hover(function() {
        jQuery(this).animate({
            opacity: 0.9
        },
        200);
    },
    function() {
        jQuery(this).animate({
            opacity: 0
        },
        200);
    });

    // SET HEIGHT AS MAX 
    var max = 0;
    jQuery('#portfolio-wrapper .four-columns li, #portfolio-wrapper .three-columns li, #portfolio-wrapper .two-columns li').each(function() {
        max = Math.max(max, jQuery(this).outerHeight());
    }).height(max + 1);

    // ISOTOPE SCRIPTS FOR PORTFOLIO FILTER, PORTFOLIO GRID LAYOUT, BLOG MASONRY //
    var $container = jQuery('.page-template-template-portfolio-php .grid');
    var $portfolio_container = jQuery('.portfolio-gallery');
    var $gallery_container = jQuery('.gallery-page');
    var $four_container = jQuery('.four-columns, .three-columns, .two-columns');

    // blog-isotope
    var $blog_container = jQuery('#masonry-blog');
    // blog masonry layout
//  $blog_container.imagesLoaded(function() {
//      $blog_container.isotope({
//          itemSelector: '.masonry-post',
//          animationEngine: 'jquery',
//          columnWidth: 300,
//          gutterWidth: 20
//      });
//  });

//  bloggingisotope = function() {
//
//      $blog_container.isotope({
//          itemSelector: '.masonry-post',
//          animationEngine: 'jquery',
//          columnWidth: 300,
//          gutterWidth: 20
//      });
//  };
//  jQuery(window).smartresize(bloggingisotope);

//  // gallery masonry layout
//  $gallery_container.imagesLoaded(function() {
//      $gallery_container.isotope({
//          itemSelector: 'li',
//          animationEngine: 'jquery',
//          gutterWidth: 20
//      });
//  });
//
//  // portfolio gallery masonry option
//  $portfolio_container.imagesLoaded(function() {
//      $portfolio_container.isotope({
//          itemSelector: 'a',
//          animationEngine: 'jquery',
//          gutterWidth: 20
//      });
//  });

    // portfolio grid layout and filtering
//  $container.isotope({
//      itemSelector: 'li',
//      animationEngine: 'jquery',
//      masonry: {
//          columnWidth: 5
//      }
//  });

    // portfolio filtering
//  $four_container.isotope({
//      itemSelector: 'li',
//      animationEngine: 'jquery'
//  });

    var $optionSets = jQuery('#options .option-set'),
    $optionLinks = $optionSets.find('a');

    $optionLinks.click(function() {
        var $this = jQuery(this);
        // don't proceed if already selected
        if ($this.hasClass('selected')) {
            return false;
        }
        var $optionSet = $this.parents('.option-set');
        $optionSet.find('.selected').removeClass('selected');
        $this.addClass('selected');

        // make option object dynamically, i.e. { filter: '.my-filter-class' }
        var options = {},
        key = $optionSet.attr('data-option-key'),
        value = $this.attr('data-option-value');
        // parse 'false' as false boolean
        value = value === 'false' ? false: value;
        options[key] = value;
        if (key === 'layoutMode' && typeof changeLayoutMode === 'function') {
            // changes in layout modes need extra logic
            changeLayoutMode($this, options)
        } else {
            // otherwise, apply new options
            $container.isotope(options);
            $four_container.isotope(options);
        }

        return false;
    });
});