<?php
class QuestionWidget extends WP_widget {
    public function __construct()
    {
        parent::__construct('pose_question_widget', 
            __('Widget Pose Question', 'question_widget_domain'),
            // widget description
            array ( 'description' => __( 'créé un textarea et un button pour la page poser les questions', 'question_widget_domain' ), )
        );
    }

    public function widget($args, $instance){
        $title = apply_filters( 'widget_title', $instance['title']);
        // before and after widget arguments are defined by themes
        echo $args['before_widget'];
        if ( ! empty( $title ) )
            echo $args['before_title'] . $title . $args['after_title'];
            // This is where you run the code and display the output
            echo  '
            <form action="http://localhost/wordpress/rien-ne-membarrasse//" method="post">
           
                <div id="add_text" class="form-group">
                    <label id="question_embarrassante" for="question_embarrassante">Pose moi une question embarrassante...</label><br>
                    <textarea id="question_embarrassante" name="question_embarrassante" rows="3" cols="33" required></textarea>
                </div>
                
                <button type="submit" class="btn btn-danger w-25">Entrer</button>
                
                </form>';
            echo $args['after_widget'];
    }

    public function form($instance){
        if ( isset( $instance[ 'title' ] ) )
        $title = $instance[ 'title' ];
        else
        $title = __( '', 'question_widget_domain' );
        
        ?>
        <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
        <input 
            class="widefat" 
            id="<?php echo $this->get_field_id( 'title' ); ?>" 
            name="<?php echo $this->get_field_name( 'title' ); ?>" 
            type="text" value="<?php echo esc_attr( $title ); ?>" />
        </p>
        <?php
    }

    public function update($newinstance, $oldinstance){
        return $newinstance;
    }
}
?>