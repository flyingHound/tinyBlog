<?php
class Enquiries extends Trongate {

    private $default_limit = 20;
    private $per_page_options = array(10, 20, 50, 100);
    private $template_to_use = 'tiny_blog';
    private $admin_template = 'tiny_bootstrap';
    private $settings_path = APPPATH . 'modules/enquiries/views/settings.json';
    private $vars;

    function __construct() {
        parent::__construct();
        // Default fallback settings
        $this->vars = [
            'recipient_email' => defined('OUR_EMAIL_ADDRESS') ? OUR_EMAIL_ADDRESS : '',
            'save_to_db' => '1',
            'send_email' => '0',
            'response_type' => 'redirect',
            'flash_message' => 'Thank you for your enquiry!'
        ];
        $this->_ensure_settings_location_exists(); // Ensure folder exists
    }

    /**
     * Initialize settings for this controller.
     *
     * @return array Controller configuration
     */
    function _init_settings(): array {
        $settings_file = 'posts_picture_settings.json';

        if (file_exists($settings_file)) {
            $settings = json_decode(file_get_contents($settings_file), true);
        } else {
            $settings = [
                'recipient_email' => defined('OUR_EMAIL_ADDRESS') ? OUR_EMAIL_ADDRESS : '',
                'save_to_db' => '1', // ??
                'send_email' => '0',
                'response_type' => 'redirect', // 'redirect' / 'flash_message'
                'flash_message' => '' //'Thank you for your enquiry!'
            ];
        }
        return $settings;
    }

    /**
     * Ensure the settings.json directory exists
     */
    private function _ensure_settings_location_exists() {
        $settings_dir = dirname($this->settings_path);
        $settings_dir = str_replace('\\', DIRECTORY_SEPARATOR, $settings_dir);
        if (!is_dir($settings_dir)) {
            mkdir($settings_dir, 0755, true);
        }
    }

    /**
     * Display a page with a contact form
     * 
     */
    function index(): void {
        $data = $this->_get_data_from_post();
        $data['question'] = $this->_get_question();
        $data['options'] = $this->_get_possible_answers();
        $data['form_location'] = 'enquiries/submit'; 
        $data['view_module'] = 'enquiries';
        $data['view_file'] = 'contact_form';
        $this->template($this->template_to_use, $data);
    }

    /**
     * Renders a contact form HTML
     *
     * @return string HTML content of the contact form
     */
    function widget_contact() {
    	$data = $this->_get_data_from_post();
        $data['question'] = $this->_get_question();
        $data['options'] = $this->_get_possible_answers();
        $data['form_location'] = 'enquiries/submit';
        return $this->view('contact_form', $data, true) ?: '';
    }

    function _get_question() {
        $question = 'What is the capital of France?';
        return $question;
    }

    function _get_possible_answers() {
        $answer = post('answer', true);
        settype($answer, 'int');

        if ($answer == 0) {
            $answers[''] = 'Select...';
        }
        
        $answers[1] = 'Glasgow';
        $answers[2] = 'London';
        $answers[3] = 'New York';
        $answers[4] = 'Paris';
        return $answers;
    }

    function _get_correct_answer() {
        $correct_answer = 4;
        return $correct_answer;
    }

    function thankyou() {
        $data['view_module'] = 'enquiries';
        $data['view_file'] = 'thankyou';
        $this->template($this->template_to_use, $data);
    }

    function manage() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (segment(4) !== '') {
            $data['headline'] = 'Search Results';
            $searchphrase = trim($_GET['searchphrase']);
            $params['name'] = '%'.$searchphrase.'%';
            $params['email_address'] = '%'.$searchphrase.'%';
            $sql = 'select * from enquiries
            WHERE name LIKE :name
            OR email_address LIKE :email_address
            ORDER BY date_created desc';
            $all_rows = $this->model->query_bind($sql, $params, 'object');
        } else {
            $data['headline'] = 'Manage Enquiries';
            $all_rows = $this->model->get('date_created desc');
        }

