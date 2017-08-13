<?php
/**
 * Basic class for all controllers /
 * All data we give from the controller to the view /
 * protected $data /
 * For access to model object
 * protected $model /
 * Parameters from request line
 * protected $params /
 */
class Controller {
    // All data we give from the controller to the view
   protected $data;

    // For access to model object
    protected $model;

    // Parameters from request line
    protected $params;

    /**
     * Controller constructor.
     * @param array $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    public function __construct($data = array()) 
    {
        $this->data = $data;
        $this->params = App::getRouter()->getParams();
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }


    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }
}