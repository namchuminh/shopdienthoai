<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Search extends CI_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model('frontend/Mcontent');
		$this->load->model('frontend/Mcategory');
		$this->load->model("frontend/Mproduct");
		$this->data['com']='search';
	}
	public function index(){
		$this->load->library('phantrang');
		$key = $_GET['search'];
		$aurl= explode('/',uri_string());
		$url = $aurl[0].'?search='.str_replace(' ', '+', $key);
		$limit=10;
		$current=$this->phantrang->PageCurrent();
		$first=$this->phantrang->PageFirst($limit, $current);
		$total = $this->Mproduct->product_search_count($key);
		$this->data['list'] = $this->Mproduct->product_search($key,$limit,$first);;
		$this->data['strphantrang']=$this->phantrang->PagePer($total, $current, $limit, $url= $url);
		$this->data['title']='Smart Store - Bạn muốn tìm gì ?';  
		$this->data['view']='index';
		$this->data['count'] = $total;
		$this->data['key'] =$key;
		$this->load->view('frontend/layout',$this->data);
	}
}