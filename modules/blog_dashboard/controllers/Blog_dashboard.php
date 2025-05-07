<?php
class Blog_dashboard extends Trongate {

    private $picture_fallback = 'blog_posts_module/img/fallback_img.jpg'; // '' for no pic

    /**
     * Dashboard View
     * 
     */
    function index() {
        $token = $this->_make_sure_allowed();

        // set timezone
        //date_default_timezone_set('UTC');

        $data = $this->_get_dashboard_data();

        // get user data
        $this->module('trongate_tokens');
        $user_id = $this->_get_current_admin_id($token);
        if ($user_id === false) {
            // Token invalid or user not found
            redirect('trongate_administrators/login');
        }

        $sql = "
            SELECT ta.username, tu.user_level_id
            FROM trongate_administrators ta
            JOIN trongate_users tu ON ta.trongate_user_id = tu.id
            WHERE ta.trongate_user_id = :user_id
        ";
        $params = ['user_id' => $user_id];
        $user_data = $this->model->query_bind($sql, $params, 'object');
        
        if (!empty($user_data)) {
            $data['username'] = $user_data[0]->username;
            $data['user_level_id'] = $user_data[0]->user_level_id;
            $data['user_id'] = $user_id;
        } else {
            $data['username'] = 'System';
            $data['user_level_id'] = 0;
            $data['user_id'] = 0;
        }

        // Token
        $data['token'] = $token;

        // FullCalendar-Skripte über additional_includes hinzufügen
        $additional_includes_top[] = '<link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet" />';
        $additional_includes_btm[] = '<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>';
        $data['additional_includes_top'] = $additional_includes_top;
        $data['additional_includes_btm'] = $additional_includes_btm;

        $data['headline'] = 'Dashboard';
        $data['subheadline'] = 'Welcome to the Dashboard.';
        $data['infoheadline'] = 'Get information and statistics of your blog.';

        $data['view_file'] = 'index_view';
        $this->template('tiny_bootstrap', $data);
    }

    private function _get_dashboard_data(): array {
        $data = [];

        // Posts, published and total
        $data = array_merge($data, $this->_get_post_details());

        // Categories
        $data = array_merge($data, $this->_get_category_details());

        // Tags
        $data = array_merge($data, $this->_get_tag_details());

        // Sources
        $data = array_merge($data, $this->_get_source_details());

        // Pages
        $data = array_merge($data, $this->_get_page_details());

        // Admins
        $data = array_merge($data, $this->_get_admin_details());

        // Comments
        $data = array_merge($data, $this->_get_comment_details());

        // Calendar Events
        $data = array_merge($data, $this->_get_calendar_events());

        // Pictures
        $data = array_merge($data, $this->_get_picture_details());

        // Recent Posts - ... moving widgets to their controllers ...
        #$this->module('blog_posts');
        #$data['recent_posts'] = $this->blog_posts->get_recent_posts_widget();

        # $data = array_merge($data, $this->_get_enquiries_details());

        # $data['enquiries_cnt'] = $this->_get_total_count('enquiries');

        return $data;
    }

    /**
     * Retrieves the total number of posts and the number of published posts from the database.
     *
     * This private method executes a SQL query to count published posts and combines it with
     * the total post count obtained from another method (_get_total_count). The result is
     * returned as an associative array containing both counts.
     *
     * @return array An associative array with keys 'num_posts' (total posts) and
     *               'num_published_posts' (published posts).
     */
    private function _get_post_details(): array {
        $sql = "SELECT COUNT(*) as total FROM blog_posts WHERE published = 1";
        $result = $this->model->query($sql, 'object'); // Fetch count of published posts
        $data['num_published_posts'] = $result[0]->total;

        // Combine total post count with published post count
        $data = [
            'num_posts' => $this->_get_total_count('blog_posts'),
            'num_published_posts' => $result[0]->total
        ];

        return $data;
    }

    private function _get_category_details(): array {
        $sql = "SELECT bc.id, bc.title, COUNT(bp.id) AS post_count
                FROM blog_categories bc
                LEFT JOIN blog_posts bp ON bp.blog_categories_id = bc.id
                GROUP BY bc.id, bc.title
                ORDER BY bc.title LIMIT 99";

        return [
            'num_blog_categories' => $this->_get_total_count('blog_categories'),
            'categories' => $this->model->query($sql, 'object')
        ];
    }

