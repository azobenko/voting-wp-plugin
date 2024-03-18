<?php
/**
 * Simple Voting plugin's core class
 */
class SimpleVoting {

	/**
	 * The current version of the plugin
	 *
	 * @since 1.0.0
	 */
	private string $version;

	/**
	 * The unique identifier of this plugin
	 *
	 * @since 1.0.0
	 */
	private string $name;

	/**
	 * Define core functionality, define plugin's version and other variables
	 */
	public function __construct()
	{
		$this->version = '1.0.0';
		$this->name = 'simple-voting';
	}

	/**
	 * Run main functionality
	 *
	 * @return void
	 * @since 1.0.0
	 * @acces public
	 */
	public function run(): void
	{
		//Frontend part
		if ( !is_admin() )  {
            add_filter( 'the_content', [ $this , 'insert_voting' ] );
            add_action( 'wp_enqueue_scripts', [ $this, 'public_plugin_assets' ] );
			$this->register_ajax();
		} else {
		//Backend part
			$this->connect_admin_scripts_and_styles();
		}
	}

    /**
     * Function for inserting voting block
     *
     * @param $content
     * @return string
     * @since 1.0.0
     * @acces public
     */
    public function insert_voting( $content ): string
    {
        if ( get_post_type() == 'post' && is_single() ) {
            $custom_content = $this->get_voting_html();
            $content .= $custom_content;
        }

        return $content;
    }

    /**
     * Function for rendering voting html
     *
     * @return string
     * @since 1.0.0
     * @acces public
     */
    private function get_voting_html()
    {
        $out = '<div class="flex">[voting]</div>';
        return $out;
    }

    /**
     * Function for registering ajax action and callback
     *
     * @return void
     * @since 1.0.0
     * @acces public
     */
    private function register_ajax(): void
    {
        add_action( 'wp_ajax_sv_save_vote', [$this, 'sv_save_vote_cb'] );
        add_action( 'wp_ajax_nopriv_sv_save_vote', [$this, 'sv_save_vote_cb'] );
    }

	/**
	 * Callback function for saving user's vote
	 *
	 * @return void
	 * @since 1.0.0
	 * @acces public
	 */
	public function sv_save_vote_cb() {
		if ( $_POST['action'] == 'sv_save_vote' && isset($_POST['post']) && isset($_POST['nonce']) ) {
			wp_send_json_success( '' );
		} else {
			wp_send_json_error('Wrong data');
		}
	}

	/**
	 * Adds action function for the admin assets
	 *
	 * @return void
	 * @since 1.0.0
	 * @acces private
	 */
	private function connect_admin_scripts_and_styles(): void
	{
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_plugin_assets' ] );
	}

	/**
	 * Enqueue scripts and styles for admin
	 *
	 * @return void
	 * @since 1.0.0
	 * @acces public
	 */
	public function admin_plugin_assets(): void
	{
		if ( 'post' == get_current_screen()->id ) {
			wp_enqueue_style( $this->name, plugins_url( '/pub/sv-styles.css' , __DIR__ ), null, $this->version );
		}
	}

	/**
	 * Enqueue scripts and styles for public
	 *
	 * @return void
	 * @since 1.0.0
	 * @acces public
	 */
    public function public_plugin_assets(): void
	{
        global $post;
        $args = [
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce('sv_save_vote'),
            'post_id' => $post->ID,
        ];

        if ( get_post_type() == 'post' && is_single() ) {
            wp_enqueue_style( 'dashicons' );
            wp_enqueue_style( $this->name, plugins_url( '/pub/pub-sv-styles.css' , __DIR__ ), ['dashicons'], $this->version );
            wp_enqueue_script( $this->name, plugins_url( '/pub/pub-sv-scripts.js' , __DIR__ ), ['jquery'], $this->version, true );
            wp_localize_script( $this->name, 'simpleVoting', $args );
        }
	}
}