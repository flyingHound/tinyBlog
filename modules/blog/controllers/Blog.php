<?php
class Blog extends Trongate {
    private $public_template = 'tiny_blog';
    private $default_limit = 4;
    private $date_format_nice   = 'M d, Y';

    private $picture_fallback = 'blog_posts_module/img/fallback_img.jpg'; // '' for no pic

// ---------------------------------------------------------------------------------

    /**
     * Index Page of the Blog redirects to show all blog posts
     * 
     */
    public function index(): void {
        redirect('blog/posts');
    }

    /** 
     * 'Error Page shown when record is missing
     */
    public function show_404() {
        $data['headline'] = 'Nothing found';
        $data['view_module'] = 'blog';
        $this->view('view_404', $data);
    }

    /**
     * Displays a single blog post view.
     * 
     */
    public function post(): void {
        $url_string = segment(3);
        $record_obj = $this->model->get_one_where('url_string', $url_string, 'blog_posts');
        
        if (!$record_obj) {
            $this->show_404();
            return;
        }

        // Handle picture URL with fallback
        $this->module('blog_posts');
        $data['picture_url'] = $record_obj->picture 
            ? $this->blog_posts->get_picture_locations()['url'].'/'.$record_obj->id.'/'.$record_obj->picture 
            : $this->picture_fallback;

        // Handle author, source, and category
        $data['author'] = $this->get_user_name($record_obj->created_by);
        $data['source'] = $this->get_source_obj($record_obj->blog_sources_id);
        $data['category'] = $this->get_category_obj($record_obj->blog_categories_id);

        // Fetch tags, gallery, and YouTube content
        $data['tags'] = $this->get_tags($record_obj->id);
        $data['html_gallery'] = $this->get_post_gallery_html($record_obj);
        $data['html_youtube'] = $this->get_post_youtube_html($record_obj);

        // Store record for view
        $data['record'] = $record_obj;

        // Get previous and next posts
        $prev_next_posts = $this->get_prev_next($record_obj->id);
        $data['prev_link'] = $prev_next_posts['prev'];
        $data['next_link'] = $prev_next_posts['next'];
        
        // Load the template with settings
        $data['date_format'] = 'M j\, Y';
        $data['layout'] = '0';
        $data['body_class'] = 'class="single-post"';
        $data['post_class'] = 'class="article-rounded"';
        $data['headline'] = htmlspecialchars_decode($record_obj->title);
        $data['view_module'] = 'blog';
        $data['view_file'] = 'view_post';
        $this->template($this->public_template, $data);
    }

    /** 
     * Display all blog posts
     * 
     * 
     */
    public function posts(): void {
        // count possible rows to publish
        $total_rows = $this->_get_posts(false);
        if ( $total_rows > 0 ) { 
            // get posts data
            $rows = $this->_get_posts(true);
            $data['rows']       = $rows;
            $data['total_rows'] = $total_rows; // count

            // get picture locations
            $this->module('blog_posts');
            $data['pic_dir'] = $this->blog_posts->get_picture_locations()['url'];
            $data['pic_fallback'] = $this->picture_fallback;

            // add admins as authors
            $data['rows'] = $this->blog_posts->_add_admin_names_multiple($data['rows']);

            // get all categories for category nav
            $data['categories'] = $this->model->get('id', 'blog_categories');
            $data['cat_filter'] = $this->get_category_filter_html();

            // pagination
            #$pagination_data['target_base_url'] = $this->get_target_pagination_base_url();
            $pagination_data['pagination_root'] = 'blog/posts';
            $pagination_data['total_rows'] = $total_rows ;
            $pagination_data['offset'] = $this->get_offset();
            $pagination_data['limit'] = $this->get_limit();
            $pagination_data['include_showing_statement'] = false;
            $pagination_data['record_name_plural'] = 'posts';
            $pagination_data['page_num_segment'] = 3;
            $pagination_data['num_links_per_page'] = 4;

            $data['pagination_data'] = $pagination_data;
        } else {
            # No Posts to publish
            $data['no_posts'] = 'No Blog Posts yet'; 
        }

        # Load the template
        $data['date_format']    = $this->date_format_nice;
        $data['layout']         = '1'; // main + sidebar
        $data['headline']       = 'Posts & Publications';
        $data['view_module']    = 'blog';
        $data['view_file']      = 'view_posts';
        $this->template($this->public_template, $data);
    }

