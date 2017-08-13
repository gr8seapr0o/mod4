<?php
/**
 * controller for log in and log out /
 * use User model /
 */
class UsersController extends Controller{

    /**
     * UsersController constructor /
     * create User model /
     * @param array $data
     */
    public function __construct($data = array())
    {
        parent::__construct($data);
        $this->model = new User();
    }

    /**
     * action for login for all roles /
     * set Session[login] /
     * set Session[role] /
     */
    public function login()
    {
        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);

        if ($_POST) {
            if (!empty($_POST['login']) && !empty($_POST['password'])) {

                $user = $this->model->getByLogin($_POST['login']); // false or true
                $hash = md5(Config::get('salt') . $_POST['password']);

                if ($user && $hash == $user['password']) {
                    Session::set('id', $user['id']);
                    Session::set('login', $user['login']);
                    Session::set('role', $user['role']);
                }

                if (Session::get('role') == 'admin') {
                    Router::redirect('/admin/');
                } else {
                    Router::redirect('/user/');
                }
            } else {
                Session::setFlash('Please fill in all fields');
            }
        } else {
            Session::setFlash('Login and password are incorrect');
            return false;
        }
    }
    
    /**
     * destroy Session and redirect to / /
     */
    public function logout()
    {
        Session::destroy();
        Router::redirect('/');
    }

    /**
     * action for registration users /
     */
    public function registration()
    {
        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);


        if ($_POST) {
            if (!empty($_POST['first_name']) && !empty($_POST['second_name'])&& !empty($_POST['login_name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['date_of_birth'])) {

                $first_name  = $_POST['first_name'];
                $second_name = $_POST['second_name'];
                $login       = $_POST['login_name'];
                $email       = $_POST['email'];
                $password    = md5(Config::get('salt').$_POST['password']); // salt + password
                $date        = $_POST['date_of_birth'];

                // php filter for validation emails
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    Session::setFlash('Please enter real email address!');
                }
                
                // check email
                if ($this->model->getByEmail($email)) {
                    Session::setFlash('This email is used!');
                    return false;
                }
                
                // check login
                if ($this->model->getByLogin($login)) {
                    Session::setFlash('The login is used!');
                    return false;
                }
                
                $this->model->registerUser($first_name, $second_name, $login, $email, $password, $date);

                Session::set('login',$login);
                Session::set('role','user');
                
                Router::redirect('/user/'); // to the home page
            } else { 
                Session::setFlash('Please fill in all fields!');
            }
        } else {
            return false;
        }
    }

    /**
     * To show all users /
     */
    public function index()
    {
        // we have data variable in parent class
        $this->data['users'] = $this->model->getUsersList();

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }

    /**
     * show 1 user with all his comments /
     */
    public function view()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {
            $id = strtolower($params[0]);
            $this->data['user'] = $this->model->getUserById($id);
            $this->data['user_comments'] = $this->model->getUserComments($id);
        }

        /*
         * create array for pagination tree
         */
        $count_rows  = 0;
        $this->data['pagination_news'] = array();
        foreach($this->data['user_comments'] as $comment) {
            $this->data['pagination_comment'][$count_rows/20][] = $comment;
            $count_rows++;
        }

        // data for pagination work
        $this->data['current_comment'] = (isset($params[0])) ? $params[0] : 0;
        $this->data['current_pag'] = (isset($params[1])) ? $params[1] : 0;
        $this->data['last_pag'] = (int)(count($this->data['user_comments'])/20)-1;

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }
    
    /**
     * action for watching all users /
     */
    public function admin_index() 
    {
        // we have data variable in parent class
        $this->data['users'] = $this->model->getUsersList();
    }

    /**
     * show 1 user with all his comments /
     */
    public function admin_view()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {
            $id = strtolower($params[0]);
            $this->data['user'] = $this->model->getUserById($id);
            $this->data['user_comments'] = $this->model->getUserComments($id);
        }

        /*
         * create array for pagination tree
         */
        $count_rows  = 0;
        $this->data['pagination_news'] = array();
        foreach($this->data['user_comments'] as $comment) {
            $this->data['pagination_comment'][$count_rows/20][] = $comment;
            $count_rows++;
        }

        // data for pagination work
        $this->data['current_comment'] = (isset($params[0])) ? $params[0] : 0;
        $this->data['current_pag'] = (isset($params[1])) ? $params[1] : 0;
        $this->data['last_pag'] = (int)(count($this->data['user_comments'])/20)-1;
    }

    /**
     * action for watching all /
     */
    public function user_index ()
    {
        // we have data variable in parent class
        $this->data['users'] = $this->model->getUsersList();

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }

    /**
     * show 1 user with all his comments /
     */
    public function user_view()
    {
        $params = App::getRouter()->getParams();

        if (isset($params[0])) {
            $id = strtolower($params[0]);
            $this->data['user'] = $this->model->getUserById($id);
            $this->data['user_comments'] = $this->model->getUserComments($id);
        }

        /*
         * create array for pagination tree
         */
        $count_rows  = 0;
        $this->data['pagination_news'] = array();
        foreach($this->data['user_comments'] as $comment) {
            $this->data['pagination_comment'][$count_rows/20][] = $comment;
            $count_rows++;
        }

        // data for pagination work
        $this->data['current_comment'] = (isset($params[0])) ? $params[0] : 0;
        $this->data['current_pag'] = (isset($params[1])) ? $params[1] : 0;
        $this->data['last_pag'] = (int)(count($this->data['user_comments'])/20)-1;

        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
    }
}

