<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Vnpay extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model('frontend/Morder');
        $this->load->model('frontend/Mproduct');
        $this->load->model('frontend/Morderdetail');
        $this->load->model('frontend/Mcustomer');
        $this->load->model('frontend/Mcategory');
        $this->load->model('frontend/Mconfig');
        $this->load->model('frontend/Mdistrict');
        $this->load->model('frontend/Mprovince');
        $this->load->helper('string');
        if(!$this->session->userdata('sessionKhachHang')){
            redirect('dang-nhap','refresh');
        }
        
	}

	public function index()
	{
		error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
		date_default_timezone_set('Asia/Ho_Chi_Minh');
		$vnp_TmnCode = "BZH5HZSG"; //Website ID in VNPAY System
		$vnp_HashSecret = "ZKGVBJNUIXYTLKAZFUFQKHHJTSMIYBNA"; //Secret key
		$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
		$vnp_Returnurl = base_url("/thanh-toan/ket-qua-vnpay/");
		$vnp_apiUrl = "http://sandbox.vnpayment.vn/merchant_webapi/merchant.html";

		$startTime = date("YmdHis");
		$expire = date('YmdHis',strtotime('+15 minutes',strtotime($startTime)));
		$vnp_TxnRef = time(); //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
		$vnp_OrderInfo = "thanh toan vnpay";
		$vnp_OrderType = "order";
		$vnp_Amount = ($this->input->post('amount'))*100;
		$vnp_Locale = 'vn';
		$vnp_BankCode = $this->input->post('bank_code');
		$hoten = $this->input->post('txt_ship_fullname');
		$sdt = $this->input->post('txt_ship_mobile');
		$diachi = $this->input->post('txt_ship_city');



		$newdata = array(
			'maKhachHang' => $this->session->userdata('sessionKhachHang')["id"],
	        'hoTen'  => $hoten,
	        'soDienThoai' => $sdt,
	        'tongTien' => $this->input->post('amount'),
	        'thanhPho' => $this->input->post('city'),
	        'quanHuyen' => $this->input->post('DistrictId'),
	        'diaChi' => $this->input->post('address'),
		);

		$this->session->set_userdata('orderVNPAY',$newdata);

		$vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

		$inputData = array(
		    "vnp_Version" => "2.1.0",
		    "vnp_TmnCode" => $vnp_TmnCode,
		    "vnp_Amount" => $vnp_Amount,
		    "vnp_Command" => "pay",
		    "vnp_CreateDate" => date('YmdHis'),
		    "vnp_CurrCode" => "VND",
		    "vnp_IpAddr" => $vnp_IpAddr,
		    "vnp_Locale" => $vnp_Locale,
		    "vnp_OrderInfo" => $vnp_OrderInfo,
		    "vnp_OrderType" => $vnp_OrderType,
		    "vnp_ReturnUrl" => $vnp_Returnurl,
		    "vnp_TxnRef" => $vnp_TxnRef,
		    "vnp_Bill_FirstName"=>$hoten,
		    "vnp_Bill_Mobile"=>$sdt,
		    "vnp_Bill_City"=>$diachi,
		);

		if (isset($vnp_BankCode) && $vnp_BankCode != "") {
		    $inputData['vnp_BankCode'] = $vnp_BankCode;
		}
		if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
		    $inputData['vnp_Bill_State'] = $vnp_Bill_State;
		}
		ksort($inputData);
		$query = "";
		$i = 0;
		$hashdata = "";
		foreach ($inputData as $key => $value) {
		    if ($i == 1) {
		        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
		    } else {
		        $hashdata .= urlencode($key) . "=" . urlencode($value);
		        $i = 1;
		    }
		    $query .= urlencode($key) . "=" . urlencode($value) . '&';
		}
		$vnp_Url = $vnp_Url . "?" . $query;
		if (isset($vnp_HashSecret)) {
		    $vnpSecureHash =   hash_hmac('sha512', $hashdata, $vnp_HashSecret);//  
		    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
		}
		$returnData = array('code' => '00'
		    , 'message' => 'success'
		    , 'data' => $vnp_Url);

		if (isset($_POST['redirect'])) {
	        header('Location: ' . $vnp_Url);
	        die();
	    } else {
	        echo json_encode($returnData);
	    }
	}


	function returnPay(){
		$_GET['vnp_TxnRef'];//ma don hang
		$_GET['vnp_Amount']; // so tien
		$_GET['vnp_OrderInfo'];// noi dung
		$_GET['vnp_BankCode'];// ngan hang
		$_GET['vnp_TransactionNo'];// Mã GD Tại VNPAY
		$_GET['vnp_PayDate'];// ngay tao
		$_GET['vnp_ResponseCode'];// ket qua

		if($_GET['vnp_ResponseCode'] == '00'){
			$data = array(
				'title' => 'Smart store - Kết quả thanh toán VNPAY',
				'view' => 'return-vnpay',
				'com' => 'thanhtoan',
				'madonhang' => $_GET['vnp_TxnRef'],
				'sotien' => $_GET['vnp_Amount']/100,
				'noidung' => $_GET['vnp_OrderInfo'],
				'nganhang' => $_GET['vnp_BankCode'],
				'magiaodichvnpay' => $_GET['vnp_TransactionNo'],
				'ngaytao' => $_GET['vnp_PayDate'],
				'ketqua' => $_GET['vnp_ResponseCode'],
			);

			$d=getdate();
        	$today=$d['year']."/".$d['mon']."/".$d['mday']." ".$d['hours'].":".$d['minutes'].":".$d['seconds'];

        	
            $mydata=array(
                'orderCode' => random_string('alnum', 8),
                'customerid' => $this->session->userdata('orderVNPAY')["maKhachHang"],
                'orderdate' => $today,
                'fullname' => $this->session->userdata('orderVNPAY')["hoTen"],
                'phone' => $this->session->userdata('orderVNPAY')["soDienThoai"],
                'address' => $this->session->userdata('orderVNPAY')["diaChi"],
                'money' => $this->session->userdata('orderVNPAY')["tongTien"],
                'price_ship' => 30000,
                'coupon' => 0,
                'province' => $this->session->userdata('orderVNPAY')["thanhPho"],
                'district' => $this->session->userdata('orderVNPAY')["quanHuyen"],
                'trash' => 1,
                'status' => 0
            );

            //Insert to db_order
            $this->Morder->order_insert($mydata);
            
            //Insert to db_orderdetail
            $order_detail = $this->Morder->order_detail_customerid($this->session->userdata('orderVNPAY')["maKhachHang"]);
            $orderid = $order_detail['id'];
            $data_insert=[];
            if($this->session->userdata('cart')){
                $val = $this->session->userdata('cart');
                foreach ($val as $key => $value){
                    $row = $this->Mproduct->product_detail_id($key);
                    if($row['price_sale'] > 0){
                        $price = $row['price_sale'];
                    }else{
                        $price = $row['price'];
                    }
                    $data_insert = array(
                        'orderid' => $orderid,
                        'productid' => $key,
                        'price' => $price,
                        'count' => $value,
                        'trash' => 1,
                        'status' => 1
                    );
                    $this->Morderdetail->orderdetail_insert($data_insert);
                }
            }
            $array_items = array('cart');

            $this->session->unset_userdata('orderVNPAY');
            $this->session->unset_userdata($array_items);

	        return $this->load->view('frontend/layout',$data);
		}else{
			$data = array(
				'title' => 'Smart store - Kết quả thanh toán VNPAY',
				'view' => 'return-vnpay',
				'com' => 'thanhtoan',
				'ketqua' => $_GET['vnp_ResponseCode']
			);
	        return $this->load->view('frontend/layout',$data);
		}
	}

}

/* End of file Vnpay.php */
/* Location: ./application/controllers/Vnpay.php */