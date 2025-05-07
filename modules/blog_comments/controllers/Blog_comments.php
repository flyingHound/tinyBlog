<?php 
class Blog_comments extends Trongate {

	/**
     * Displays the comment records.
     * Retrieves necessary from the model, and loads the management view.
     *
     * @return void
     */
    public function manage(): void {
        $this->module('trongate_security');
        $this->trongate_security->_make_sure_allowed();
        
        // $data['rows'] = $this->model->get('id', 'blog_comments');
		$data['headline'] = 'Blog Comments';
        $data['view_module'] = 'blog_comments';
        $data['view_file'] = 'manage';
        $this->template('tiny_bootstrap', $data);
    }
}
?>