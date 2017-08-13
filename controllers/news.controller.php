<?php
/**
 * here we have actions for news /
 * controller task is to form data witch he will sent to view /
 */
class NewsController extends Controller {

    /**
     * NewsController constructor /
     * use Article model /
     * @param array $data
     */
    public function __construct($data = array())
    {
        parent::__construct($data);
        $this->model = new Article();
    }

    /**
     * data for carousel with pictures for 1 article /
     * @param $id
     * @return bool
     */
    public function getCarouselData($id)
    {
        // data for OwlCarousel
        $dir_name = ROOT.DS."webroot".DS."uploads".DS.$id;

        if (!is_dir($dir_name)) {
            return false;
        }
            
        $dir = scandir($dir_name);
        $pictures = array();

        foreach ($dir as $file) {

            if (is_file($dir_name.DS.$file)) { // fail or not

                $type = new SplFileInfo($file); // need it for type of the file

                if ($type->getExtension()=='jpg' || $type->getExtension() == 'png' || $type->getExtension() == 'jpeg') { // check type of the file
                    array_push($pictures,DS."webroot".DS."uploads".DS.$id.DS.$file);

                }
            }
        }

        $this->data['carousel'] = $pictures;
    }
    

    // actions for all visitors ====================
    // index TODONE
    // view TODONE

    /**
     * default action for news and all site /
     */
    public function index()
    {
        $attr = $this->model->getAttributes();
        $attr_result = array();
        if (!empty($attr)) {
            $i = 1;
            foreach($attr as $row) {
                $attr_result[$row['id']][$i]=$row['value'];
                $i++;
            }
        }
        // data for checkboxes for filter
        $this->data['attributes'] = $attr_result;

        // array with selected filters
        $this->data['selected_filters'] = (isset($_REQUEST['filter']) && is_array($_REQUEST['filter'])) ? $_REQUEST['filter'] : array();

        $selected_attr = array();
        foreach ($this->data['selected_filters'] as $f_key =>$f_value) {
            foreach ($this->data['attributes'] as $a_key=>$a_value) {

                if (array_key_exists($f_value,$this->data['attributes'][$a_key])) {
                    $selected_attr[$a_key][] = $this->data['attributes'][$a_key][$f_value];
                }
            }
        }
        
        // we have data variable in parent class
        $this->data['news'] = $this->model->getList($selected_attr);

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);


