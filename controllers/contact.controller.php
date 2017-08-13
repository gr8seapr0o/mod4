<?php
/**
 * TODO finish contact us form
 */

class ContactController extends Controller{
    
    public function __construct($data = array())
    {
        parent::__construct($data);
        $this->model = new Message();
    }

    public function index()
    {
        // data for advertising
        $this->data['adv'] = $this->model->getAdvertising();
        shuffle($this->data['adv']);
        $this->data['adv_left'] = array_slice($this->data['adv'],0,4);
        $this->data['adv_right'] = array_slice($this->data['adv'],3,4);
        
        if ($_POST) {
            if ($this->model->save($_POST)) {
                Session::setFlash('Success!');
            } else {
                Session::setFlash('There is some problems');
            }
        }
    }
    
    public function admin_index() 
    {
        $this->data['messages'] = $this->model->getList();
    }
}