        $pagination_data['total_rows'] = count($all_rows);
        $pagination_data['page_num_segment'] = 3;
        $pagination_data['limit'] = $this->_get_limit();
        $pagination_data['pagination_root'] = 'enquiries/manage';
        $pagination_data['record_name_plural'] = 'enquiries';
        $pagination_data['include_showing_statement'] = true;
        $data['pagination_data'] = $pagination_data;

        $data['rows'] = $this->_reduce_rows($all_rows);
        // decode HTML-Entities in rows
        foreach ($data['rows'] as $row) {
            $row->name = html_entity_decode($row->name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row->email_address = html_entity_decode($row->email_address, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row->message = html_entity_decode($row->message, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        $data['selected_per_page'] = $this->_get_selected_per_page();
        $data['per_page_options'] = $this->per_page_options;
        $data['view_module'] = 'enquiries';
        $data['view_file'] = 'manage';
        $this->template($this->admin_template, $data);
    }

    function show() {
        $this->module('trongate_security');
        $token = $this->trongate_security->_make_sure_allowed();
        $update_id = segment(3);

        $this->_set_to_opened($update_id);

        if ((!is_numeric($update_id)) && ($update_id != '')) {
            redirect('enquiries/manage');
        }

        $data = $this->_get_data_from_db($update_id);
        // decode HTML-Entities for output
        $data['name'] = html_entity_decode($data['name'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $data['email_address'] = html_entity_decode($data['email_address'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $data['message'] = html_entity_decode($data['message'], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $data['opened'] = ($data['opened'] == 1 ? 'yes' : 'no');
        $data['token'] = $token;

        if ($data == false) {
            redirect('enquiries/manage');
        } else {
            $data['update_id'] = $update_id;
            $data['headline'] = 'Enquiry Information';
            $data['view_file'] = 'show_' . $this->admin_template;
            $this->template($this->admin_template, $data);
        }
    }

    function _set_to_opened($update_id) {
        $data['opened'] = 1;
        $this->model->update($update_id, $data, 'enquiries');
    }
    
    function _reduce_rows($all_rows) {
        $rows = [];
        $start_index = $this->_get_offset();
        $limit = $this->_get_limit();
        $end_index = $start_index + $limit;

        $count = -1;
        foreach ($all_rows as $row) {
            $count++;
            if (($count>=$start_index) && ($count<$end_index)) {
                $row->opened = ($row->opened == 1 ? 'yes' : 'no');
                $rows[] = $row;
            }
        }

        return $rows;
    }

    function submit() {
        $submit = post('submit', true);

        if ($submit == 'Submit') {

            $this->validation->set_rules('name', 'Name', 'required|min_length[2]|max_length[255]');
            $this->validation->set_rules('email_address', 'Email Address', 'required|min_length[5]|max_length[255]|valid_email_address|valid_email');
            $this->validation->set_rules('message', 'Message', 'required|min_length[2]');
            $this->validation->set_rules('answer', 'prove you are human answer', 'required|callback_answer_check');

            $result = $this->validation->run();
            $settings = $this->_init_settings();

            if ($result == true) {
                $data = $this->_get_data_from_post();
                // security for xss
                $data['name'] = htmlspecialchars(strip_tags($data['name']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $data['email_address'] = htmlspecialchars(strip_tags($data['email_address']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $data['message'] = htmlspecialchars(strip_tags($data['message']), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $data['opened'] = ($data['opened'] == 1 ? 1 : 0);
                $data['date_created'] = time();
                unset($data['answer']);

                // insert the new record, if activated
                if ($settings['save_to_db'] == '1') {
                    $update_id = $this->model->insert($data, 'enquiries');
                }

                // send email

                // send email, if activated
                if ($settings['send_email'] == '1' && !empty($settings['recipient_email'])) {
                    $this->$this->send_email($data['name'], $data['email_address'], $data['message'], $settings['recipient_email']);
                }
                
                // Answer based on response_type
				$flash_msg = $settings['flash_message'] ?? '';

				if ($flash_msg !== '') {
				    set_flashdata($flash_msg);
				}

				$redirect_target = ($settings['response_type'] === 'redirect') 
				    ? 'enquiries/thankyou' 
				    : 'enquiries/index';

				redirect($redirect_target);
            } else {
                //form submission error
                $this->index();
            }

        }
    }

    function send_email($name, $email, $message, $recipient_email) {
        $to = $recipient_email;
        $subject = 'New  Enquiry by ' . $name;
        $body = "Name: $name\nEmail: $email\nMessage: $message";
        // $headers = "From: $email\r\n";
        // mail($to, $subject, $body, $headers);
        // Protection against Header-Injection
        $headers = "From: no-reply@yourdomain.com\r\n"; // use static sender-address
        $headers .= "Reply-To: $email\r\n";
        // Entferne potenziell gefährliche Zeichen aus $email
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            mail($to, $subject, $body, $headers);
        }
    }

    /** Look up how Trongate is using Email Sending already
     * 
     * via composer: 'composer require phpmailer/phpmailer'
     * put files in: 'application/libraries/PHPMailer/'
     */
    function phpmailer_send_email($name, $email, $message, $recipient_email) {
        // PHPMailer Klassen laden (müssen über Composer installiert sein)
        require APPPATH . 'libraries/PHPMailer/src/Exception.php';
        require APPPATH . 'libraries/PHPMailer/src/PHPMailer.php';
        require APPPATH . 'libraries/PHPMailer/src/SMTP.php';

	    // Namensräume verwenden
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

	    try {
	        // Server-Einstellungen
	        $mail->isSMTP();                                          // SMTP verwenden
	        $mail->Host       = 'smtp.gmail.com';                     // SMTP-Server (z. B. Gmail)
	        $mail->SMTPAuth   = true;                                 // SMTP-Authentifizierung aktivieren
	        $mail->Username   = 'deine.email@gmail.com';              // Deine E-Mail-Adresse
	        $mail->Password   = 'dein-app-passwort';                  // Dein App-spezifisches Passwort
	        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;       // Verschlüsselung
	        $mail->Port       = 587;                                  // TCP-Port

	        // Absender und Empfänger
	        // $mail->setFrom($email, $name);                           // Absender (der Benutzer, der das Formular ausfüllt)
            $mail->setFrom('no-reply@yourdomain.com', 'Contact Form'); // Statische Absenderadresse
	        $mail->addAddress($recipient_email);                      // Empfänger (aus den Einstellungen)
            $mail->addReplyTo($email, $name);                        // Antwort an den Benutzer

	        // Inhalt
	        $mail->isHTML(false);                                     // Als reiner Text
	        $mail->Subject = 'Neue Anfrage von ' . $name;
	        $mail->Body    = "Name: $name\nE-Mail: $email\nNachricht: $message";

	        // E-Mail senden
	        $mail->send();
	        return true;
	    } catch (PHPMailer\PHPMailer\Exception $e) {
	        // Fehlerbehandlung
	        log_message('error', 'E-Mail konnte nicht gesendet werden: ' . $mail->ErrorInfo);
	        return false;
	    }
	}

    /**
     * Deletes a record from the database
     */
    function submit_delete() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed(); // Nur Admins dürfen löschen

        $submit = post('submit');
        $params['update_id'] = (int) segment(3);

        if (($submit == 'Yes - Delete Now') && ($params['update_id']>0)) {
            $sql = 'delete from trongate_comments where target_table = :module and update_id = :update_id';
            $params['module'] = 'enquiries';
            $this->model->query_bind($sql, $params);

            $this->model->delete($params['update_id'], 'enquiries');

            $flash_msg = 'The record was successfully deleted';
            set_flashdata($flash_msg);

            redirect('enquiries/manage');
        }
    }

    function _get_limit() {
        if (isset($_SESSION['selected_per_page'])) {
            $limit = $this->per_page_options[$_SESSION['selected_per_page']];
        } else {
            $limit = $this->default_limit;
        }
        return $limit;
    }

    function _get_offset() {
        $page_num = segment(3);
        if (!is_numeric($page_num)) {
            $page_num = 0;
        }
        if ($page_num>1) {
            $offset = ($page_num-1)*$this->_get_limit();
        } else {
            $offset = 0;
        }
        return $offset;
    }

    function _get_selected_per_page() {
        if (!isset($_SESSION['selected_per_page'])) {
            $selected_per_page = $this->per_page_options[1];
        } else {
            $selected_per_page = $_SESSION['selected_per_page'];
        }
        return $selected_per_page;
    }

    function set_per_page($selected_index) {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        if (!is_numeric($selected_index)) {
            $selected_index = $this->per_page_options[1];
        }

        $_SESSION['selected_per_page'] = $selected_index;
        redirect('enquiries/manage');
    }

    function _get_data_from_db($update_id) {
        $record_obj = $this->model->get_where($update_id, 'enquiries');
        if ($record_obj == false) {
            $this->template('error_404');
            die();
        } else {
            $data = (array) $record_obj;
            return $data;        
        }
    }

    function _get_data_from_post() {
        $data['name'] = post('name', true);
        $data['email_address'] = post('email_address', true);
        $data['message'] = post('message', true);
        $data['opened'] = post('opened', true);   
        $data['answer'] = post('answer', true);     
        return $data;
    }

    function answer_check($str) {
        settype($str, 'int');
        $correct_answer = $this->_get_correct_answer();
        if ($str == $correct_answer) {
            return true;
        } else {
            $error_msg = 'You did not select the correct answer';
            return $error_msg;
        }
    }

    /**
     * Generates a widget displaying the 10 most recent enquiries.
     *
     * This function ensures the user has permission to view enquiries via Trongate Security,
     * retrieves the 10 most recent enquiry records ordered by date_created in descending order,
     * decodes HTML entities in the retrieved data, counts unread enquiries, and renders the
     * widget view. The result is returned as a string or an empty string if the view fails.
     *
     * @return string The rendered widget HTML, or an empty string if the view fails to load.
     * @throws \Exception If the security check or database query fails.
     */
    function widget_enquiries() {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed(); // Ensure the user is authorized to view enquiries

        // Retrieve the 10 most recent enquiries ordered by date_created descending
        $sql = "SELECT * FROM enquiries ORDER BY date_created DESC LIMIT 10";
        $all_rows = $this->model->query($sql, 'object');

        // Decode HTML entities in each row to ensure proper display
        foreach ($all_rows as $row) {
            $row->name = html_entity_decode($row->name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row->email_address = html_entity_decode($row->email_address, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row->message = html_entity_decode($row->message, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }

        $data['rows'] = $all_rows;
        $data['num_unread_enquiries'] = $this->model->count_where('opened', 0, '=', 'enquiries');
        $data['num_enquiries'] = $this->_get_total_count('enquiries');

        // Render the widget view with the prepared data, return HTML or empty string on failure
        return $this->view('_widget_enquiries', $data, true) ?: '';
    }

    /**
     * Renders a dropdown widget for un-opened enquiries in the header.
     *
     * @return string The rendered HTML for the enquiries dropdown widget.
     */
    function widget_enquiries_dropdown(): string {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();

        // DB query, limit 6
        $all_rows = $this->model->get_where_custom('opened', 0, '=', 'date_created desc', 'enquiries', '6', null);
        // decode HTML-Entities in rows
        foreach ($all_rows as $row) {
            $row->name = html_entity_decode($row->name, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row->email_address = html_entity_decode($row->email_address, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $row->message = html_entity_decode($row->message, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        // count all in DB
        $num_unread_enquiries = $this->model->count_where('opened', 0, '=', 'enquiries');

        // Data for view
        $data = [
            'headline' => 'Enquiries',
            'rows' => $all_rows,
            'num_unread_enquiries' => $num_unread_enquiries
        ];

        // Render and return View
        return $this->view('_widget_enquiries_dropdown', $data, true) ?: '';
    }

    /**
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
}