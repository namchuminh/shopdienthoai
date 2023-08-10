<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Khachhang extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		if(!$this->session->userdata('sessionKhachHang')){
            redirect('dang-nhap','refresh');
        }
        $this->data['com']='khachhang';
        $this->load->model('frontend/Mproduct');
        $this->load->model('frontend/Mcategory');
        $this->load->model('frontend/Mcustomer');
        $this->load->model('frontend/Morder');
        $this->load->model('frontend/Morderdetail');
        $this->load->model('frontend/Minfocustomer');
        $this->load->model('frontend/Mprovince');
        $this->load->model('frontend/Mdistrict');
        $this->load->model('frontend/Mconfig');
        $this->data['info']=$this->Minfocustomer->customer_detail_id($this->session->userdata('id'));
        $this->load->library('session');
        $this->load->library('alias');
	}

	public function postProduct()
	{
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if(empty($_POST['catid']) || empty($_POST['name']) || empty($_POST['detail']) || empty($_POST['sortDesc']) || empty(strval($_POST['number'])) || empty($_POST['price_root']) || empty($_POST['price_buy'])){
                $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                $this->data['view']='post';
                $this->data['error']='Vui lòng nhập đủ thông tin sản phẩm!';
                $this->data['category']= $this->Mcategory->all_category();
                return $this->load->view('frontend/layout',$this->data);
            }

            if($_POST['price_root'] <= 0){
                $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                $this->data['view']='post';
                $this->data['error']='Giá bán gốc của sản phẩm phải lớn hơn 0!';
                $this->data['category']= $this->Mcategory->all_category();
                return $this->load->view('frontend/layout',$this->data);
            }

            if($_POST['price_buy'] < 0){
                $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                $this->data['view']='post';
                $this->data['error']='Giá bán của sản phẩm phải lớn hơn hoặc bằng 0!';
                $this->data['category']= $this->Mcategory->all_category();
                return $this->load->view('frontend/layout',$this->data);
            }

            if($_POST['price_buy'] > $_POST['price_root']){
                $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                $this->data['view']='post';
                $this->data['error']='Giá bán sản phẩm phải nhỏ hơn giá gốc sản phẩm!';
                $this->data['category']= $this->Mcategory->all_category();
                return $this->load->view('frontend/layout',$this->data);
            }

            if(intval($_POST['number']) <= 0){
                $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                $this->data['view']='post';
                $this->data['error']='Số lượng sản phẩm phải lớn hơn 0!';
                $this->data['category']= $this->Mcategory->all_category();
                return $this->load->view('frontend/layout',$this->data);
            }

            $avatar = "";

            if(!empty($_FILES['avatar']['name'])){
                // Cấu hình cho thư viện Upload
                $config['upload_path']   = './public/images/products';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                
                $this->load->library('upload', $config);
                if($this->upload->do_upload('avatar')){
                    // File upload thành công
                    $data = $this->upload->data();
                    $avatar = $data['file_name'];
                }else{
                    $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                    $this->data['view']='post';
                    $this->data['error']='Có lỗi khi upload ảnh chính sản phẩm!';
                    $this->data['category']= $this->Mcategory->all_category();
                    return $this->load->view('frontend/layout',$this->data);
                }
            }else{
                $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                $this->data['view']='post';
                $this->data['error']='Vui lòng chọn ảnh chính của sản phẩm!';
                $this->data['category']= $this->Mcategory->all_category();
                return $this->load->view('frontend/layout',$this->data);
            }

            $images = "";

            if(!empty($_FILES['images']['name'])){
                $filesCount = count($_FILES['images']['name']);
                for($i = 0; $i < $filesCount; $i++){
                    $_FILES['file']['name']     = $_FILES['images']['name'][$i];
                    $_FILES['file']['type']     = $_FILES['images']['type'][$i];
                    $_FILES['file']['tmp_name'] = $_FILES['images']['tmp_name'][$i];
                    $_FILES['file']['error']    = $_FILES['images']['error'][$i];
                    $_FILES['file']['size']     = $_FILES['images']['size'][$i];

                    $config['upload_path']   = './public/images/products';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';

                    $this->load->library('upload', $config);

                    if($this->upload->do_upload('file')){
                        $data = $this->upload->data();
                        $images .= "#".$data['file_name'];
                    }else{
                        $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                        $this->data['view']='post';
                        $this->data['error']='Có lỗi khi upload ảnh phụ sản phẩm!';
                        $this->data['category']= $this->Mcategory->all_category();
                        return $this->load->view('frontend/layout',$this->data);
                    }
                }
            }else{
                $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                $this->data['view']='post';
                $this->data['error']='Vui lòng chọn ảnh phụ của sản phẩm!';
                $this->data['category']= $this->Mcategory->all_category();
                return $this->load->view('frontend/layout',$this->data);
            }

            

            $d=getdate();
            $today=$d['year']."/".$d['mon']."/".$d['mday']." ".$d['hours'].":".$d['minutes'].":".$d['seconds'];
            $sale = (($_POST['price_root'] - $_POST['price_buy']) / $_POST['price_root']) * 100;

            $mydata= array(
                'catid'=>$_POST['catid'],
                'name' =>$_POST['name'], 
                'alias' =>$string=$this->alias->str_alias($_POST['name']),
                'detail'=>$_POST['detail'], 
                'sortDesc'=>$_POST['sortDesc'], 
                'number'=>$_POST['number'],
                'sale'=> intval($sale),
                'price'=>$_POST['price_root'],
                'price_sale'=>$_POST['price_buy'],
                'created'=>$today,
                'modified'=>$today,
                'trash'=>1,
                'status'=>0,
                'avatar'=> $avatar,
                'img'=> substr($images, 1),
                'idcustomer' => $this->session->userdata('id'),
                'producer' => 5
            );

            if($this->Mproduct->Khachhang_post($mydata)){
                $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                $this->data['view']='post';
                $this->data['success']='Thành công! Sản phẩm đã được gửi lên quản trị để xét duyệt!';
                $this->data['category']= $this->Mcategory->all_category();
                return $this->load->view('frontend/layout',$this->data);
            }else{
                $this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
                $this->data['view']='post';
                $this->data['error']='Thêm sản phẩm không thành công, vui lòng kiểm tra lại!';
                $this->data['category']= $this->Mcategory->all_category();
                return $this->load->view('frontend/layout',$this->data);
            }
        }
		$this->data['title']='Smart Store - Đăng Bán Sản Phẩm Phụ Kiện Điện Tử, Laptop & Máy Tính!';
        $this->data['view']='post';
        $this->data['category']= $this->Mcategory->all_category();
        $this->load->view('frontend/layout',$this->data);
	}

}

/* End of file Khachhang.php */
/* Location: ./application/controllers/Khachhang.php */