    /** 
     * Display all blog posts of a specific category
     * 
     * 
     */
    public function category(): void {
        $category_url = segment(3);
        $category_obj = $this->model->get_one_where('url_string', $category_url, 'blog_categories');

        if ($category_obj == false) {
            // no such category
            redirect('blog');
        } else {
            $category_id = $category_obj->id;

            # count possible rows to publish
            $posts_rows = $this->_get_category_posts(false, $category_id); // Array of IDs
            $total_rows = count($posts_rows); // Number of posts
            if ($total_rows > 0) {

                // get category's posts data
                $rows = $this->_get_category_posts(true, $category_id);
                $data['rows']       = $rows;
                $data['total_rows'] = $total_rows;

                // get picture locations    - - - > thumb??
                $this->module('blog_posts');
                $data['pic_dir'] = $this->blog_posts->get_picture_locations()['url'];
                $data['pic_fallback'] = $this->picture_fallback;

                // add admins as authors
                $data['rows'] = $this->blog_posts->_add_admin_names_multiple($data['rows']);

                // source obj ??

                // pagination
                #$pagination_data['target_base_url'] = $this->get_target_pagination_base_url();
                $pagination_data['pagination_root'] = 'blog/category';
                $pagination_data['total_rows'] = $total_rows;
                $pagination_data['offset'] = $this->get_offset();
                $pagination_data['limit'] = $this->get_limit();
                $pagination_data['include_showing_statement'] = false;
                $pagination_data['record_name_plural'] = 'categories';
                $pagination_data['page_num_segment'] = 3;
                $pagination_data['num_links_per_page'] = 4;

                $data['pagination_data'] = $pagination_data;

            } else {
                # No Posts to publish
                $data['no_posts'] = 'No Blog Posts yet';
            }

            // get all categories for category nav
            $data['categories'] = $this->model->get('id', 'blog_categories');
            $data['category_id'] = $category_id;

            # Load the template
            $data['date_format']    = 'M j\, Y';
            $data['layout']         = '1'; // main + sidebar
            $data['headline']       = $category_obj->title;
            $data['headline']      .= ' Publications';
            $data['view_module']    = 'blog';
            $data['view_file']      = 'view_posts';
            $this->template($this->public_template, $data);
        }
    }

    public function tag(): void {
        $tag_url = segment(3);
        $tag_obj = $this->model->get_one_where('url_string', $tag_url, 'blog_tags');

        if (!$tag_obj) {
            redirect('blog');
        }

        $tag_id = $tag_obj->id;

        // get Post-IDs for counting
        $post_ids = $this->_get_posts_from_tag($tag_id, false);
        $total_rows = count($post_ids);

        // Pagination-Setup
        $limit = $this->get_limit();
        $offset = $this->get_offset();

        // get paginated Posts
        $data['rows'] = $this->_get_posts_from_tag($tag_id, true, $limit, $offset);
        $data['total_rows'] = $total_rows;

        if ($total_rows === 0) {
            $data['no_posts'] = 'No Blog Posts yet for this tag';
        } else {
            // get picture locations    - - - > thumb??
            $this->module('blog_posts');
            $data['pic_dir'] = $this->blog_posts->get_picture_locations()['url'];
            $data['pic_fallback'] = $this->picture_fallback;

            // add admins as authors
            $data['rows'] = $this->blog_posts->_add_admin_names_multiple($data['rows']);

            // get all categories for category nav ---> change to html-mod
            $data['categories'] = $this->model->get('id', 'blog_categories');

            // Pagination
            $pagination_data = [
                'pagination_root' => 'blog/tag/' . $tag_url,
                'total_rows' => $total_rows,
                'offset' => $offset,
                'limit' => $limit,
                'include_showing_statement' => false,
                'record_name_plural' => 'blog posts',
                'page_num_segment' => 4, // /blog/tag/[tag_url]/[page]
                'num_links_per_page' => 4
            ];
            //$data['pagination'] = $this->pagination->create_links($pagination_data);
            $data['pagination_data'] = $pagination_data;
        }

        // Template-Daten
        $data['date_format'] = 'M j\, Y';
        $data['layout'] = '1'; // with Sidebar
        $data['headline'] = htmlspecialchars_decode($tag_obj->name) . ' Publications';
        $data['view_module'] = 'blog';
        $data['view_file'] = 'view_posts';
        $this->template($this->public_template, $data);
    }

// ---------------------------------------------------------------------------------