        // TODO pagination by 10
    }

    /**
     * action for showing 1 article /
     */
    public function view()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {
            $id = strtolower($params[0]);
            $this->data['article'] = $this->model->getByID($id);
            $this->data['article_tags'] = $this->model->getArticleTags($id);
            $this->data['article_comments'] = $this->model->getArticleComments($id);

            
            $this->getCarouselData($id);
        }

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }


    // actions for administrators ====================
    // index TODONE
    // view
    //  add TODONE

    /**
     * admin action for showing all news /
     */
    public function admin_index() 
    {
        $attr = $this->model->getAttributes();
        $attr_result = array();
        if (!empty($attr)) {
            $i = 1;
            foreach($attr as $row) {
                $attr_result[$row['id']][$i]=$row['value'];
                $i++;
            }
        }
        // data for checkboxes for filter
        $this->data['attributes'] = $attr_result;

        // array with selected filters
        $this->data['selected_filters'] = (isset($_REQUEST['filter']) && is_array($_REQUEST['filter'])) ? $_REQUEST['filter'] : array();

        $selected_attr = array();
        foreach ($this->data['selected_filters'] as $f_key =>$f_value) {
            foreach ($this->data['attributes'] as $a_key=>$a_value) {

                if (array_key_exists($f_value,$this->data['attributes'][$a_key])) {
                    $selected_attr[$a_key][] = $this->data['attributes'][$a_key][$f_value];
                }
            }
        }

        // we have data variable in parent class
        $this->data['news'] = $this->model->getList($selected_attr);
    }

    /**
     * user action for showing 1 article /
     */
    public function admin_view()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {
            $id = strtolower($params[0]);
            $this->data['article'] = $this->model->getByID($id);
            $this->data['article_tags'] = $this->model->getArticleTags($id);
            $this->data['article_comments'] = $this->model->getArticleComments($id);

            $id_user=Session::get('id');
            $this->data['user'] = $this->model->getArticleUser($id_user);
            $this->getCarouselData($id);
        }

        if($_POST) {

            $id_news = $_POST['id_news'];
            $id_user = $_POST['id_user'];
            $this->data['user'] = $this->model->getArticleUser($id_user);
            $id_parent = $_POST['id_parent'];
            $text = $_POST['text'];
            $this->model->saveComment($id_news,$id_user,$id_parent,$text);
        }
    }


    /**
     *action for analytical articles /
     */
    public function admin_analytical()
    {
        $this->data['news'] = $this->model->getAnalyticalList();
    }

    /**
     * Tadding news /
     */
    public function admin_add()
    {
        if ($_POST) {
            if (!empty($_POST['title']) && !empty($_POST['text'])) {

                $result = $this->model->saveArticle($_POST); // save to mySQL

                $id = $this->model->getID(); // name of the folder
                mkdir(ROOT.DS."webroot".DS."uploads".DS.$id); // create folder

                $dir_name = ROOT.DS."webroot".DS."uploads".DS.$id.DS;

                if ($_FILES) {
                    foreach ($_FILES['image']['error'] as $key => $error) {

                        if ($error == UPLOAD_ERR_OK) {

                            $temp_file_name = $_FILES['image']['tmp_name'][$key];
                            $file_name = $dir_name . basename($_FILES['image']['name'][$key]);
                            move_uploaded_file($temp_file_name, $file_name);
                        }
                    }
                }

                if ($result) {
                    Session::setFlash('Article was saved.');
                } else {
                    Session::setFlash('Error');
                }
                Router::redirect('/admin/news/');
            } else {
                Session::setFlash("Please fill all fields");
            }
        } else {
            return false;
        }
    }

    /**
     * edit one news /
     */
    public function admin_edit()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {
            $id = strtolower($params[0]);
            $this->data['article'] = $this->model->getByID($id);
            $this->data['art-tags'] = $this->model->getArticleTagLine($id);
            $this->data['art_category'] = $this->model->getArticleCategory($id);
            $this->data['all-categories'] = $this->model->getAllCategories();

            $this->getCarouselData($id);
        }

        if ($_POST) {
            $result = $this->model->saveEditedArticle($_POST);
            if ($result) {
                Session::setFlash('article was saved');
            } else {
                Session::setFlash('Error');
            }
        }

        $id = $this->data['article']['id'];
        $dir_name = ROOT . DS . "webroot" . DS . "uploads" . DS . $id . DS;
        if ($_FILES) {
            if (file_exists($dir_name)) {
                foreach ($_FILES['image']['error'] as $key => $error) {
                    if (!file_exists($dir_name . DS . basename($_FILES['image']['name'][$key]))) {
                        if ($error == UPLOAD_ERR_OK) {
                            $temp_file_name = $_FILES['image']['tmp_name'][$key];
                            $file_name = $dir_name . basename($_FILES['image']['name'][$key]);
                            move_uploaded_file($temp_file_name, $file_name);
                        }
                    }
                }
            } else {
                mkdir(ROOT . DS . "webroot" . DS . "uploads" . DS . $id); // create folder
                foreach ($_FILES['image']['error'] as $key => $error) {

                    if ($error == UPLOAD_ERR_OK) {
                        $temp_file_name = $_FILES['image']['tmp_name'][$key];
                        $file_name = $dir_name . basename($_FILES['image']['name'][$key]);
                        move_uploaded_file($temp_file_name, $file_name);
                    }
                }
            }
        }
    }


    // actions for login users ====================
    // index TODONE
    // view TODONE

    /**
     * user action for news and all site /
     */
    public function user_index()
    {
        $attr = $this->model->getAttributes();
        $attr_result = array();
        if (!empty($attr)) {
            $i = 1;
            foreach($attr as $row) {
                $attr_result[$row['id']][$i]=$row['value'];
                $i++;
            }
        }
        // data for checkboxes for filter
        $this->data['attributes'] = $attr_result;

        // array with selected filters
        $this->data['selected_filters'] = (isset($_REQUEST['filter']) && is_array($_REQUEST['filter'])) ? $_REQUEST['filter'] : array();

        $selected_attr = array();
        foreach ($this->data['selected_filters'] as $f_key =>$f_value) {
            foreach ($this->data['attributes'] as $a_key=>$a_value) {

                if (array_key_exists($f_value,$this->data['attributes'][$a_key])) {
                    $selected_attr[$a_key][] = $this->data['attributes'][$a_key][$f_value];
                }
            }
        }

        // we have data variable in parent class
        $this->data['news'] = $this->model->getList($selected_attr);

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }

    /**
     * user action for showing 1 article /
     */
    public function user_view()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {

            $id = strtolower($params[0]);
            $this->data['article'] = $this->model->getByID($id);
            $this->data['article_tags'] = $this->model->getArticleTags($id);
            $this->data['article_comments'] = $this->model->getArticleComments($id);

            $id_user=Session::get('id');
            $this->data['user'] = $this->model->getArticleUser($id_user);
            $this->getCarouselData($id);

        }

        if($_POST) {

            $id_news = $_POST['id_news'];
            $id_user = $_POST['id_user'];
            $id_parent = $_POST['id_parent'];
            $text = $_POST['text'];
            $this->model->saveComment($id_news,$id_user,$id_parent,$text);
        }

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }

    /**
     *action for analytical articles /
     */
    public function user_analytical()
    {
        $this->data['news'] = $this->model->getAnalyticalList();

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }


    // actions for moderators ====================

    /**
     * TODO moderator can edit articles
     *
     */
    public function moderator_index()
    {
        // TODO same as index()
    }
    
}