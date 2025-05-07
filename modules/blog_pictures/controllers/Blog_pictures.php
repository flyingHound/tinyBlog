<?php
class Blog_pictures extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);
    private $admin_template = 'tiny_bootstrap';

    /**
     *  Displays Order View for Gallery Pictures 
     * 
     *  
     */
    function order_pictures(): void {
        $target_module = segment(3);
        $target_module_id = (int) segment(4);
        $params['target_module'] = $target_module;
        $params['target_module_id'] = $target_module_id;

        $this->module('trongate_security');
        $data['token'] = $this->trongate_security->_make_sure_allowed();

        $sql = 'SELECT * FROM blog_pictures 
                WHERE target_module = :target_module 
                AND target_module_id = :target_module_id 
                ORDER BY priority';
        $rows = $this->model->query_bind($sql, $params, 'object');

        $data['rows'] = $rows;

        $data['num_rows'] = $this->model->count_where('target_module_id', $target_module_id, "=", 'blog_pictures');
        //$data['num_rows'] =  $this->model->count_where('target_module_id', $target_module_id, "=",  'priority','pictures');

        $data['rows_count'] = count($rows);

        $data['target_module'] = $target_module;
        $data['target_module_id'] = $target_module_id;

        $this->module($target_module);
        $filezone_settings = $this->$target_module->_init_filezone_settings();
        // extract($filezone_settings);

        // wie hier drüber aber logischer:
        # $pictures_path = BASE_URL . $this->_get_thumbs_dir($filezone_settings);
        $this->module('blog_filezone');
        $pictures_path = BASE_URL . $this->blog_filezone->_get_filezone_locations($filezone_settings)['pictures']['url'];

        # picture_folder 
        $data['target_directory'] = $pictures_path.'/'.$target_module_id.'/';

        $data['upload_url'] = BASE_URL.'blog_filezone/uploader/'.$target_module.'/'.$target_module_id;
        $data['delete_url'] = BASE_URL.'blog_filezone/uploader/'.$target_module.'/'.$target_module_id;
        $data['cancel_url'] = BASE_URL.$target_module.'/show/'.$target_module_id;

        $data['btn_text']   = 'GO BACK';

        if ($this->admin_template == 'admin') {
            $additional_includes_top[] = BASE_URL.'blog_pictures_module/js/sort/jquery.min.js';
            $additional_includes_top[] = BASE_URL.'blog_pictures_module/js/sort/jquery-ui.min.js';
            $additional_includes_top[] = BASE_URL.'blog_pictures_module/js/sort/jquery.ui.touch-punch.min.js';
            $data['additional_includes_top'] = $additional_includes_top; 
        }

        $data['headline']       = 'Order Blog Gallery Pictures';
        $data['view_module']    = 'blog_pictures';
        $data['view_file']      = 'order_pictures_panel_' . $this->admin_template;
        $this->template($this->admin_template, $data);
    }

    /** 
     *  Fetch module related pictures from DB
     * 
     *  Gallery Pictures in single-Post-View
     *  in use: [blog] posts > _get_blog_notices_pics_html($data)
     * 
     */
    function _fetch_pictures($target_module, $target_module_id) {
        $params['target_module'] = $target_module;
        $params['target_module_id'] = $target_module_id;
        
        $sql = 'SELECT picture 
                FROM blog_pictures 
                WHERE target_module = :target_module AND target_module_id = :target_module_id 
                ORDER BY priority';
        $data = $this->model->query_bind($sql, $params, 'object');
        
        return $data;
    }

    /** Momentan nur in Blog_Posts in use - Noch auf diese hier umstellen ...
     * 
     */
    function _fetch_highest_priority($target_module, $target_module_id) {
        $params['target_module'] = $target_module;
        $params['target_module_id'] = $target_module_id;

        $sql = 'SELECT MAX(priority) AS highest_priority
                FROM blog_pictures 
                WHERE target_module = :target_module AND target_module_id = :target_module_id';
        $data = $this->model->query_bind($sql, $params, 'object');

        return $data;
    }

// ---------------------------------------------------------------------------------

    /**
     * Display a webpage with a form for creating or updating a record.
     */
    public function create(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit === '') && ($update_id>0)) {
            $data = $this->get_data_from_db($update_id);
        } else {
            $data = $this->get_data_from_post();
        }

        if ($update_id>0) {
            $data['headline'] = 'Update Picture Record';
            $data['cancel_url'] = BASE_URL.'blog_pictures/show/'.$update_id;
        } else {
            $data['headline'] = 'Create New Picture Record';
            $data['cancel_url'] = BASE_URL.'blog_pictures/manage';
        }

        $data['form_location'] = BASE_URL.'blog_pictures/submit/'.$update_id;
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
        $this->trongate_security->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['picture'] = '%'.$searchphrase.'%';
            $params['target_module'] = '%'.$searchphrase.'%';
            $sql = 'select * from blog_pictures
            WHERE picture LIKE :picture
            OR target_module LIKE :target_module
            ORDER BY id';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Blog Pictures';
            $all_rows = $this->model->get('id');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'blog_pictures/manage';
        $pagination_data['record_name_plural'] = 'blog_pictures';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'blog_pictures';
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
            redirect('blog_pictures/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('blog_pictures/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Picture Information';
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
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        $submit = post('submit', true);

        if ($submit === 'Submit') {

            $this->validation->set_rules('picture', 'Picture', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('priority', 'Priority', 'max_length[11]|numeric|integer');
            $this->validation->set_rules('target_module', 'Target Module', 'min_length[2]|max_length[255]');
            $this->validation->set_rules('target_module_id', 'Target Module ID', 'max_length[11]|numeric|integer');

            $result = $this->validation->run();

            if ($result === true) {

                $update_id = (int) segment(3);
                $data = $this->get_data_from_post();
                
                if ($update_id>0) {
                    //update an existing record
                    $this->model->update($update_id, $data, 'blog_pictures');
                    $flash_msg = 'The record was successfully updated';
                } else {
                    //insert the new record
                    $update_id = $this->model->insert($data, 'blog_pictures');
                    $flash_msg = 'The record was successfully created';
                }

                set_flashdata($flash_msg);
                redirect('blog_pictures/show/'.$update_id);

            } else {
                //form submission error
                $this->create();
            }

        }

    }

    /**
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
            $params['module'] = 'blog_pictures';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'blog_pictures');

            //set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            //redirect to the manage page
            redirect('blog_pictures/manage');
        }
    }

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
        redirect('blog_pictures/manage');
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

    /**
     * Get data from the database for a specific update_id.
     *
     * @param int $update_id The ID of the record to retrieve.
     *
     * @return array|null An array of data or null if the record doesn't exist.
     */
    private function get_data_from_db(int $update_id): ?array {
        $record_obj = $this->model->get_where($update_id, 'blog_pictures');

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
        $data['picture'] = post('picture', true);
        $data['priority'] = post('priority', true);
        $data['target_module'] = post('target_module', true);
        $data['target_module_id'] = post('target_module_id', true);        
        return $data;
    }

}