    /**
     * Get all published blog posts from db
     *
     * @param bool|null $limit_results Whether to apply pagination (true), return all IDs (false), or default to pagination (null)
     * @return array Array of post objects or IDs, or empty array if no results
     */
    private function _get_posts($limit_results = NULL) {
        
        // without limit
        if($limit_results == false) {

            $sql = '
                SELECT p.id 
                FROM blog_posts p
                WHERE p.published = 1
                ORDER BY p.date_published DESC, id DESC
            ';

            $rows = $this->model->query($sql, 'object');

            if (empty($rows)) {$results = 0;} else {$results = count($rows);}          

        } else {

            $pagination_data['offset'] = $this->get_offset();
            $pagination_data['limit'] = $this->get_limit();

            $sql = '
                SELECT
                    p.id, 
                    p.title,
                    p.subtitle,
                    p.text,
                    p.date_published,
                    p.date_updated,
                    p.created_by,
                    p.updated_by,
                    p.url_string,
                    p.picture,
                    p.blog_sources_id,
                    p.blog_categories_id
                FROM
                    blog_posts p
                WHERE 
                    p.published = 1
                ORDER BY 
                    p.date_published desc, id desc
                LIMIT :offset, :limit
            ';

            $params = [
                'offset' => $pagination_data['offset'],
                'limit' => $pagination_data['limit']
            ];

            $rows = $this->model->query_bind($sql, $params, 'object');

            if ($rows === false || $rows === null || empty($rows)) {
                return [];
            }

            $this->module('blog_posts');
            $rows = $this->blog_posts->_add_short_texts($rows, 'text', 'text_short', 20);

            $data = [];
            foreach ($rows as $post) {
                $row_data = [
                    'id'             => $post->id,
                    'title'          => $post->title,
                    'subtitle'       => $post->subtitle,
                    'text'           => $post->text,
                    'text_short'     => $post->text_short,
                    'date_published' => $post->date_published,
                    'date_updated'   => $post->date_updated,
                    'created_by'     => $post->created_by,
                    'updated_by'     => $post->updated_by,
                    'url_string'     => $post->url_string,
                    'picture'        => $post->picture,

                    'source_id'      => $post->blog_sources_id,
                    'source'         => $this->get_source_obj($post->blog_sources_id),
                    'category'       => $this->get_category_obj($post->blog_categories_id),
                    'tags'           => $this->get_tags($post->id)
                ];
                $data[] = (object) $row_data;
            }
            $results =  $data; 
        }
        return $results;
    }

    /**
     * Get category blog posts from db
     * 
     * @param bool $limit_results Whether to apply pagination (true) or not (false)
     * @param int $category_id The category ID to filter posts by
     * @return array Array of post objects, IDs, or empty array if no results
     */
    private function _get_category_posts($limit_results = true, $category_id) {
        if (!is_numeric($category_id) || $category_id <= 0) {
            return [];
        }

        if ($limit_results === false) {
            $sql = '
                SELECT p.id 
                FROM blog_posts p
                WHERE p.published = 1 AND p.blog_categories_id = :category_id
                ORDER BY p.date_published DESC, p.id DESC
            ';
            $params = ['category_id' => $category_id];
            $rows = $this->model->query_bind($sql, $params, 'object');

            if ($rows === false || $rows === null || empty($rows)) {
                return [];
            }

            // Array von IDs zurückgeben
            $results = array_column((array) $rows, 'id');
        } else {
            $pagination_data['offset'] = $this->get_offset();
            $pagination_data['limit'] = $this->get_limit();

            $sql = '
                SELECT
                    p.id, 
                    p.title,
                    p.subtitle,
                    p.text,
                    p.date_published,
                    p.date_updated,
                    p.created_by,
                    p.updated_by,
                    p.url_string,
                    p.picture,
                    p.blog_sources_id,
                    p.blog_categories_id
                FROM
                    blog_posts p
                WHERE 
                    p.published = 1 AND p.blog_categories_id = :category_id
                ORDER BY 
                    p.date_published DESC, id DESC
                LIMIT :offset, :limit
            ';
            $params = [
                'category_id' => $category_id,
                'offset' => $pagination_data['offset'],
                'limit' => $pagination_data['limit']
            ];
            $rows = $this->model->query_bind($sql, $params, 'object');

            if ($rows === false || $rows === null || empty($rows)) {
                return [];
            }

            $this->module('blog_posts');
            $rows = $this->blog_posts->_add_short_texts($rows, 'text', 'text_short', 20);

            $data = [];
            foreach ($rows as $post) {
                $row_data = [
                    'id'             => $post->id,
                    'title'          => $post->title,
                    'subtitle'       => $post->subtitle,
                    'text'           => $post->text,
                    'text_short'     => $post->text_short,
                    'date_published' => $post->date_published,
                    'date_updated'   => $post->date_updated,
                    'created_by'     => $post->created_by,
                    'updated_by'     => $post->updated_by,
                    'url_string'     => $post->url_string,
                    'picture'        => $post->picture,
                    'source_id'      => $post->blog_sources_id,
                    'source'         => $this->get_source_obj($post->blog_sources_id),
                    'category'       => $this->get_category_obj($post->blog_categories_id),
                    'tags'           => $this->get_tags($post->id)
                ];
                $data[] = (object) $row_data;
            }
            $results = $data;
        }
        return $results;
    }

