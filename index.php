<?php
/*
    Plugin Name: Example CPT
    Description: This is an example for the development of a CPT
    version: 1.0
    Author: diego.andino93@gmail.com
*/

namespace ExampleCPT;

class ExampleCPT{
    public function __construct(){
        add_action('init', array($this, 'registerExampleCPT'));
        add_action('add_meta_boxes', array($this, 'addMetaBox'));
        add_action('rest_api_init', array($this, 'addExampleMetaIntoRestApi'));
        add_action('save_post', array($this, 'saveExampleMeta'));
    }
    
    /**
     * Init method to Register the CPT and the hooks
     */
    public function registerExampleCPT (){
        
        $labels = $this->setLabels();
        $args = $this->setArguments($labels);
        register_post_type('example_cpt', $args);
        
    }

    /**
     * Fn in charge to Add the custom meta box for the CPT
     */
    public function addMetaBox(){
        add_meta_box(
            'example-meta',
            'Example Meta',
            array($this, 'renderExampleMetaField'),
            'example_cpt',
            'normal',
            'high'
        );
    }

    /**
     * Fn in charge to render the custom meta field.
     *
     * @param $post.
     */
    public function renderExampleMetaField($post){
        $example_meta = get_post_meta($post->ID, 'example_meta', true);
        ?>
        <label for='example_meta'>Example meta:</label>
        <input type='text' name="example_meta" id="example_meta" value="<?php echo $example_meta ?>">
        <?php
    }

    /**
     * Fn in charge to save the custom meta field when the post is saved.
     *
     * @param int $post_id
     */
    public function saveExampleMeta($post_id){
        if( defined("DOING_AUTOSAVE") && DOING_AUTOSAVE ){
            return $$post_id;
        }
        if (!isset($_POST['example_meta'])) {
            return $post_id;
        }

        if (get_post_type($post_id) == 'example_cpt'){
            $data = sanitize_text_field($_POST['example_meta']);
            update_post_meta($post_id, 'example_meta', $data);
        }
    }

    /**
     * Fn to show the meta field in the rest api.
     * @param string $object
     * 
     * @return callback
     */
    public function addExampleMetaIntoRestApi() {
        register_rest_field('example_cpt', 'example_meta', array(
            'get_callback' => array($this, 'getExampleMetaValue'),
            'update_callback' => array($this, 'updateExampleMetaValue'),
            'schema' => array(
                'type' => 'string',
                'description' => 'CPT example meta field',
                'context' => array('view', 'edit'),
            ),
        ));
    }


    /**
     * Fn to get the value of the meta field using rest.
     * @param array $object
     * 
     * @return callback
     */
    public function getExampleMetaValue($object) {
        return get_post_meta($object['id'], 'example_meta', true);
    }

    /**
     * Fn to update the value of the meta field using rest.
     * @param int $value
     * @param array $object
     * @param boolean $field_name
     * 
     * @return callback
     */
    public function updateExampleMetaValue($value, $object, $field_name) {
        return update_post_meta($object['id'], $field_name, $value);
    }

    /**
     * Label Setter to register the CPT.
     *
     * @return array
     */
    private function setLabels(): array
    {
        return array(
            'name' => _x( 'Example cpts', 'Example cpt General Name', 'example_cpt' ),
            'singular_name' => _x( 'Example CPT', 'Example CPT Singular Name', 'example_cpt' ),
            'all_items' => __( 'All Items', 'example_cpt' ),
            'add_new_item' => __( 'Add New Example', 'example_cpt' ),
            'add_new' => __( 'Add Example', 'example_cpt' ),
            'new_item' => __( 'New Example', 'example_cpt' ),
            'edit_item' => __( 'Edit Example', 'example_cpt' ),
            'update_item' => __( 'Update Example', 'example_cpt' ),
            'view_item' => __( 'View Example', 'example_cpt' ),
            'view_items' => __( 'View Example', 'example_cpt' ),
            'search_items' => __( 'Search Example', 'example_cpt' ),
        );
    }

    /**
     * Argument setter to register the CPT.
     * @param array $label
     * @return array
     */
    private function setArguments($labels): array
    {
        return array(
            'labels' => $labels,
            'public' => true,
            'supports' => array(
                'title',
                'editor',
                'custom-fields',
            ),
            'capability_type' => 'post',
            'map_meta_cap' =>true,
            'show_in_rest' => true, // Argument to display the field on the rest endpoint
        );
    }
}

new ExampleCPT();
?>

