<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Debug extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('url');
    }

    public function index()
    {
        echo 'try <a href="'.base_url('debug/this').'">this</a>';
    }

    public function this($arg = '', $switch = '')
    {
        echo "<title>{$arg} - Debug</title>";

        if (isset($this->$arg))
        {
            if ('' === $switch)
            {
                echo "<pre>";
                print_r($this->$arg);
            }
            else
            {
                switch ($switch)
                {
                case 'class':
                    echo "<pre>";
                    print_r(get_class($this->$arg));
                    break;
                case 'methods':
                    echo "<pre>";
                    print_r(get_class_methods($this->$arg));
                    break;
                case 'vars':
                    echo "<pre>";
                    print_r(get_class_vars(get_class($this->$arg)));
                    break;
                default:
                    echo <<<EOD
<h1>Only below are supported</h1>
<ul>
    <li>None, means blank</li>
    <li>class</li>
    <li>methods</li>
    <li>vars</li>
</ul>
EOD;
                    break;
                }
            }
        }
        else
        {
            if (in_array($arg, ['', 'self', 'methods', 'class', 'vars']))
            {
                switch ($arg)
                {
                case 'self':
                    echo "<pre>";
                    print_r($this);
                    break;
                case 'class':
                    echo "<pre>";
                    print_r(get_class($this));
                    break;
                case 'methods':
                    echo "<pre>";
                    print_r(get_class_methods($this));
                    break;
                case '':
                case 'vars':
                    //echo "<pre>";
                    echo "<ul>";
                    echo "<li>self, means this self stuff</li>";
                    foreach (array_keys((array)$this) as $index => $var)
                    {
                        echo "<li>$var</li>";
                    }
                    echo "</ul>";
                    //print_r(get_class_vars(get_class($this)));
                    break;
                default:
                    echo <<<EOD
<h1>Only below are supported</h1>
<ul>
    <li>None, means blank</li>
    <li>class</li>
    <li>methods</li>
    <li>vars</li>
</ul>
EOD;
                    break;
                }
            }
            else
            {
                echo 'sorry about '.$arg;
            }
        }
    }
}
