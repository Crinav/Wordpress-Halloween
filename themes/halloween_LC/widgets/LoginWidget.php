<?php
class LoginWidget extends WP_widget
{
    public function __construct()
    {
        parent::__construct(
            'login_widget',
            __('Widget login', 'login_widget_domain'),
            // widget description
            array('description' => __('créé un input pour la page login', 'login_widget_domain'),)
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
            <form action="" method="POST">
           
            <div class="form-group ">
                <input type="text" class="form-control w-75 btn-login" id="pseudo" name="pseudo" placeholder="Pseudo" required>
            </div>
            <div class="form-group">
                <input type="email" class="form-control w-75 btn-login" id="email" name="email"  aria-describedby="emailHelp" placeholder="Entrez votre email" required>
            </div>
            
            <button type="submit" class="btn btn-warning w-25">Entrer</button>
            
            </form>';
        echo $args['after_widget'];
    }

    public function form($instance)
    {
        if (isset($instance['title']))
            $title = $instance['title'];
        else
            $title = __('', 'login_widget_domain');

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