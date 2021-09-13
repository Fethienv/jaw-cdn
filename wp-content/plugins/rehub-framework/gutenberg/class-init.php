<?php
namespace Rehub\Gutenberg;

defined('ABSPATH') OR exit;

final class Init {
    private static $instance = null;

    /** @return Assets */
    public static function instance(){
        if(is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    private function __construct(){
        add_filter('block_categories_all', array($this,'block_categories_filter'), 10, 2);

        Assets::instance();
        Blocks\Box::instance();
        Blocks\TitleBox::instance();
        Blocks\Heading::instance();
        Blocks\PostOfferbox::instance();
        Blocks\Offerbox::instance();
        Blocks\ReviewBox::instance();
        Blocks\ConsPros::instance();
        Blocks\Accordion::instance();
        Blocks\PostOfferListing::instance();
        Blocks\OfferListing::instance();
        Blocks\WCList::instance();
        Blocks\VersusTable::instance();
        Blocks\WCBox::instance();
        Blocks\Itinerary::instance();
        Blocks\Slider::instance();
        Blocks\PrettyList::instance();
        Blocks\PromoBox::instance();
        Blocks\ReviewHeading::instance();
        Blocks\ColorHeading::instance();
        Blocks\ComparisonTable::instance();
        Blocks\ComparisonItem::instance();
        Blocks\WCQuery::instance();
        Blocks\Howto::instance();
        Blocks\OfferListingFull::instance();
        Blocks\Colortitlebox::instance();
        Blocks\Countdown::instance();
        Blocks\Video::instance();
        Blocks\Toc::instance();
        Blocks\Metaget::instance();
        Blocks\Postelement::instance();
	    REST::instance();
    }

    public function block_categories_filter($categories, $post){
        array_splice($categories, 3, 0, array(
            array(
                'slug'  => 'helpler-modules',
                'title' => __('Rehub Helper Modules', 'rehub-framework'),
            )
        ));

        return $categories;
    }
}