    /**
     * Get posts associated with a tag.
     * @param int $tag_id The ID of the tag
     * @param bool $fetch_full Fetch full post data or just IDs
     * @param int|null $limit Number of posts to fetch
     * @param int|null $offset Offset for pagination
     * @return array Array of post objects or IDs
     */
    private function _get_posts_from_tag(int $tag_id, bool $fetch_full = false, ?int $limit = null, ?int $offset = null): array {
        if (!is_numeric($tag_id) || $tag_id <= 0) {
            return [];
        }

        if (!$fetch_full) {
            $sql = '
                SELECT p.id
                FROM blog_posts p
                INNER JOIN associated_blog_tags_and_blog_posts apt ON p.id = apt.blog_posts_id
                WHERE apt.blog_tags_id = :tag_id
                AND p.published = 1
                ORDER BY p.date_published DESC, p.id DESC
            ';
            $params = ['tag_id' => $tag_id];
            $rows = $this->model->query_bind($sql, $params, 'object');
            return $rows ? array_column((array) $rows, 'id') : [];
        }

        $sql = '
            SELECT
                p.id,
                p.title,
                p.subtitle,
                p.text,
                p.date_published,
                p.date_updated,
                p.created_by,
                p.updated_by,
                p.url_string,
                p.picture,
                p.blog_sources_id,
                p.blog_categories_id
            FROM blog_posts p
            INNER JOIN associated_blog_tags_and_blog_posts apt ON p.id = apt.blog_posts_id
            WHERE apt.blog_tags_id = :tag_id
            AND p.published = 1
            ORDER BY p.date_published DESC, p.id DESC
        ';
        
        $params = ['tag_id' => $tag_id];
        if ($limit !== null) {
            $sql .= ' LIMIT :limit OFFSET :offset';
            $params['limit'] = $limit;
            $params['offset'] = $offset;
            $bind_types = [
                'tag_id' => PDO::PARAM_INT,
                'limit' => PDO::PARAM_INT,
                'offset' => PDO::PARAM_INT
            ];
            $rows = $this->model->query_bind($sql, $params, 'object', $bind_types);
        } else {
            $rows = $this->model->query_bind($sql, $params, 'object');
        }

        if (!$rows) {
            return [];
        }

        $this->module('blog_posts');
        $rows = $this->blog_posts->_add_short_texts($rows, 'text', 'text_short', 20);

        $data = [];
        foreach ($rows as $post) {
            $row_data = [
                'id' => $post->id,
                'title' => $post->title,
                'subtitle' => $post->subtitle,
                'text' => $post->text,
                'text_short' => $post->text_short,
                'date_published' => $post->date_published,
                'date_updated' => $post->date_updated,
                'created_by' => $post->created_by,
                'updated_by' => $post->updated_by,
                'url_string' => $post->url_string,
                'picture' => $post->picture,
                'source_id' => $post->blog_sources_id,
                'source' => $this->get_source_obj($post->blog_sources_id),
                'category' => $this->get_category_obj($post->blog_categories_id),
                'tags' => $this->get_tags($post->id)
            ];
            $data[] = (object) $row_data;
        }
        return $data;
    }

// ---------------------------------------------------------------------------------