    private function _get_tag_details(): array {
        $sql = "SELECT bt.name, COUNT(bp.id) AS post_count
                FROM blog_tags bt
                LEFT JOIN associated_blog_tags_and_blog_posts abtp ON abtp.blog_tags_id = bt.id
                LEFT JOIN blog_posts bp ON bp.id = abtp.blog_posts_id
                GROUP BY bt.id, bt.name
                ORDER BY bt.name LIMIT 99";

        return [
            'num_blog_tags' => $this->_get_total_count('blog_tags'),
            'tags' => $this->model->query($sql, 'object')
        ];
    }

    private function _get_source_details(): array {
        $sql = "SELECT bs.author, COUNT(bp.id) AS post_count
                FROM blog_sources bs
                LEFT JOIN blog_posts bp ON bp.blog_sources_id = bs.id
                GROUP BY bs.id, bs.author
                ORDER BY bs.author LIMIT 5";

        return [
            'num_blog_sources' => $this->_get_total_count('blog_sources'),
            'sources' => $this->model->query($sql, 'object')
        ];
    }

    private function _get_picture_details(): array {
        $sql = "SELECT bpict.picture, COUNT(bp.id) AS post_count
                FROM blog_pictures bpict
                LEFT JOIN blog_posts bp ON bp.id = bpict.target_module_id AND bpict.target_module = 'blog_posts'
                WHERE bpict.target_module = 'blog_posts'
                GROUP BY bpict.picture
                ORDER BY bpict.picture LIMIT 5";

        return [
            'num_blog_pictures' => $this->_get_total_count('blog_pictures'),
            'pictures' => $this->model->query($sql, 'object')
        ];
    }

    private function _get_page_details(): array {
        return [
            'num_trongate_pages' => $this->_get_total_count('trongate_pages'),
            'pages' => $this->model->query("SELECT url_string FROM trongate_pages ORDER BY url_string LIMIT 5", 'object')
        ];
    }

    /**
     * Get admin details with post counts.
     *
     * @return array Array containing the number of admins and their details with post counts.
     */
    private function _get_admin_details(): array {
        $sql = "SELECT ta.username, COUNT(bp.id) AS post_count
                FROM trongate_administrators ta
                LEFT JOIN blog_posts bp ON bp.created_by = ta.id
                GROUP BY ta.id, ta.username
                ORDER BY ta.username LIMIT 5";

        return [
            'num_admins' => $this->_get_total_count('trongate_administrators'),
            'admins' => $this->model->query($sql, 'object')
        ];
    }

    private function _get_comment_details(): array {
        $sql = "SELECT tc.comment, COUNT(bp.id) AS post_count
                FROM trongate_comments tc
                LEFT JOIN blog_posts bp ON bp.id = tc.update_id AND tc.target_table = 'blog_posts'
                WHERE tc.target_table = 'blog_posts'
                GROUP BY tc.id, tc.comment
                ORDER BY tc.date_created DESC LIMIT 5";

        return [
            'num_comments' => $this->_get_total_count('trongate_comments'),
            'comments' => $this->model->query($sql, 'object')
        ];
    }

// ---------------------------

    private function _get_calendar_events(): array {
        $sql = "SELECT DATE(date_published) as start, 
                       COUNT(*) as post_count 
                FROM blog_posts 
                WHERE published = 1 AND date_published IS NOT NULL 
                GROUP BY DATE(date_published)";
        $rows = $this->model->query($sql, 'array');

        $events = [];
        foreach ($rows as $row) {
            $events[] = [
                'start' => $row['start'],
                'title' => $row['post_count'] . ' Post' . ($row['post_count'] > 1 ? 's' : ''),
                'allDay' => true
            ];
        }

        return ['calendar_events' => $events];
    }

// ---------------------------

    private function _fetch_calendar_events($days) {
        $this->_make_sure_allowed();

        // Validierung des $days-Parameters
        if (!is_numeric($days) || $days <= 0 || $days > 365) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Invalid days parameter. Must be a positive number between 1 and 365.']);
            die();
        }

        $sql = "
            SELECT 
                DATE(p.date_published) as publish_date,
                COUNT(*) as post_count
            FROM blog_posts p
            WHERE p.published = 1
            AND p.date_published >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
            GROUP BY DATE(p.date_published)
        ";
        $params = ['days' => (int) $days];
        $rows = $this->model->query_bind($sql, $params, 'object');

