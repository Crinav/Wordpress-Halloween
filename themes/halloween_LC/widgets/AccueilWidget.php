<?php
class AccueilWidget extends WP_widget
{
    public function __construct()
    {
        parent::__construct(
            'accueil_widget',
            __('Widget Accueil', 'accueil_widget_domain'),
            // widget description
            array('description' => __('créé deux boutons pour l\'accueil', 'accueil_widget_domain'),)
        );
    }

    public function widget($args, $instance)
    {
        $title = apply_filters('widget_title', $instance['title']);
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if (!empty($title))
            echo $args['before_title'] . $title . $args['after_title'];

        // This is where you run the code and display the output
        echo  '
            <a href="poser-une-question"><button 
                class="btn btn-warning w-100" 
                type="button"  
                name="question">
                " Je préfère poser les questions "
            </button></a>
            
            <a href="theme"><button 
                class="btn btn-warning w-100 mt-4"
                type="button"  
                name="reponses">
                " Je préfère répondre aux questions "
            </button></a>
            ';
        echo $args['after_widget'];
    }
    public function form($instance)
    {
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = __('', 'accueil_widget_domain');

?>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
<?php
    }
    public function update($newinstance, $oldinstance)
    {
        return $newinstance;
    }
}
?>