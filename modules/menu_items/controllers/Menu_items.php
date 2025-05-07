<?php
class Menu_items extends Trongate {

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
        $token = $this->_make_sure_allowed();
        $update_id = (int) segment(3);
        $submit = post('submit');

        if (($submit === '') && ($update_id > 0)) {
            $data = $this->get_data_from_db($update_id);
        } else {
            $data = $this->get_data_from_post();
        }

        if ($update_id > 0) {
            $data['headline'] = 'Update Menu Item Record';
            $data['cancel_url'] = BASE_URL.'menu_items/show/'.$update_id;
        } else {
            $data['headline'] = 'Create Menu Item Record';
            $data['cancel_url'] = BASE_URL.'menu_items/manage';
        }

        // published default 1
        $data['published'] = ($data['published'] == 0) ? 0 : 1;

        // user ID
        $data['user_id'] = $this->_get_current_admin_id($token);

        // add options
        $selected_menu_id = isset($data['menus_id']) ? (int) $data['menus_id'] : 0; // Explizite Konvertierung
        $data['menus_options'] = $this->_get_relation_options($selected_menu_id, 'menu_items', 'menus');
        $data['target_options'] = $this->get_target_options();
        $data['parent_options'] = $this->get_parent_options($update_id);

        $data['form_location'] = BASE_URL.'menu_items/submit/'.$update_id;
        $data['view_file'] = 'create_' . $this->admin_template;
        $this->template($this->admin_template, $data);
    }

    /**
     * Display a webpage to manage records.
     *
     * @return void
     */
    public function manage(): void {
        $token = $this->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['title'] = '%'.$searchphrase.'%';
            $sql = 'select * from menu_items
            WHERE title LIKE :title
            ORDER BY sort_order desc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Menu Items';
            $all_rows = $this->model->get('menus_id, sort_order asc');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->get_limit();
        $pagination_data['pagination_root'] = 'menu_items/manage';
        $pagination_data['record_name_plural'] = 'menu items';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->reduce_rows($all_rows);
        $data['rows'] = $this->_add_names_to_rows($data['rows']);

        $data['date_format'] = $this->date_format_manage;
        $data['selected_per_page'] = $this->get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['token'] = $token;
        $data['view_module'] = 'menu_items';
        $data['view_file'] = 'manage_' . $this->admin_template;
        $this->template($this->admin_template, $data);
    }

    /**
     * Display a webpage showing information for an individual record.
     *
     * @return void
     */
    public function show(): void {
        // verify user permissions
        $token = $this->_make_sure_allowed();
        
        // get the ID for the record to display
        $update_id = (int) segment(3);

        // Redirect if update_id is invalid
        if ($update_id <= 0 || !is_numeric($update_id)) {
            redirect('menu_items/manage');
        }

        $data = $this->get_data_from_db($update_id);
        $data['token'] = $token;

        if ($data === false) {
            redirect('menu_items/manage');
        } else {
            $data['menus_id'] = $this->get_menu_name($data['menus_id']);
            $data['parent_id'] = $this->get_parent_name($data['parent_id']);
            // set date format
            $data['date_format'] = $this->date_format_show;

            $data['update_id'] = $update_id;
            $data['headline'] = 'Menu Item Information';
            $data['view_file'] = 'show_' . $this->admin_template;
            $this->template($this->admin_template, $data);
        }
    }

public function submit(): void {
    $this->_make_sure_allowed();

    $submit = post('submit', true);

    if ($submit === 'Submit') {
        $this->validation->set_rules('title', 'Title', 'required|min_length[2]|max_length[255]');
        $this->validation->set_rules('url_string', 'URL String', 'required|min_length[2]|max_length[255]|callback_validate_url');
        $this->validation->set_rules('parent_id', 'Parent ID', 'max_length[11]|numeric|integer');
        $this->validation->set_rules('sort_order', 'Sort Order', 'max_length[11]|numeric|integer');
        $this->validation->set_rules('published', 'Active', 'max_length[1]|numeric|integer');
        $this->validation->set_rules('target', 'Target', 'min_length[3]|max_length[12]');

        $result = $this->validation->run();

        if ($result === true) {
            $update_id = (int) segment(3);
            $data = $this->get_data_from_post();

            $data['url_string'] = $this->_sanitize_url($data['url_string']);

            if ($update_id > 0) {
                $data['date_updated'] = date('Y-m-d H:i:s', time());
                $this->model->update($update_id, $data, 'menu_items');
                $flash_msg = 'The record was successfully updated';
            } else {
                $data['date_created'] = date('Y-m-d H:i:s', time());
                $update_id = $this->model->insert($data, 'menu_items');
                $flash_msg = 'The record was successfully created';
            }

            set_flashdata($flash_msg);
            redirect('menu_items/show/'.$update_id);
        } else {
            $this->create();
        }
    }
}

/**
 * Custom validation callback for URL
 *
 * @param string $url The URL string to validate
 * @return string|true True if valid, error message if invalid
 */
public function validate_url(string $url): string|true {
    // Muss gefüllt sein (wird durch 'required' bereits geprüft, aber zur Sicherheit)
    if (empty($url)) {
        return 'The URL String cannot be empty.';
    }
    // Erlaube vollständige URLs, relative Pfade mit Slash oder einfache Strings
    if (filter_var($url, FILTER_VALIDATE_URL) || 
        preg_match('/^\/[a-z0-9\/-]+$/', $url) || 
        preg_match('/^[a-z0-9-]+$/', $url)) {
        return true;
    }
    return 'The URL String must be a valid URL, relative path, or simple slug.';
}

