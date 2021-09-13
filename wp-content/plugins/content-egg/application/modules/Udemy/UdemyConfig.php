<?php

namespace ContentEgg\application\modules\Udemy;

defined('\ABSPATH') || exit;

use ContentEgg\application\components\AffiliateParserModuleConfig;

/**
 * UdemyConfig class file
 *
 * @author keywordrush.com <support@keywordrush.com>
 * @link http://www.keywordrush.com/
 * @copyright Copyright &copy; 2017 keywordrush.com
 */
class UdemyConfig extends AffiliateParserModuleConfig {

    public function options()
    {
        $optiosn = array(
            'client_id' => array(
                'title' => 'Client Id <span class="cegg_required">*</span>',
                'description' => __('Sign up on udemy.com and go to <a href="https://www.udemy.com/user/edit-api-clients">API Clients</a> page in your user profile.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'Client Id'),
                    ),
                ),
                'section' => 'default',
            ),
            'client_secret' => array(
                'title' => 'Client Secret <span class="cegg_required">*</span>',
                'description' => __('Sign up on udemy.com and go to <a href="https://www.udemy.com/user/edit-api-clients">API Clients</a> page in your user profile.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'required'),
                        'when' => 'is_active',
                        'message' => sprintf(__('The field "%s" can not be empty.', 'content-egg'), 'Client Secret'),
                    ),
                ),
                'section' => 'default',
            ),
            'deeplink' => array(
                'title' => 'Deeplink',
                'description' => __('Set this parameter if you want to have commissions. Rakuten <a href="https://pubhelp.rakutenmarketing.com/hc/en-us/articles/201295755-Guide-to-Deep-Linking">Guide to Deep Linking</a>', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                    array(
                        'call' => array('\ContentEgg\application\components\Cpa', 'deeplinkPrepare'),
                        'type' => 'filter'
                    ),
                ),
                'section' => 'default',
            ),
            'entries_per_page' => array(
                'title' => __('Results', 'content-egg'),
                'description' => __('Number of results for one search query.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 8,
                'validator' => array(
                    'trim',
                    'absint',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
                        'arg' => 100,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results', 100),
                    ),
                ),
            ),
            'entries_per_page_update' => array(
                'title' => __('Results for updates and autoblogging', 'content-egg'),
                'description' => __('Number of results for automatic updates and autoblogging.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => 6,
                'validator' => array(
                    'trim',
                    'absint',
                    array(
                        'call' => array('\ContentEgg\application\helpers\FormValidator', 'less_than_equal_to'),
                        'arg' => 100,
                        'message' => sprintf(__('The field "%s" can not be more than %d.', 'content-egg'), 'Results', 100),
                    ),
                ),
            ),
            'language' => array(
                'title' => __('Language', 'content-egg'),
                'description' => __('Filter courses by <a href="http://www.loc.gov/standards/iso639-2/php/code_list.php">alpha-2 language code</a>.', 'content-egg'),
                'callback' => array($this, 'render_input'),
                'default' => '',
                'validator' => array(
                    'trim',
                ),
                'section' => 'default',
            ),
            'ordering' => array(
                'title' => __('Order', 'content-egg'),
                'description' => '',
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '' => __('Default', 'content-egg'),
                    'relevance' => 'Relevance',
                    'most-reviewed' => 'Most reviewed',
                    'highest-rated' => 'Highest rated',
                    'newest' => 'Newest',
                    'price-low-to-high' => 'Price low to high',
                    'price-high-to-low' => 'Price high tolow',
                ),
                'default' => '',
            ),
            'category' => array(
                'title' => __('Category', 'content-egg'),
                'description' => '',
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '' => __('All categories', 'content-egg'),
                    'Academics' => 'Academics',
                    'Business' => 'Business',
                    'Design' => 'Design',
                    'Development' => 'Development',
                    'Health & Fitness' => 'Health &amp; Fitness',
                    'IT & Software' => 'IT &amp; Software',
                    'Language' => 'Language',
                    'Lifestyle' => 'Lifestyle',
                    'Marketing' => 'Marketing',
                    'Music' => 'Music',
                    'Office Productivity' => 'Office Productivity',
                    'Personal Development' => 'Personal Development',
                    'Photography' => 'Photography',
                    'Teacher Training' => 'Teacher Training',
                    'Test Prep' => 'Test Prep',
                ),
                'default' => '',
            ),
            'subcategory' => array(
                'title' => __('Subcategory', 'content-egg'),
                'description' => __('Filter courses by primary subcategory.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '' => __('All subcategories', 'content-egg'),
                    '3D & Animation' => '3D & Animation', 'Advertising' => 'Advertising', 'Affiliate Marketing' => 'Affiliate Marketing', 'Analytics & Automation' => 'Analytics & Automation', 'Apple' => 'Apple', 'Arabic' => 'Arabic', 'Architectural Design' => 'Architectural Design', 'Arts & Crafts' => 'Arts & Crafts', 'Beauty & Makeup' => 'Beauty & Makeup', 'Black & White' => 'Black & White', 'Branding' => 'Branding', 'Business Law' => 'Business Law', 'Career Development' => 'Career Development', 'Chinese' => 'Chinese', 'College Entry Exam' => 'College Entry Exam', 'Commercial Photography' => 'Commercial Photography', 'Communications' => 'Communications', 'Content Marketing' => 'Content Marketing', 'Creativity' => 'Creativity', 'Dance' => 'Dance', 'Data & Analytics' => 'Data & Analytics', 'Databases' => 'Databases', 'Design Thinking' => 'Design Thinking', 'Design Tools' => 'Design Tools', 'Development Tools' => 'Development Tools', 'Dieting' => 'Dieting', 'Digital Marketing' => 'Digital Marketing', 'Digital Photography' => 'Digital Photography', 'E-Commerce' => 'E-Commerce', 'Educational Development' => 'Educational Development', 'English' => 'English', 'Entrepreneurship' => 'Entrepreneurship', 'Fashion' => 'Fashion', 'Finance' => 'Finance', 'Fitness' => 'Fitness', 'Food & Beverage' => 'Food & Beverage', 'French' => 'French', 'Game Design' => 'Game Design', 'Game Development' => 'Game Development', 'Gaming' => 'Gaming', 'General Health' => 'General Health', 'German' => 'German', 'Google' => 'Google', 'Grad Entry Exam' => 'Grad Entry Exam', 'Graphic Design' => 'Graphic Design', 'Growth Hacking' => 'Growth Hacking', 'Happiness' => 'Happiness', 'Hardware' => 'Hardware', 'Hebrew' => 'Hebrew', 'Home Business' => 'Home Business', 'Home Improvement' => 'Home Improvement', 'Human Resources' => 'Human Resources', 'Humanities' => 'Humanities', 'Industry' => 'Industry', 'Influence' => 'Influence', 'Instructional Design' => 'Instructional Design', 'Instruments' => 'Instruments', 'Interior Design' => 'Interior Design', 'International High School' => 'International High School', 'Intuit' => 'Intuit', 'IT Certification' => 'IT Certification', 'Italian' => 'Italian', 'Japanese' => 'Japanese', 'Landscape' => 'Landscape', 'Latin' => 'Latin', 'Leadership' => 'Leadership', 'Management' => 'Management', 'Marketing Fundamentals' => 'Marketing Fundamentals', 'Math & Science' => 'Math & Science', 'Media' => 'Media', 'Meditation' => 'Meditation', 'Memory & Study Skills' => 'Memory & Study Skills', 'Mental Health' => 'Mental Health', 'Microsoft' => 'Microsoft', 'Mobile Apps' => 'Mobile Apps', 'Mobile Photography' => 'Mobile Photography', 'Motivation' => 'Motivation', 'Music Fundamentals' => 'Music Fundamentals', 'Music Software' => 'Music Software', 'Music Techniques' => 'Music Techniques', 'Network & Security' => 'Network & Security', 'Non-Digital Marketing' => 'Non-Digital Marketing', 'Nutrition' => 'Nutrition', 'Operating Systems' => 'Operating Systems', 'Operations' => 'Operations', 'Oracle' => 'Oracle', 'Other' => 'Other', 'Parenting & Relationships' => 'Parenting & Relationships', 'Personal Brand Building' => 'Personal Brand Building', 'Personal Finance' => 'Personal Finance', 'Personal Transformation' => 'Personal Transformation', 'Pet Care & Training' => 'Pet Care & Training', 'Photography Fundamentals' => 'Photography Fundamentals', 'Photography Tools' => 'Photography Tools', 'Portraits' => 'Portraits', 'Portuguese' => 'Portuguese', 'Product Marketing' => 'Product Marketing', 'Production' => 'Production', 'Productivity' => 'Productivity', 'Programming Languages' => 'Programming Languages', 'Project Management' => 'Project Management', 'Public Relations' => 'Public Relations', 'Real Estate' => 'Real Estate', 'Religion & Spirituality' => 'Religion & Spirituality', 'Russian' => 'Russian', 'Safety & First Aid' => 'Safety & First Aid', 'Sales' => 'Sales', 'Salesforce' => 'Salesforce', 'SAP' => 'SAP', 'Search Engine Optimization' => 'Search Engine Optimization', 'Self Defense' => 'Self Defense', 'Self Esteem' => 'Self Esteem', 'Social Media Marketing' => 'Social Media Marketing', 'Social Science' => 'Social Science', 'Software Engineering' => 'Software Engineering', 'Software Testing' => 'Software Testing', 'Spanish' => 'Spanish', 'Sports' => 'Sports', 'Strategy' => 'Strategy', 'Stress Management' => 'Stress Management', 'Teaching Tools' => 'Teaching Tools', 'Test Taking Skills' => 'Test Taking Skills', 'Travel' => 'Travel', 'Travel Photography' => 'Travel Photography', 'User Experience' => 'User Experience', 'Video & Mobile Marketing' => 'Video & Mobile Marketing', 'Video Design' => 'Video Design', 'Vocal' => 'Vocal', 'Web Design' => 'Web Design', 'Web Development' => 'Web Development', 'Wedding Photography' => 'Wedding Photography', 'Wildlife Photography' => 'Wildlife Photography', 'Yoga' => 'Yoga',
                ),
                'default' => '',
            ),
            'price' => array(
                'title' => __('Price', 'content-egg'),
                'description' => __('Rank courses by price-paid, or price-free.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '' => __('All', 'content-egg'),
                    'price-paid' => __('Paid', 'content-egg'),
                    'price-free' => __('Free', 'content-egg'),
                ),
                'default' => 'price-paid',
            ),
            'is_affiliate_agreed' => array(
                'title' => __('Affiliate agreed', 'content-egg'),
                'description' => __('Filter courses that are affiliate agreed.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => true,
            ),
            'is_fixed_priced_deals_agreed' => array(
                'title' => __('Fixed priced deal', 'content-egg'),
                'description' => __('Filter courses that are fixed priced deal agreed.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
            ),
            'is_percentage_deals_agreed' => array(
                'title' => __('Percentage deals', 'content-egg'),
                'description' => __('Filter courses that are percentage deal agreed.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
            ),
            'has_closed_caption' => array(
                'title' => __('Closed caption', 'content-egg'),
                'description' => __('Filter courses that has closed caption.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
            ),
            'has_coding_exercises' => array(
                'title' => __('Coding exercises', 'content-egg'),
                'description' => __('Filter courses that has coding exercises.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
            ),
            'has_simple_quiz' => array(
                'title' => __('Simple quiz', 'content-egg'),
                'description' => __('Filter courses that has simple quiz.', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
            ),
            'instructional_level' => array(
                'title' => __('Instructional level', 'content-egg'),
                'description' => __('Filter courses by instructional level.', 'content-egg'),
                'callback' => array($this, 'render_dropdown'),
                'dropdown_options' => array(
                    '' => __('Default', 'content-egg'),
                    'all' => __('All', 'content-egg'),
                    'beginner' => __('Beginner', 'content-egg'),
                    'intermediate' => __('Intermediate', 'content-egg'),
                    'expert' => __('Expert', 'content-egg'),
                ),
                'default' => '',
            ),
            'save_img' => array(
                'title' => __('Save images', 'content-egg'),
                'description' => __('Save images on server', 'content-egg'),
                'callback' => array($this, 'render_checkbox'),
                'default' => false,
                'section' => 'default',
            ),
        );
        return array_merge(parent::options(), $optiosn);
    }

}
