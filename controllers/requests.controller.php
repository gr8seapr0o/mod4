<?php

/**
 * controller for all ajax requests /
 */
class RequestsController extends Controller
{
    public function __construct($data = array())
    {
        parent::__construct(!$data);
        $this->model = new Request();
    }

    /**
     * search tags in nav bar /
     */
    public function search_tags()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->model->newSearchTags($_POST['search']);
        }
    }

    /**
     * get amount of all readers of the article /
     */
    public function all_readers()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->model->allReaders($_POST['search'],$_POST['reading']);
        }
    }

    /**
     * add plus or minus to comment /
     */
    public function add_plus_minus()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            if (array_key_exists("symbol",$_POST)) {
                if($_POST['symbol'] == "1") {
                    $this->model->addPlus($_POST['search']);
                } elseif ($_POST['symbol'] == "2") {
                    $this->model->addMinus($_POST['search']);
                }
            }
        }
    }

    /**
     * for adding comment to news /
     */
    public function add_comments()
    {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            
        }
    }
    
    public function build_category() 
    {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->model->allCategories($_POST['search']);
        }
    }
    
    public function header_color() 
    {
        if($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->model->getColor();
        }
    }
}
