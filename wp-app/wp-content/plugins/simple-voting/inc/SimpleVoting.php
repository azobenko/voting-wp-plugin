<?php

/**
 * Simple Voting plugin's core class
 */
class SimpleVoting
{

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
     * Define plugin's version and name
     */
    public function __construct()
    {
        $this->version = '1.0.6';
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
        if (!is_admin()) {
            add_filter('the_content', [$this, 'insert_voting']);
            add_action('wp_enqueue_scripts', [$this, 'public_plugin_assets']);
        } else {
            //Backend part
            add_action('admin_enqueue_scripts', [$this, 'admin_plugin_assets']);
            add_action('add_meta_boxes', [$this, 'add_sv_meta_box']);
        }
        //Registering of AJAX
        $this->register_ajax();
    }

    /**
     * Creates meta-box on post-edit page
     *
     * @return void
     * @since 1.0.0
     * @acces public
     */
    public function add_sv_meta_box(): void
    {
        add_meta_box(
            'sv_box',
            __('Usefulness of this post', $this->name),
            [$this, 'display_sv_meta_box_content'],
            'post',
            'side',
            'high'
        );
    }

    /**
     * Render meta-box content
     *
     * @return void
     * @since 1.0.0
     * @acces public
     */
    public function display_sv_meta_box_content(): void
    {
        $stats = get_post_meta(get_the_ID(), 'sv_votes', true);
        if (!empty($stats)) {
            $stats_arr = explode(',', $stats);
            $y = intval(($stats_arr[1] * 100) / $stats_arr[0]);
            $n = 100 - $y;
            $t = $stats_arr[0];
            echo "<div class='sv-panel'><div>Total votes: {$t}</div><div><span>{$y}%</span><span>{$n}%</span></div></div>";
        } else {
            echo '<div class="empty-panel">' . __('No one has voted for this post yet', $this->name) . '</div>';
        }
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
        if ('post' == get_current_screen()->id) {
            wp_enqueue_style($this->name, plugins_url('/pub/adm-sv-styles.css', __DIR__), null, $this->version);
        }
    }

    /**
     * Rendering voting block
     *
     * @return string
     * @since 1.0.0
     * @acces private
     */
    private function get_voting_html(): string
    {
        //Check for the Cookies
        if ($this->is_voted()) {
            $stats = get_post_meta(get_the_ID(), 'sv_votes', true);
            if (!empty($stats)) {
                $out = $this->get_voting_with_stats($stats);
            } else {
                $out = $this->get_empty_voting();
            }
        } else {
            $out = $this->get_empty_voting();
        }
        return $out;
    }

    /**
     * Rendering voting statistics
     *
     * @param $stats string
     * @return string
     * @since 1.0.0
     * @acces private
     */
    private function get_voting_with_stats(string $stats): string
    {
        $stats_arr = explode(',', $stats);
        $q = __('Thank you for your feedback.', $this->name);
        $y = intval(($stats_arr[1] * 100) / $stats_arr[0]);
        $n = 100 - $y;

        if ($_COOKIE['alreadyVoted'] == 'vote-up') {
            return "<div class='sv-panel voted'><span>{$q}</span><span class='voted' id='vote-up'>{$y}%</span><span id='vote-down'>{$n}%</span></div>";
        } else {
            return "<div class='sv-panel voted'><span>{$q}</span><span id='vote-up'>{$y}%</span><span class='voted' id='vote-down'>{$n}%</span></div>";
        }
    }

    /**
     * Rendering starting empty voting block
     *
     * @return string
     * @since 1.0.0
     * @acces private
     */
    private function get_empty_voting(): string
    {
        //Ready for translations
        $q = __('Was this article helpful?', $this->name);
        $y = __('Yes', $this->name);
        $n = __('No', $this->name);

        return "<div class='sv-panel'><span>{$q}</span><span id='vote-up'>{$y}</span><span id='vote-down'>{$n}</span></div>";
    }

    /**
     * Checks for Cookies - if User has already voted
     *
     * @return bool
     * @since 1.0.0
     * @acces private
     */
    private function is_voted(): bool
    {
        $key = 'alreadyVoted';
        return (array_key_exists($key, $_COOKIE) && $_COOKIE[$key]);
    }

    /**
     * Registering ajax action and callback
     *
     * @return void
     * @since 1.0.0
     * @acces private
     */
    private function register_ajax(): void
    {
        add_action('wp_ajax_sv_save_vote', [$this, 'sv_save_vote_cb']);
        add_action('wp_ajax_nopriv_sv_save_vote', [$this, 'sv_save_vote_cb']);
    }

    /**
     * Function for inserting voting block
     *
     * @param $content string
     * @return string
     * @since 1.0.0
     * @acces public
     */
    public function insert_voting(string $content): string
    {
        if (get_post_type() == 'post' && is_single()) {
            $custom_content = $this->get_voting_html();
            $content .= $custom_content;
        }

        return $content;
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
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('sv_save_vote'),
            'post_id' => $post->ID,
            'stats' => get_post_meta($post->ID, 'sv_votes', true),
            'text' => __('Thank you for your feedback.', $this->name)
        ];

        if (get_post_type() == 'post' && is_single()) {
            wp_enqueue_style('dashicons');
            wp_enqueue_style($this->name, plugins_url('/pub/pub-sv-styles.css', __DIR__), ['dashicons'], $this->version);
            wp_enqueue_script($this->name, plugins_url('/pub/pub-sv-scripts.js', __DIR__), ['jquery'], $this->version, true);
            wp_localize_script($this->name, 'simpleVoting', $args);
        }
    }

    /**
     * Callback function for saving user's vote
     *
     * @return void
     * @since 1.0.0
     * @acces public
     */
    public function sv_save_vote_cb(): void
    {
        if (wp_verify_nonce($_POST['nonce'], 'sv_save_vote')) {

            if ($_POST['action'] == 'sv_save_vote' && !empty($_POST['post']) && !empty($_POST['vote'])) {

                $stats = get_post_meta(intval($_POST['post']), 'sv_votes', true);
                if (empty($stats)) {
                    $stats = ($_POST['vote'] == 'vote-up') ? '1,1' : '1,0';
                } else {
                    $stats_arr = explode(',', $stats);
                    $stats_arr[0] = intval($stats_arr[0]) + 1;
                    if ($_POST['vote'] == 'vote-up') $stats_arr[1] = intval($stats_arr[1]) + 1;
                    $stats = implode(',', $stats_arr);
                }

                update_post_meta(intval($_POST['post']), 'sv_votes', $stats);
                wp_send_json_success($stats);

            } else {
                wp_send_json_error('Wrong data');
            }
        } else {
            wp_send_json_error('Security Error');
        }
    }
}