    /** not really in use
     * 
     */
    function get_target_pagination_base_url(){
        $first_bit = segment(1);  
        $second_bit = segment(2);
        $target_base_url = BASE_URL.$first_bit."/".$second_bit;
        #$target_base_url = BASE_URL.$first_bit;

        return $target_base_url;
    }

    /**
     * Returns pagination limit 
     * 
     */
    private function get_limit() {
        $limit = $this->default_limit;
        return $limit;
    }

    /**
     * Returns pagination offset
     */
    private function get_offset() {
    
        $page_num = segment(3);

        if(!is_numeric($page_num)) {
            $page_num = 0;
        } 
        if($page_num>1) {
          
            $offset = ($page_num-1)*$this->get_limit();
        } else {
            $offset = 0;
        }
        return $offset;
    }

// ---------------------------------------------------------------------------------

    /**
     * Get the full category object by ID.
     *
     * Returns category details including title and url_string.
     *
     * @param int $categories_id The ID of the category to fetch
     * @return object|null Category object or null if not found
     */
    private function get_category_obj(int $categories_id) {
        if (!is_numeric($categories_id) || $categories_id <= 0) {
            return null;
        }

        $category_obj = $this->model->get_one_where('id', $categories_id, 'blog_categories');
        return $category_obj ?: null;
    }

    /**
     * Get the full source object by ID.
     *
     * Returns source details including name, author, and link.
     *
     * @param int $sources_id The ID of the source to fetch
     * @return object|null Source object or null if not found
     */
    private function get_source_obj(int $sources_id) {
        if (!is_numeric($sources_id) || $sources_id <= 0) {
            return null;
        }

        $source_obj = $this->model->get_one_where('id', $sources_id, 'blog_sources');
        return $source_obj ?: null;
    }

    /**
     * Returns an array of tags associated with a specific post.
     *
     * Fetches all tags linked to a post via the associated_posts_and_tags table.
     *
     * @param int $posts_id The ID of the post to fetch tags for
     * @return array Array of tag objects with name and url_string, or empty array if none exist
     */
    private function get_tags(int $posts_id) {
        if (!is_numeric($posts_id) || $posts_id <= 0) {
            return [];
        }

        $sql = '
            SELECT 
                blog_tags.name,
                blog_tags.url_string
            FROM 
                blog_tags
            INNER JOIN 
                associated_blog_tags_and_blog_posts ON blog_tags.id = associated_blog_tags_and_blog_posts.blog_tags_id
            WHERE 
                associated_blog_tags_and_blog_posts.blog_posts_id = :posts_id
            ORDER BY 
                blog_tags.name ASC
        ';
        $rows = $this->model->query_bind($sql, ['posts_id' => $posts_id], 'object');

        return $rows ?: [];
    }

    /**
     * Returns a single tag by its ID.
     *
     * @param int $tags_id The ID of the tag to fetch
     * @return object|null Tag object with name and url_string, or null if not found
     *
    private function get_tag(int $tags_id) {
        if (!is_numeric($tags_id) || $tags_id <= 0) {
            return null;
        }

        $tag = $this->model->get_one_where('id', $tags_id, 'blog_tags');
        return $tag ?: null;
    } */

    /** 
     * return the admin name
     * 
     * @params int $user_id
     * @return user_name
     */
    private function get_user_name(int $user_id): string {
        $result = $this->model->get_one_where("trongate_user_id", $user_id, "trongate_administrators");
        $user_name = ($user_id=='') ? 'unknown' : $result->username;
        return $user_name;
    }