        $calendar_events = [];
        foreach ($rows as $row) {
            $calendar_events[] = [
                'title' => $row->post_count . ' Post(s)',
                'start' => $row->publish_date,
                'allDay' => true,
                'backgroundColor' => '#1dd2af',
                'borderColor' => '#1dd2af'
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($calendar_events);
    }

    /** already moved to blog_posts
     * 
     */
    function get_posts_by_date($date) {
        $this->_make_sure_allowed();

        // format date
        $date = date('Y-m-d', strtotime($date));
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            header('Content-Type: application/json');
            http_response_code(400);
            echo json_encode(['error' => 'Invalid date format']);
            die();
        }

        // db query
        $sql = "
            SELECT title, date_published
            FROM blog_posts
            WHERE published = 1
            AND DATE(date_published) = ?
        ";
        $rows = $this->model->query_bind($sql, [$date], 'object');

        $posts = [];
        if ($rows) {
            foreach ($rows as $row) {
                $posts[] = [
                    'title' => $row->title,
                    'date_published' => $row->date_published
                ];
            }
        }

        header('Content-Type: application/json');
        echo json_encode($posts);
    }


    /** Goes in each controller ...
     * Retrieves the total count of rows in a specified database table.
     *
     * This private method executes a SQL COUNT query on the given table and returns
     * the result as an integer. It assumes the table exists and the model supports
     * the query method with an 'object' return type.
     *
     * @param string $table The name of the database table to count rows from.
     * @return int The total number of rows, or 0 if the table is empty or query fails.
     * @throws \RuntimeException If the database query fails or the table is invalid.
     */
    private function _get_total_count(string $table): int {
        $sql = "SELECT COUNT(*) as total FROM $table";
        $result = $this->model->query($sql, 'object');
        return $result ? (int) $result[0]->total : 0;
    }

    /** Widgets are located in their origin controllers
     * Example of a widget located in this controller, 
     * note that all widgets are located in their origin controllers
     * due to possible settings variables.
     * 
     * Returns HTML for a widget showing the 4 latest posts with titles, dates, and images.
     *
     * This method queries the posts table for the latest entries based on their publication date,
     * prepares the data including titles, URLs, publication dates, and images, and renders it
     * into a view template. If a post has an associated picture, it constructs the full URL using
     * the picture directory and post ID; otherwise, it falls back to a default image.
     *
     * @return string HTML string for the widget or empty string if no posts are found.
     */
    public function local_widget_recent_posts() {
        $sql = '
            SELECT 
                id,
                title, 
                url_string,
                date_published,
                picture
            FROM 
                blog_posts 
            ORDER BY 
                date_published DESC 
            LIMIT 10
        ';
        $rows = $this->model->query($sql, 'object');

        if (empty($rows)) {
            return '';
        }

        $this->module('blog_posts');
        $pic_dir = $this->blog_posts->get_picture_locations(NULL, 'thumbnail_dir')['url'];
        $pic_fallback = $this->picture_fallback; // '';

        $data = [];
        foreach ($rows as $post) {
            $row_data['title']          = $post->title;
            $row_data['url_string']     = $post->url_string;
            $row_data['date_published'] = date('Y-m-d h:i', strtotime($post->date_published));
            $row_data['picture']        = $post->picture 
                ? BASE_URL . $pic_dir . '/' . $post->id . '/' . $post->picture 
                : $pic_fallback;
            
            $data[] = (object) $row_data;
        }

        return $this->view('_widget_recent_posts', ['rows' => $data], true) ?: '';
    }

    /**
     * Ensures that the current user is allowed to access the protected resource.
     * Feel free to change to suit your own individual use case.
     *
     * @return string|false The security token if the user is authorized, or false otherwise.
     */
    private function _make_sure_allowed(): string|false {
        // By default, 'admin' users (i.e., users with user_level_id === 1) are allowed
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        return $token;
    }

    /**
     * Get admin_id sending a trongate_token
     * 
     * @return int|false trongate_user_id or false
     */
    private function _get_current_admin_id($tg_token): int|false {
        $this->module('trongate_tokens');
        $tguser_id = $this->trongate_tokens->_get_user_id($tg_token);
        return $tguser_id;
    }

// -------

    /** Gone to Menus
     * Count the Number of total & new Enquiries
     */
    private function gone_get_enquiries_details(): array {
        $sql = "SELECT COUNT(*) as total FROM enquiries WHERE opened = 0";
        $result = $this->model->query($sql, 'object');
        $data['num_new_enquiries'] = $result[0]->total;
        $data['num_enquiries'] = $this->_get_total_count('enquiries');
        return $data;
    }

// ------  Making all widgets in their own controllers, as is possible

    function widget_navbar_user () {
        /**
         * icon with dropdown
         * pic, name, email
         * total num posts -> views, likes, msg, profile, settings
         * weekly, monthly -> posts
         */
    }

}