/**
 * Sanitize URL while preserving its structure
 *
 * @param string $url The URL to sanitize
 * @return string Sanitized URL
 */
private function _sanitize_url(string $url): string {
    // Vollständige URL
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        return filter_var($url, FILTER_SANITIZE_URL);
    }
    // Relativer Pfad mit Slash
    if (preg_match('/^\/[a-zA-Z0-9\/-]*$/', $url)) {
        return strtolower($url);
    }
    // Einfacher String ohne Slash
    if (preg_match('/^[a-z0-9-]+$/', $url)) {
        return strtolower($url);
    }
    // Fallback: Als Slug mit Slash behandeln
    return '/' . strtolower(url_title($url));
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
            // delete all of the comments associated with this record
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'menu_items';
            $this->model->query_bind($sql, $params);

            //delete the record
            $this->model->delete($params['update_id'], 'menu_items');

            // set the flashdata
            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            // redirect to the manage page
            redirect('menu_items/manage');
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
        redirect('menu_items/manage');
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
        $record_obj = $this->model->get_where($update_id, 'menu_items');

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
        $data['title'] = post('title', true);
        $data['url_string'] = post('url_string', true);
        $data['parent_id'] = post('parent_id', true);
        $data['sort_order'] = post('sort_order', true);
        $data['published'] = post('published', true);
        $data['target'] = post('target', true);
        $data['date_created'] = post('date_created', true);
        $data['date_updated'] = post('date_updated', true);        
        $data['menus_id'] = post('menus_id');
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
     * Get admin_id sending a trongate_token
     * 
     * @return int|false trongate_user_id or false
     */
    private function _get_current_admin_id($tg_token): int|false {
        $this->module('trongate_tokens');
        $tguser_id = $this->trongate_tokens->_get_user_id($tg_token);
        return $tguser_id;
    }

    /**
     * Get the name of a related record from a specified table and column
     *
     * @param int|null $id ID of the record
     * @param string $table Table name
     * @param string $column Column to fetch (e.g., 'title', 'name')
     * @param string $default Default value if not found
     * @return string The name or default value
     */
    private function get_related_name(?int $id, string $table, string $column, string $default = 'unset'): string {
        if (!$id || $id <= 0) {
            return $default;
        }
        $result = $this->model->get_one_where('id', $id, $table);
        return $result ? $result->$column : $default;
    }

    /**
     * Provides options for related records, excluding a specific ID
     *
     * @param int $exclude_id ID to exclude (e.g., current item)
     * @param string $table Table name
     * @param string $column Column to fetch (e.g., 'title', 'name')
     * @param string $default_option Default option text
     * @return array Associative array with options
     */
    private function get_related_options(int $exclude_id = 0, string $table, string $column, string $default_option = 'None'): array {
        $items = $this->model->get($column, $table);
        $options = ['' => $default_option];
        foreach ($items as $item) {
            if ($item->id != $exclude_id) {
                $options[$item->id] = $item->$column;
            }
        }
        return $options;
    }

    private function get_parent_options(int $current_id = 0): array {
        return $this->get_related_options($current_id, 'menu_items', 'title', 'No Parent');
    }

    private function get_parent_name(?int $parent_id): string {
        return $this->get_related_name($parent_id, 'menu_items', 'title');
    }

    private function get_menu_name(?int $menus_id): string {
        return $this->get_related_name($menus_id, 'menus', 'name');
    }

    /**
     * Replace menu and parent IDs with names in multiple rows
     *
     * @param array $rows Rows with menu and parent IDs
     * @return array Rows with names instead of IDs
     */
    private function _add_names_to_rows(array $rows): array {
        foreach ($rows as &$row) {
            $row->menus_id = $this->get_menu_name($row->menus_id);
            $row->parent_id = $this->get_parent_name($row->parent_id);
            // Optional: Published as yes/no
            // $row->published = ($row->published == 1) ? 'Yes' : 'No';
        }
        unset($row); // Referenz auflösen
        return $rows;
    }

    /**
     * Provides options for the link target like _self, _blank
     *
     * @return array Associative array with target options
     */
    public function get_target_options(): array {
        return [
            '_self' => 'Same Tab (_self)',
            '_blank' => 'New Tab (_blank)',
            '_parent' => 'Parent Frame (_parent)',
            '_top' => 'Top Frame (_top)'
        ];
    }

// ---------------------------------------------------------------------------------  

    /**
     * Fetch options for a one-to-many relation.
     *
     * @param int|null $selected_key The currently selected ID (default 0).
     * @param string $target_table The target table (e.g., 'menu_items').
     * @param string $relation_table The relation table (e.g., 'menus').
     * @return array Options for the relation.
     */
    private function _get_relation_options(?int $selected_key = 0, string $target_table, string $relation_table): array {
        $this->module('module_relations');
        // Stelle sicher, dass $selected_key immer ein Integer ist
        $selected_key = (int) ($selected_key ?? 0);
        return $this->module_relations->_fetch_options($selected_key, $target_table, $relation_table);
    }

    /**
     * Fetch a full object from a relation table.
     *
     * @param int $id The ID of the object.
     * @param string $table The relation table (e.g., 'blog_sources').
     * @return object|null The object or null if not found.
     */
    function _get_relation_obj(int $id, string $table): ?object {
        $obj = $this->model->get_one_where('id', $id, $table);
        return $obj ?: null;
    }

    
}


/*

Praktisch wäre, man könnte Menüs und Items auf einer einzigen Seite herstellen und mittels der API und JS auch direkt speichern.
Es gibt einen Speicher button, der alles in die jeweiligen Table speichert.



*/