    /** OLD VERSION showing links to posts if no prev or next existing
     * Get previous and next post links based on date_published, with id as tiebreaker.
     * 
     * Composite Index:
     * Will make queries scale better than full table scans, especially if the blog grows large.
     * Run this in phpMyAdmin to add the index:
     * CREATE INDEX idx_date_id ON posts (date_published, id, published);
     * Run to remove the index:
     * DROP INDEX idx_date_id ON posts;
     *
     * @param int $posts_id The ID of the current post
     * @return array Array containing 'prev' and 'next' URLs
     */
    private function version1_get_prev_next($posts_id) {
        // Fetch the current post's date_published to compare against
        $params['id'] = $posts_id;
        $current_sql = 'SELECT date_published FROM blog_posts WHERE id = :id AND published = 1 LIMIT 1';
        $current_result = $this->model->query_bind($current_sql, $params, 'object');
        
        if ($current_result === false || empty($current_result)) {
            // If current post not found, fall back to blog home for both links
            return [
                'prev' => BASE_URL . 'blog/posts',
                'next' => BASE_URL . 'blog/posts'
            ];
        }
        
        $current_date = $current_result[0]->date_published;

        // Get the previous post (latest before current date_published, then lower id if tied)
        $prev_sql = 'SELECT url_string 
                     FROM blog_posts 
                     WHERE (date_published < :date OR (date_published = :date AND id < :id)) 
                     AND published = 1 
                     ORDER BY date_published DESC, id DESC 
                     LIMIT 1';
        $prev_params = ['date' => $current_date, 'id' => $posts_id];
        $prev_result = $this->model->query_bind($prev_sql, $prev_params, 'object');
        
         $prev = ($prev_result === false || empty($prev_result)) 
            ? BASE_URL . 'blog/posts'
            : BASE_URL . 'blog/post/' . $prev_result[0]->url_string;

        // Get the next post (earliest after current date_published, then higher id if tied)
        $next_sql = 'SELECT url_string 
                     FROM blog_posts 
                     WHERE (date_published > :date OR (date_published = :date AND id > :id)) 
                     AND published = 1 
                     ORDER BY date_published ASC, id ASC 
                     LIMIT 1';
        $next_params = ['date' => $current_date, 'id' => $posts_id];
        $next_result = $this->model->query_bind($next_sql, $next_params, 'object');
        
        $next = ($next_result === false || empty($next_result)) 
            ? BASE_URL . 'blog/posts'
            : BASE_URL . 'blog/post/' . $next_result[0]->url_string;

        return [
            'prev' => $prev,
            'next' => $next
        ];
    }

    /**
     * Get previous and next post links based on date_published, with id as tiebreaker.
     *
     * Returns URLs for the previous and next published posts, or null if none exist.
     * Uses a composite index (date_published, id, published) for efficient querying.
     *
     * @param int $posts_id The ID of the current post
     * @return array Array containing 'prev' and 'next' URLs, or null where no post exists
     */
    private function get_prev_next($posts_id) {
        // Fetch the current post's date_published to compare against
        $params['id'] = $posts_id;
        $current_sql = 'SELECT date_published FROM blog_posts WHERE id = :id AND published = 1 LIMIT 1';
        $current_result = $this->model->query_bind($current_sql, $params, 'object');

        if ($current_result === false || empty($current_result)) {
            // If current post not found, return null for both
            return [
                'prev' => null,
                'next' => null
            ];
        }

        $current_date = $current_result[0]->date_published;

        // Get the previous post (latest before current date_published, then lower id if tied)
        $prev_sql = 'SELECT url_string 
                     FROM blog_posts 
                     WHERE (date_published < :date OR (date_published = :date AND id < :id)) 
                     AND published = 1 
                     ORDER BY date_published DESC, id DESC 
                     LIMIT 1';
        $prev_params = ['date' => $current_date, 'id' => $posts_id];
        $prev_result = $this->model->query_bind($prev_sql, $prev_params, 'object');

        $prev = ($prev_result === false || empty($prev_result)) 
            ? null 
            : BASE_URL . 'blog/post/' . $prev_result[0]->url_string;

        // Get the next post (earliest after current date_published, then higher id if tied)
        $next_sql = 'SELECT url_string 
                     FROM blog_posts 
                     WHERE (date_published > :date OR (date_published = :date AND id > :id)) 
                     AND published = 1 
                     ORDER BY date_published ASC, id ASC 
                     LIMIT 1';
        $next_params = ['date' => $current_date, 'id' => $posts_id];
        $next_result = $this->model->query_bind($next_sql, $next_params, 'object');

        $next = ($next_result === false || empty($next_result)) 
            ? null 
            : BASE_URL . 'blog/post/' . $next_result[0]->url_string;

        return [
            'prev' => $prev,
            'next' => $next
        ];
    }

// ---------------------------------------------------------------------------------

