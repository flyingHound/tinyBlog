<?php
class Menus extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);
    private $admin_template = 'tiny_bootstrap';
    private $date_format_en     = 'm/d/Y, H:i';
    private $date_format_show   = 'F d Y \a\t H:i';
    private $date_format_manage = 'M/d/Y';
    private $date_format_long   = 'l jS F Y \a\t H:i';

    /**
     * Display a webpage with a form for creating or updating a record.
     */
    public function create(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit === '') && ($update_id > 0)) {
            $data = $this->get_data_from_db($update_id);
        } else {
            $data = $this->get_data_from_post();
        }

        if ($update_id>0) {
            $data['headline'] = 'Update Menu Record';
            $data['cancel_url'] = BASE_URL.'menus/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Menu Record';
            $data['cancel_url'] = BASE_URL.'menus/manage';
        }

        // published default 1
        $data['published'] = ($data['published'] == 0) ? 0 : 1;
        $data['template_options'] = $this->get_template_options();
        $data['form_location'] = BASE_URL.'menus/submit/'.$update_id;
        $data['view_file'] = 'create_' . $this->admin_template;
        $this->template($this->admin_template, $data);
    }

    /**
     * Display a webpage to manage records.
     *
     * @return void
     */
    public function manage(): void {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['name'] = '%'.$searchphrase.'%';
            $params['description'] = '%'.$searchphrase.'%';
            $sql = 'select * from menus
            WHERE name LIKE :name
            OR description LIKE :description
            ORDER BY id asc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Menus';
            $all_rows = $this->model->get('id asc');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'menus/manage';
        $pagination_data['record_name_plural'] = 'menus';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['date_format'] = $this->date_format_manage;
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['token'] = $token;
        $data['view_module'] = 'menus';
        $data['view_file'] = 'manage_' . $this->admin_template;
        $this->template($this->admin_template, $data);
    }

    /**
     * Display a webpage showing information for an individual record.
     *
     * @return void
     */
    public function show(): void {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = (int) segment(3);

        if ($update_id === 0) {
            redirect('menus/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('menus/manage');
        } else {
            $data['date_format'] = $this->date_format_show;
            $data['update_id'] = $update_id;
            $data['headline'] = 'Menu Information';
            $data['view_file'] = 'show_' . $this->admin_template;
            $this->template($this->admin_template, $data);
        }
    }

    /**
     * Handle submitted record data.
     *
     * @return void
     */
    public function submit(): void {
        $token = $this->_make_sure_allowed();

        $submit = post('submit', true);

        if ($submit === 'Submit') {

            $this->validation->set_rules('name', 'Name', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('description', 'Description', 'min_length[2]|max_length[255]');
            $this->validation->set_rules('published', 'Published', 'max_length[1]|numeric|integer');
            $this->validation->set_rules('template', 'Template', 'required|in_list[default,horizontal,footer,backend]');

            $result = $this->validation->run();

            if ($result === true) {

                $update_id = (int) segment(3);
                $data = $this->get_data_from_post();
                # $data['date_updated'] = str_replace(' at ', '', $data['date_updated']);
                # $data['date_updated'] = date('Y-m-d H:i', strtotime($data['date_updated']));
                # $data['date_created'] = str_replace(' at ', '', $data['date_created']);
                # $data['date_created'] = date('Y-m-d H:i', strtotime($data['date_created']));
                
                if ($update_id > 0) {
                    //update an existing record
                    $data['date_updated'] = date('Y-m-d H:i:s', time());
                    $this->model->update($update_id, $data, 'menus');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $data['date_created'] = date('Y-m-d H:i:s', time());
                    $update_id = $this->model->insert($data, 'menus');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('menus/show/'.$update_id);

            } else {
                //form submission error
                $this->create();
            }

        }

    }

    /** Delete Items or set to 'unrelated', if we delete a Menu ???
     * Handle submitted request for deletion.
     *
     * @return void
     */
    public function submit_delete(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit');
        $params['update_id'] = (int) segment(3);

        if (($submit === 'Yes - Delete Now') && ($params['update_id']>0)) {
            //delete all of the comments associated with this record
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'menus';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'menus');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('menus/manage');
        }
    }

// ---------------------------------------------------------------------------------

    /**
     * Set the number of items per page.
     *
     * @param int $selected_index Selected index for items per page.
     *
     * @return void
     */
    public function set_per_page(int $selected_index): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('menus/manage');
    }

    /**
     * Get the selected number of items per page.
     *
     * @return int Selected items per page.
     */
    private function get_selected_per_page(): int {
        $selected_per_page = (isset($_SESSION['selected_per_page'])) ? $_SESSION['selected_per_page'] : 1;
        return $selected_per_page;
    }

    /**
     * Reduce fetched table rows based on offset and limit.
     *
     * @param array $all_rows All rows to be reduced.
     *
     * @return array Reduced rows.
     */
    private function reduce_rows(array $all_rows): array {
        $rows = [];
        $start_index = $this->get_offset();
        $limit = $this->get_limit();
        $end_index = $start_index + $limit;

        $count = -1;
        foreach ($all_rows as $row) {
            $count++;
            if (($count>=$start_index) && ($count<$end_index)) {
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Get the limit for pagination.
     *
     * @return int Limit for pagination.
     */
    private function get_limit(): int {
        if (isset($_SESSION['selected_per_page'])) {
            $limit = $this->per_page_options[$_SESSION['selected_per_page']];
        } else {
            $limit = $this->default_limit;
        }

        return $limit;
    }

    /**
     * Get the offset for pagination.
     *
     * @return int Offset for pagination.
     */
    private function get_offset(): int {
        $page_num = (int) segment(3);

        if ($page_num>1) {
            $offset = ($page_num-1)*$this->get_limit();
        } else {
            $offset = 0;
        }

        return $offset;
    }

// ---------------------------------------------------------------------------------

    /**
     * Get data from the database for a specific update_id.
     *
     * @param int $update_id The ID of the record to retrieve.
     *
     * @return array|null An array of data or null if the record doesn't exist.
     */
    private function get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'menus');

        if ($record_obj === false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    /**
     * Get data from the POST request.
     *
     * @return array Data from the POST request.
     */
    private function get_data_from_post(): array {
        $data['name'] = post('name', true);
        $data['description'] = post('description', true);
        $data['published'] = post('published', true);
        $data['template'] = post('template', 'default');  
        return $data;
    }

// ---------------------------------------------------------------------------------

    /**
     * Ensures that the current user is allowed to access the protected resource.
     * Feel free to change to suit your own individual use case.
     *
     * @return string|false The security token if the user is authorized, or false otherwise.
     */
    private function _make_sure_allowed(): string|false {
        //by default, $admin_template users (i.e., users with user_level_id === 1) are allowed
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        return $token;
    }

    /**
     * Get available template options for menus
     *
     * @return array Associative array with template options
     */
    private function get_template_options(): array {
        return [
            'default' => 'Default',
            'default_2' => 'Default 2',
            'horizontal' => 'Horizontal (Header)',
            'footer' => 'Footer',
            'backend' => 'Backend'
        ];
    }

    /**
     * Render navigation for a specific menu
     *
     * Integrate in Controller-FX:
     * $this->module('menus');
     * $data['navigation'] = $this->menus->render_navigation(1);
     *
     * Integrate in Template:
     * <?= Modules::run('menus/render_navigation', 1) ?>
     *
     * @param int $menu_id ID of the menu to render
     * @return string HTML string of the navigation
     */
    public function render_navigation(int $menu_id): string {
        // get menu data to determin the template
        $menu = $this->model->get_where($menu_id, 'menus');
        if (!$menu) {
            return ''; // Fallback, if menu doesn't exist
        }

        // check if the menu is published
        if ($menu->published == 0) {
            return ''; // Return empty string if menu is not published
        }

        $template = $menu->template ?? 'default';

        // get menu items
        $sql = "SELECT * FROM menu_items 
                WHERE menus_id = :menu_id AND published = 1 
                ORDER BY sort_order ASC";
        $params = ['menu_id' => $menu_id];
        $items = $this->model->query_bind($sql, $params, 'array');

        // Build nested structure
        $menu_tree = [];
        $lookup = [];
        foreach ($items as $item) {
            $item['children'] = [];
            $lookup[$item['id']] = $item;
        }
        foreach ($lookup as $id => $item) {
            if ($item['parent_id'] && isset($lookup[$item['parent_id']])) {
                $lookup[$item['parent_id']]['children'][] = &$lookup[$id];
            } else {
                $menu_tree[] = &$lookup[$id];
            }
        }

        $data['menu_items'] = $menu_tree;
        $data['current_url'] = current_url();
        return $this->view('_navigation_' . $template, $data, true);
    }

    /**
     * Renders the sidebar navigation for the tiny_bootstrap template
     * 
     */
    public function render_sidebar_nav(): string {
        $current_url = current_url();
        $num_enquiries = $this->_get_new_enquiries_count();

        // show unopened enquiries
        $html_enquiries = $num_enquiries > 0 
            ? '<span class="badge rounded-pill bg-danger p-1 ms-2">'.$num_enquiries.'</span>'
            : '';

        // define menu items
        $menu_sections = [
            'Blog Management' => [
                ['title' => 'Dashboard', 'url' => BASE_URL . 'blog_dashboard', 'icon' => 'fa fa-tachometer'],
                ['title' => 'Manage Posts', 'url' => BASE_URL . 'blog_posts/manage', 'icon' => 'fa fa-newspaper-o'],
                ['title' => 'Manage Categories', 'url' => BASE_URL . 'blog_categories/manage', 'icon' => 'fa fa-tag'],
                ['title' => 'Manage Tags', 'url' => BASE_URL . 'blog_tags/manage', 'icon' => 'fa fa-tags'],
                ['title' => 'Manage Sources', 'url' => BASE_URL . 'blog_sources/manage', 'icon' => 'fa fa-book'],
                ['title' => 'Manage Pictures', 'url' => BASE_URL . 'blog_pictures/manage', 'icon' => 'fa fa-image'],
                ['title' => 'Manage Comments*', 'url' => BASE_URL . 'blog_comments/manage', 'icon' => 'fa fa-comments'],
            ],
            'Navigation' => [
                ['title' => 'Manage Menus', 'url' => BASE_URL . 'menus/manage', 'icon' => 'fa fa-bars'],
                ['title' => 'Manage Menu Items', 'url' => BASE_URL . 'menu_items/manage', 'icon' => 'fa fa-list'],
            ],
            'App' => [
                ['title' => 'Manage Pages', 'url' => BASE_URL . 'trongate_pages/manage', 'icon' => 'fa fa-file-o'],
                ['title' => 'Manage Admins', 'url' => BASE_URL . 'trongate_administrators/manage', 'icon' => 'fa fa-users'],
                ['title' => 'Admin Comments', 'url' => BASE_URL . 'trongate_comments/manage', 'icon' => 'fa fa-comment'],
                ['title' => 'Manage Enquiries'.$html_enquiries, 'url' => BASE_URL . 'enquiries/manage', 'icon' => 'fa fa-envelope-o'],
            ],
            'App Settings' => [
                ['title' => 'Manage App*', 'url' => BASE_URL . 'app/index', 'icon' => 'fa fa-cogs'],
                ['title' => 'Settings*', 'url' => BASE_URL . 'blog_settings/index', 'icon' => 'fa fa-cogs']
            ],
        ];

        // prepare data for view
        $data = [
            'menu_sections' => $menu_sections,
            'current_url' => $current_url
        ];

        $html = '';

        // Render view
        $html = $this->view('_nav_sidebar_tiny_bootstrap', $data, true);
        return $html;
    }

    /**
     * Count the Number of unopened Enquiries
     */
    private function _get_new_enquiries_count(): int {
        $data = [];

        $sql = "SELECT COUNT(*) as total FROM enquiries WHERE opened = 0";
        $result = $this->model->query($sql, 'object');
        $num_new_enquiries = $result[0]->total;

        return $num_new_enquiries;
    }
}