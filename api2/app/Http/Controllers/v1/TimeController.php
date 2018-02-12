<?php  
/* 
 * 时间计算(年，月，日，时，分，秒) 
 * $createtime 可以是当前时间 
 * $gettime 你要传进来的时间 
 */
namespace App\Http\Controllers\v1;
use App\Http\Controllers\Controller;

class TimeController extends Controller {

    protected $createtime;
    protected $gettime;

    public function  __construct($createtime,$gettime) {  
        $this->createtime = $createtime;  
        $this->gettime = $gettime;  
    }  
    public function getSeconds() {  
        return $this->createtime-$this->gettime;  
    }  
    public function getMinutes() {
        return ($this->createtime-$this->gettime)/(60);  
    }  
    public function getHours() {
        return ($this->createtime-$this->gettime)/(60*60);  
    }  
    public function getDay() {
        return ($this->createtime-$this->gettime)/(60*60*24);  
    }
    public function getMonth() {  
        return ($this->createtime-$this->gettime)/(60*60*24*30);  
    }  
    public function getYear()  
    {  
        return ($this->createtime-$this->gettime)/(60*60*24*30*12);  
    }
    public function index() {  
        if($this->getYear() > 1) {  
            if($this->getYear() > 2) {  
                return date("Y-m-d",$this->gettime);  
                exit();  
            }  
            return intval($this->getYear())."年前";  
            exit();  
        }  
        if($this->getMonth() > 1) {  
            return intval($this->getMonth())."个月前";  
            exit();  
        }  
        if($this->getDay() > 1) {  
            return intval($this->getDay())."天前";  
            exit();  
        }  
        if($this->getHours() > 1) {
            return intval($this->getHours())."小时前";  
            exit();  
        }  
        if($this->getMinutes() > 1) {  
            return intval($this->getMinutes())."分钟前";  
            exit();  
        }  
        if($this->getSeconds() > 1) {  
            return intval($this->getSeconds()-1)."秒前";  
            exit();  
        }
    }  
}  