    /* HTML Partials */

    /**
     * Returns HTML for a category filter.
     * might be neccessary to send in the current category_id
     * not in use, view_posts has it build in
     * 
     */
    function get_category_filter_html() {
        //
        $data['categories'] = $this->model->get('id', 'blog_categories');

        if ($data['categories'] > 0) {
            $html = $this->view('_html_category_filter', $data, true);
        } else {
            $html = '';
        }

        return $html;
    }

    /**
     * Returns HTML for a post's gallery view.
     *
     * @param object $record_obj The post object to fetch gallery pictures for
     * @return string HTML string for the gallery or empty string if no pictures exist
     */
    private function get_post_gallery_html($record_obj) {

        if (!isset($record_obj->id) || !is_numeric($record_obj->id)) {
            return '';
        }

        // load gallery pictures of post from db
        $this->module('blog_pictures');
        $data['gallery_pics'] = $this->blog_pictures->_fetch_pictures('blog_posts', $record_obj->id);

        if (empty($data['gallery_pics'])) {
            return '';
        }

        $this->module('blog_posts');
        $settings = $this->blog_posts->_init_filezone_settings();
        
        // load directory path
        $this->module('blog_filezone');
        $thumb_dir = $this->blog_filezone->_get_filezone_locations($settings)['thumbs']['url'];
        $picture_dir = $this->blog_filezone->_get_filezone_locations($settings)['pictures']['url'];

        // build path to gallery pictures
        $data['thumb_dir'] = $thumb_dir.'/'.$record_obj->id;
        $data['picture_dir'] = $picture_dir.'/'.$record_obj->id;
        $data['record_obj'] = $record_obj;

        return $this->view('_html_post_gallery', $data, true) ?: '';
    }

    private function get_post_youtube_html($record_obj) {
        $data['record_obj'] = $record_obj;
        return $this->view('_html_post_youtube', $data, true) ?: '';
    }

    /* Sidebar Widgets */

    /**
     * Returns HTML for a categories widget with the number of associated posts.
     * 
     * @return string HTML string for the widget or empty string if no categories exist.
     */
    public function get_categories_counter_widget() {
        $sql ='
            SELECT 
                blog_categories.title,
                blog_categories.url_string,
                COUNT(blog_posts.id) AS post_count
            FROM 
                blog_categories
            LEFT JOIN 
                blog_posts ON blog_categories.id = blog_posts.blog_categories_id
            GROUP BY 
                blog_categories.id
            ORDER BY 
                blog_categories.title ASC
        ';
        $rows = $this->model->query($sql, 'object');
        
        // Error Handling
        if ($rows === false || $rows === null || empty($rows)) {
            return '';
        }

        $data['rows'] = $rows;
        $html = $this->view('_widget_categories_counter', $data, true);

        return $html ?: ''; // Fallback, if no view()
    }

    /**
     * Returns HTML for a tag cloud widget with post counts.
     * @return string Rendered HTML string of the tag cloud
     */
    public function get_tag_cloud_widget() {
        $sql = '
            SELECT 
                blog_tags.name,
                blog_tags.url_string,
                COUNT(associated_blog_tags_and_blog_posts.blog_posts_id) as post_count
            FROM 
                blog_tags
            LEFT JOIN 
                associated_blog_tags_and_blog_posts ON blog_tags.id = associated_blog_tags_and_blog_posts.blog_tags_id
            GROUP BY 
                blog_tags.id, blog_tags.name, blog_tags.url_string
            ORDER BY 
                blog_tags.name ASC
        ';
        $tags = $this->model->query_bind($sql, [], 'object');
        
        // Übergib die Tags an den View und rendere das HTML
        $data['tags'] = $tags ?: [];
        $html = $this->view('_widget_tag_cloud', $data, true);
        
        return $html ?: '';
    }

