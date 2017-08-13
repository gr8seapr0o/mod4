<?php
/**
 * need for request processing and to call controllers action /
 * protected static $router /
 * public static $db /
 */
class App {
    
    // this parameter is static because we need only one router
    protected static $router;

    // this parameter is public because everyone need access to it
    public static $db;

    /**
     * return example of current Router /
     * @return mixed 
     */
    public static function getRouter()
    {
        return self::$router;
    }

    /**
     * this action is for simple access to example of a Router class /
     * @param $uri
     * @throws Exception
     */
    public static function run($uri) 
    {
        self::$router = new Router($uri);
        self::$db = new DB(Config::get('db.host'), Config::get('db.user'), Config::get('db.password'), Config::get('db.name'));
        
        Lang::load(self::$router->getLanguage()); // loading language settings

        // ucfirst need because the names of the  controllers begin with a capital letter
        // example : NewsController
        $controller_class = ucfirst(self::$router->getController()) . 'Controller';
        
        // example : index
        $controller_method = strtolower(self::$router->getMethodPrefix() . self::$router->getAction());

        // result = '',admin,user,moderator
        $layout = self::$router->getRouter();
        if (($layout == 'admin' && Session::get('role') != 'admin') || ($layout == 'user' && Session::get('role') != 'user')) {
            if (($layout == 'admin' || $layout == 'user') && Session::get('role') != 'admin') {
                if ($controller_method != 'admin_login') {
                    Router::redirect('/users/login/');
                }
            }
            if ($layout == 'user' && (Session::get('role') != 'user' || Session::get('role') != 'admin')) {
                if ($controller_method != 'user_login') {
                    Router::redirect('/users/login/');
                }
            }
        }

        // now we have a controller class name and a method name
        // we will create the controller instance
        $controller_object = new $controller_class();
        
        // if method exists
        if (method_exists($controller_object,$controller_method)) {
            
            // controller's action may return a view path
            $view_path = $controller_object->$controller_method();
            $view_object = new View($controller_object->getData(), $view_path);

            $content = $view_object->render();
            // after this we have content from action like
            // controller -> action data from this action was render
            // by controller - view and included
        } else {
            throw new Exception ('Method ' .  $controller_method . ' in ' . $controller_class . ' does not exist');
        }

        $layout_path = VIEWS_PATH.DS.$layout.'.html';
        
        // compact creates array from variables
        $layout_view_object = new View(compact('content'),$layout_path);
        echo $layout_view_object->render();
    }
}