<?php
/**
 * here we have actions for tags /
 * controller task is to form data witch he will sent to view /
 * uses Tag model
 */
class TagsController extends Controller {

    /**
     * NewsController constructor /
     * @param array $data
     */
    public function __construct($data = array())
    {
        parent::__construct($data);
        $this->model = new Tag();
    }
    
    /**
     * default action show all tags /
     */
    public function index()
    {
        // we have data variable in parent class
        $this->data['tags'] = $this->model->getList();

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }

    /**
     * view all news for 1 tag /
     */
    public function view()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {
            $id = strtolower($params[0]);
            $this->data['tag'] = $this->model->getTagName($id);
            $this->data['tag_news'] = $this->model->getNewsByTagId($id);
        }

        /*
         * create array for pagination tree
         */

//        echo"<pre>";
//        print_r($this->data['tag_news']);
//        die;
        $count_rows  = 0;
        $this->data['pagination_news'] = array();
        foreach($this->data['tag_news'] as $news) {
            $this->data['pagination_tags'][$count_rows/5][] = $news;
            $count_rows++;
        }

        // data for pagination work
        $this->data['current_tag'] = (isset($params[0])) ? $params[0] : 0;
        $this->data['current_pag'] = (isset($params[1])) ? $params[1] : 0;
        $this->data['last_pag'] = (int)(count($this->data['tag_news'])/5)-1;

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }


    // actions for administrators ====================

    /**
     * admin index for all tags /
     */
    public function admin_index()
    {
        $this->data['tags'] = $this->model->getList();

        if ($_POST) {
            $this->model->createTag($_POST['tag_name']);
        }
    }

    /**
     * view all news for 1 tag /
     */
    public function admin_view()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {
            $id = strtolower($params[0]);
            $this->data['tag'] = $this->model->getTagName($id);
            $this->data['tag_news'] = $this->model->getNewsByTagId($id);
        }

        /*
         * create array for pagination tree
         */
        $count_rows  = 0;
        $this->data['pagination_news'] = array();
        foreach($this->data['tag_news'] as $news) {
            $this->data['pagination_tags'][$count_rows/5][] = $news;
            $count_rows++;
        }

        // data for pagination work
        $this->data['current_tag'] = (isset($params[0])) ? $params[0] : 0;
        $this->data['current_pag'] = (isset($params[1])) ? $params[1] : 0;
        $this->data['last_pag'] = (int)(count($this->data['tag_news'])/5)-1;
    }

    /**
     * TODO redact this action
     */
    public function admin_add()
    {
        if ( $_POST ) {
            $result = $this->model->save($_POST);
            echo "<pre>";
            print_r($_POST);
            die;
            if ($result) {
                Session::setFlash('Article was saved.');
            } else {
                Session::setFlash('Error');
            }
            Router::redirect('/admin/news/');
        }
    }


    // actions for login users ====================

    /**
     * user default for all tags /
     */
    public function user_index()
    {
        // we have data variable in parent class
        $this->data['tags'] = $this->model->getList(true);

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }

    /**
     * view all news for 1 tag /
     */
    public function user_view()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {
            $id = strtolower($params[0]);
            $this->data['tag'] = $this->model->getTagName($id);
            $this->data['tag_news'] = $this->model->getNewsByTagId($id);
        }

        /*
         * create array for pagination tree
         */
        $count_rows  = 0;
        $this->data['pagination_news'] = array();
        foreach($this->data['tag_news'] as $news) {
            $this->data['pagination_tags'][$count_rows/5][] = $news;
            $count_rows++;
        }

        // data for pagination work
        $this->data['current_tag'] = (isset($params[0])) ? $params[0] : 0;
        $this->data['current_pag'] = (isset($params[1])) ? $params[1] : 0;
        $this->data['last_pag'] = (int)(count($this->data['tag_news'])/5)-1;


        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }

    // actions for moderators ==================== 

    public function moderator_index()
    {
        // TODO same as index()
    }

}