    /**
     * Returns HTML for a widget displaying 3 random gallery images with linked post titles.
     *
     * This method queries the pictures table for random entries, joins them with the posts table
     * to ensure only images with existing posts are selected, and prepares image URLs using the
     * gallery directory. If no image exists, a fallback image is used.
     *
     * @return string HTML string for the random images widget or an empty string if no valid images exist.
     */
    public function get_random_images_widget() {
        $sql = '
            SELECT 
                blog_pictures.picture,
                blog_pictures.target_module,
                blog_pictures.target_module_id,
                blog_posts.title,
                blog_posts.url_string
            FROM 
                blog_pictures
            INNER JOIN 
                blog_posts ON blog_pictures.target_module_id = blog_posts.id 
                     AND blog_pictures.target_module = "blog_posts"
            ORDER BY RAND() 
            LIMIT 6
        ';
        $rows = $this->model->query($sql, 'object');

        if (empty($rows)) {
            return '';
        }

        $this->module('blog_posts');
        $settings = $this->blog_posts->_init_filezone_settings();
        $this->module('blog_filezone');
        $locations = $this->blog_filezone->_get_filezone_locations($settings, 'articles');

        $thumb_dir = $locations['thumbs']['url'];
        $pic_dir = $locations['pictures']['url'];
        $pic_fallback = $this->picture_fallback;

        $data = [];
        foreach ($rows as $row) {
            $row_data['title'] = $row->title; // Vom Post
            $row_data['url_string'] = $row->url_string; // Vom Post
            $row_data['thumb'] = $row->picture 
                ? BASE_URL . $thumb_dir . '/' . $row->target_module_id . '/' . $row->picture 
                : $pic_fallback;
            $row_data['picture'] = $row->picture 
                ? BASE_URL . $pic_dir . '/' . $row->target_module_id . '/' . $row->picture 
                : $pic_fallback;

            $data[] = (object) $row_data;
        }

        return $this->view('_widget_random_images', ['rows' => $data], true) ?: '';
    }

    /**
     * Returns HTML for a widget showing the 4 latest posts with titles, dates, and images.
     *
     * This method queries the posts table for the latest entries based on their publication date,
     * prepares the data including titles, URLs, publication dates, and images, and renders it
     * into a view template. If a post has an associated picture, it constructs the full URL using
     * the picture directory and post ID; otherwise, it falls back to a default image.
     *
     * @return string HTML string for the widget or empty string if no posts are found.
     */
    public function get_recent_posts_widget() {
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
            LIMIT 4
        ';
        $rows = $this->model->query($sql, 'object');

        if (empty($rows)) {
            return '';
        }

        $this->module('blog_posts');
        $pic_dir = $this->blog_posts->get_picture_locations(NULL, 'thumbnail_dir')['url'];
        $pic_fallback = $this->picture_fallback;

        $data = [];
        foreach ($rows as $post) {
            $row_data['title']          = $post->title;
            $row_data['url_string']     = $post->url_string;
            $row_data['date_published'] = date($this->date_format_nice, strtotime($post->date_published));
            $row_data['picture']        = $post->picture 
                ? BASE_URL . $pic_dir . '/' . $post->id . '/' . $post->picture 
                : $pic_fallback;
            
            $data[] = (object) $row_data;
        }

        return $this->view('_widget_recent_posts', ['rows' => $data], true) ?: '';
    }

// ---

    public function get_sidebar_text_widget() {
        $data['first_post'] = $this->get_first_post();
        return $this->view('_widget_sidebar_text', $data, true) ?: '';
    }

    /** Noch Version get_first_post_link machen !!!
     * Fetch the first published post as an object
     */
    function get_first_post() {
        $sql = "
            SELECT 
                p.id, 
                p.title,
                p.subtitle,
                p.text,
                p.date_published,
                p.date_updated,
                p.created_by,
                p.updated_by,
                p.url_string,
                p.picture,
                p.blog_sources_id,
                p.blog_categories_id
            FROM blog_posts p
            WHERE p.published = 1
            ORDER BY p.date_published ASC, p.id ASC
            LIMIT 1
        ";

        $rows = $this->model->query($sql, 'object');

        if (empty($rows)) {
            return null; // Kein Post gefunden
        }

        // Erweitere das Post-Objekt mit weiteren Informationen
        $post = $rows[0]; // Es wird nur ein einzelner Post erwartet
        $post->source = $this->get_source_obj($post->blog_sources_id);
        $post->category = $this->get_category_obj($post->blog_categories_id);
        $post->tags = $this->get_tags($post->id);

        return $post;
    }
}