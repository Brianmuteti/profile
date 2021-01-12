<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Profile extends CI_Controller {

	public function __construct(){
		parent::__construct();
		check_login_user();
		check_valid_user(); 
		is_valid();
	}

	public function index()
	{
		$data = array();
		$data['page_title'] = "Home";
        $data['page'] = "Profile";
        $data['s_home'] = false;
        $data['social_sites'] = $this->admin_m->select_with_status('social_sites');
        $data['home'] = $this->admin_m->get_home();
		$data['main_content'] = $this->load->view('admin/dashboard/profile/home', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function edit_home($id)
	{
		$data = array();
		$data['page_title'] = "Home";
        $data['page'] = "Profile";
        $data['s_home'] =$this->admin_m->single_select_by_id($id,'home');
        valid_user($data['s_home']['user_id']);

        $data['home'] = $this->admin_m->get_home(); 
        $data['social_sites'] = $this->admin_m->select('social_sites');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/home', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	
	/**
	  *** add home content
	**/ 
	public function add_home(){
		$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('about_me', 'About Me', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect(base_url('admin/profile/'));
			}else{	

				$data = array(
					'title' => $this->input->post('title',true),
					'user_id' => auth('id'),
					'about_me' => $this->input->post('about_me'),
					'designations' => $this->input->post('designations',true),
					'created_at' => d_time(),
				);
				$id = $this->input->post('id');
				if($id==0){
					$insert = $this->admin_m->insert($data,'home');
				}else{
					$insert = $this->admin_m->update($data,$id,'home');
				}

				if($insert){
					if(isset($_POST['social_sites'])):
						foreach ($_POST['social_sites'] as $key => $site) {
							$user_site = get_user_social_sites($site);
							$data = array(
								'site_id' => $site,
								'site_value' => $_POST[$site],
								'user_id' => auth('id'),
							);
							if($user_site['site_id']==$site){
								$this->admin_m->update($data,$user_site['id'],'user_social_sites');
							}else{
								$this->admin_m->insert($data,'user_social_sites');
							}
						}
					endif;
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/'));
				}	
		}
	}




/**
   ** default Image Upload for services and reviews
 **/ 
	public function upload_images($id=0,$table)
	{	
		if(!empty($_FILES['images']['name'])){
			if($table=='services'){
				$directory = 'uploads/services/';
			}elseif($table=='reviews'){
				$directory = 'uploads/reviews/';
			}elseif($table=='products'){
				$directory = 'uploads/products/';
			}else{
				$directory = 'uploads/';
			}
			$dir = $directory;
			$name = $_FILES['images']['name'];
			list($txt, $ext) = explode(".", $name);
			$image_name = md5(time()).".".$ext;
			$tmp = $_FILES['images']['tmp_name'];
		   if(move_uploaded_file($tmp, $dir.$image_name)){
			    $url = $dir.$image_name;
			    $data = array('images' => $url);
				$this->admin_m->update($data,$id,$table);	
		   }else{
		      echo "image uploading failed";
		   }
		}

	}

	/**
	  ** user skill 
	**/

	public function skills()
	{
		$data = array();
		$data['page_title'] = "Skills";
        $data['page'] = "Profile";
        $data['s_skill'] = false;
        $data['skills'] = $this->admin_m->select_all_by_user('skills');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/skills', $data, TRUE);
		$this->load->view('admin/index',$data);
	}


	/**
	  ** edit user skill
	**/

	public function edit_skills($id)
	{
		$data = array();
		$data['page_title'] = "Skills";
        $data['page'] = "Profile";
        $data['s_skill'] =$this->admin_m->single_select_by_id($id,'skills');
        $data['skills'] = $this->admin_m->select_all_by_user('skills');
        valid_user($data['s_skill']['user_id']);
		$data['main_content'] = $this->load->view('admin/dashboard/profile/skills', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** add_skills
	**/ 
	public function add_skills(){
		$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('level', 'level', 'trim|required|integer|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->skills();
			}else{	
				$data = array(
					'title' => $this->input->post('title',true),
					'user_id' => auth('id'),
					'level' => $this->input->post('level',true),
					'created_at' => d_time(),
				);
				$id = $this->input->post('id');
				if($id==0){
					$insert = $this->admin_m->insert($data,'skills');
				}else{
					$insert = $this->admin_m->update($data,$id,'skills');
				}

				if($insert){
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/skills'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/skills'));
				}	
		}
	}

	/**
	  ** user services
	**/

	public function services()
	{
		$data = array();
		$data['page_title'] = "Services";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['services'] = $this->admin_m->select_all_by_user('services');
		$data['main_content'] = $this->load->view('admin/dashboard/about/services', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  ** edit user services
	**/

	public function edit_services($id)
	{
		$data = array();
		$data['page_title'] = "Services";
        $data['page'] = "Profile";
        $data['data'] =$this->admin_m->single_select_by_id($id,'services');
        $data['services'] = $this->admin_m->select_all_by_user('services');
        valid_user($data['data']['user_id']);
		$data['main_content'] = $this->load->view('admin/dashboard/about/services', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** add services
	**/ 
	public function add_services(){
		$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('details', 'Details', 'required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->services();
			}else{	
				$data = array(
					'title' => $this->input->post('title',true),
					'user_id' => auth('id'),
					'icon' => $this->input->post('icon',true),
					'details' => $this->input->post('details'),
					'created_at' => d_time(),
				);
				$id = $this->input->post('id');
				if($id==0){
					$insert = $this->admin_m->insert($data,'services');
				}else{
					$insert = $this->admin_m->update($data,$id,'services');
				}

				if($insert){
					$this->upload_images($insert,'services');
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/services'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/services'));
				}	
		}
	}

	/**
	  ** user about 
	**/
	public function about()
	{
		$data = array();
		$data['page_title'] = "About Me";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['home'] = $this->admin_m->get_about(0);
        $data['my_info'] = $this->admin_m->single_select_by_user(auth('id'),'home');
		$data['main_content'] = $this->load->view('admin/dashboard/about/home', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  ** edit about
	**/

	public function edit_about($id)
	{
		$data = array();
		$data['page_title'] = "About Me";
        $data['page'] = "Profile";
        $data['home'] = $this->admin_m->get_about(0);
        $data['data'] =$this->admin_m->get_about($id);
        $data['my_info'] = $this->admin_m->single_select_by_user(auth('id'),'home');
        if(!empty($data['data'])):
	        valid_user($data['data'][0]['user_id']);
	    else:
	    	redirect(base_url('dashboard'));
	    endif;
		$data['main_content'] = $this->load->view('admin/dashboard/about/home', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** add about
	**/ 
	public function add_about(){
		$this->form_validation->set_rules('full_name', 'Label', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->about();
			}else{
				$data = array(
					'user_id' => auth('id'),
					'full_name' =>  $this->input->post('full_name',true),
					'nationality' =>  $this->input->post('nationality',true),
					'dob' => $this->input->post('dob',true),
					'created_at' => d_time(),
				);
				$id = $this->input->post('id',true);
				if($id==0){
					$insert = $this->admin_m->insert($data,'about');
				}else{
					$insert = $this->admin_m->update($data,$id,'about');
				}

				if($insert){
					$label = $this->input->post('label',true);
					$i=0;	
						if(!empty($label)){
							foreach ($label as $key => $value) {
								$data = array(
									'about_id' => $insert,
									'label' => $value,
									'value' => $this->input->post('value',true)[$i],
								);
								$i++;
								$this->admin_m->insert($data,'about_content');
							}
						}

					// update old fields value
					$label_ex = $this->input->post('label_ex',true);
					$j=0;	
						if(!empty($label_ex)){
							foreach ($label_ex as $key => $value_ex) {
								$data_ex = array(
									'about_id' => $insert,
									'label' => $value_ex,
									'value' => $this->input->post('value_ex',true)[$j],
								);
								$this->admin_m->update($data_ex,$this->input->post('ex_id')[$j],'about_content');
								$j++;
							}
						}
					$this->upload_images($insert,'about');
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/about'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/about'));
				}	
		}
	}


	/**
	  ** user review
	**/

	public function reviews()
	{
		$data = array();
		$data['page_title'] = "Reviews";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['reviews'] = $this->admin_m->select_all_by_user('reviews');
		$data['main_content'] = $this->load->view('admin/dashboard/about/reviews', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function edit_reviews($id)
	{
		$data = array();
		$data['page_title'] = "Reviews";
        $data['page'] = "Profile";
        $data['data'] =$this->admin_m->single_select_by_id($id,'reviews');
        $data['reviews'] = $this->admin_m->select_all_by_user('reviews');
        valid_user($data['data']['user_id']);
		$data['main_content'] = $this->load->view('admin/dashboard/about/reviews', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** add_reviews
	**/ 
	public function add_reviews(){
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('designation', 'Designation', 'trim|required|xss_clean');
		$this->form_validation->set_rules('comments', 'Comments', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->reviews();
		}else{	
			$data = array(
				'name' => $this->input->post('name',true),
				'user_id' => auth('id'),
				'designation' => $this->input->post('designation',true),
				'comments' => $this->input->post('comments'),
				'created_at' => d_time(),
			);
			$id = $this->input->post('id');
			if($id==0){
				$insert = $this->admin_m->insert($data,'reviews');
			}else{
				$insert = $this->admin_m->update($data,$id,'reviews');
			}

			if($insert){
				$this->upload_images($insert,'reviews');
				$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
				redirect(base_url('admin/profile/reviews'));
			}else{
				$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
				redirect(base_url('admin/profile/reviews'));
			}	
		}
	}


	/**
	  ** user resume
	**/

	public function resume()
	{
		$data = array();
		$data['page_title'] = "Resume";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['type_data'] = false;
        $data['resume_type'] = $this->admin_m->select_all_by_user('resume_type');
        $data['resume'] = $this->admin_m->get_resume_by_user();
		$data['main_content'] = $this->load->view('admin/dashboard/profile/resume', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	
	/**
	  ** edit resume
	**/
	public function edit_resume($id)
	{
		$data = array();
		$data['page_title'] = "Resume";
        $data['page'] = "Profile";
        $data['type_data'] =false;
        $data['resume_type'] = $this->admin_m->select_all_by_user('resume_type');
        $data['data'] =$this->admin_m->single_select_by_id($id,'resume');
        $data['resume'] = $this->admin_m->get_resume_by_user();

        valid_user($data['data']['user_id']);

		$data['main_content'] = $this->load->view('admin/dashboard/profile/resume', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  ** edit resume type
	**/

	public function edit_resume_type($id)
	{
		$data = array();
		$data['page_title'] = "Resume";
        $data['page'] = "Profile";
        $data['type_data'] =$this->admin_m->single_select_by_id($id,'resume_type');
        $data['resume_type'] = $this->admin_m->select_all_by_user('resume_type');
        $data['data'] =false;
        $data['resume'] = $this->admin_m->get_resume_by_user();

        valid_user($data['type_data']['user_id']);
		$data['main_content'] = $this->load->view('admin/dashboard/profile/resume', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** add resume
	**/ 
	public function add_resume(){
		$this->form_validation->set_rules('resume_type', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('title', 'Title', 'trim|required|xss_clean');
		$this->form_validation->set_rules('designation', 'Designation', 'trim|required|xss_clean');
		$this->form_validation->set_rules('details', 'Details', 'trim|required|xss_clean');
		$this->form_validation->set_rules('start_year', 'Start Year', 'trim|required|xss_clean|exact_length[4]');
		$this->form_validation->set_rules('end_year', 'End Year', 'trim|xss_clean|exact_length[4]');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->resume();
		}else{	
			$data = array(
				'title' => $this->input->post('title',true),
				'type_id' => $this->input->post('resume_type',true),
				'user_id' => auth('id'),
				'designation' => $this->input->post('designation',true),
				'details' => $this->input->post('details',true),
				'start_year' => $this->input->post('start_year',true),
				'end_year' => $this->input->post('end_year',true),
				'created_at' => d_time(),
			);
			$id = $this->input->post('id');
			if($id==0){
				$insert = $this->admin_m->insert($data,'resume');
			}else{
				$insert = $this->admin_m->update($data,$id,'resume');
			}

			if($insert){
				$this->upload_images($insert,'reviews');
				$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
				redirect(base_url('admin/profile/resume'));
			}else{
				$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
				redirect(base_url('admin/profile/resume'));
			}	
		}
	}

	/**
	  *** add_resume_type
	**/ 
	public function add_resume_type(){
		$this->form_validation->set_rules('type_name', 'Name', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->resume();
		}else{	
			$data = array(
				'type_name' => $this->input->post('type_name',true),
				'icon' => $this->input->post('icon',true),
				'user_id' => auth('id'),
				'created_at' => d_time(),
			);
			$id = $this->input->post('id');
			if($id==0){
				$insert = $this->admin_m->insert($data,'resume_type');
			}else{
				$insert = $this->admin_m->update($data,$id,'resume_type');
			}

			if($insert){
				$this->upload_images($insert,'reviews');
				$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
				redirect(base_url('admin/profile/resume'));
			}else{
				$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
				redirect(base_url('admin/profile/resume'));
			}	
		}
	}

	 /**
	  ** Billing
	**/ 
	public function billing()
	{
		$data = array();
		$data['page_title'] = "Billing";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['custs'] = $this->admin_m->select_all_by_user('customers');
        $data['pdts'] = $this->admin_m->select_all_by_user('invoices');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/billing', $data, TRUE);
		$this->load->view('admin/index',$data);
	}
	 /**
	  ** Order
	**/ 
	public function order()
	{
		$data = array();
		$data['page_title'] = "Invoicing";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['custs'] = $this->admin_m->select_all_by_user('customers');
        $data['pdts'] = $this->admin_m->select_all_by_user('products');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/order', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function fetch_products() {
         echo $this->admin_m->fetch_products('products');
    }

	public function fetch_price() {
        if ($this->input->post("product_id")) {
            $id = $this->input->post("product_id");
            $product = $this->admin_m->fetch_price($id);
            echo json_encode($product);
        }
    }

    /**
	  *** add order
	**/ 
	public function add_order(){
	   $amount=array_sum($this->input->post('totalcost')); 
	   // echo json_encode($this->input->post());exit;
		$data = array(
			// 'customer' => $this->input->post('customer',true),
			'user_id' => auth('id'),
			'username' =>$this->input->post('customer',TRUE),
            // 'email' =>$this->input->post('email',TRUE),
            // 'phone' =>$this->input->post('phone',TRUE),
            // 'address' =>$this->input->post('address',TRUE),
            // 'payref' =>$this->input->post('reference',TRUE),
            'paymethod' =>$this->input->post('payment_method',TRUE),
            'total'=>$amount,
            'status' =>1,
		);
		$id = $this->input->post('id');
		if($id==0){
			$insert = $this->admin_m->insert($data,'orders');
		}else{
			$insert = $this->admin_m->update($data,$id,'orders');
		}

		if($insert){
			$order = $this->admin_m->fetch_order_id($this->input->post('customer'));
			$quantitys = $this->input->post("quantity");
            $products = $this->input->post("product");
            $unitcosts = $this->input->post("unitcost");
            $subcosts = $this->input->post("totalcost");
            $ord_no = $order;
            foreach ($products as $index => $product) {
                $quantity = $quantitys[$index];
                $unitcost = $unitcosts[$index];
                $subcost = $subcosts[$index];
                $row = array(
                 'qty' => $quantity,
                 'product_id' => $product, 
                 'prce' => $unitcost, 
                 'subtotal' => $subcost,
                 'order_id' => $order,
             );
                $result = $this->admin_m->add_order_details($row);
            }
			$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
			redirect(base_url('admin/profile/billing'));
		}else{
			$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
			redirect(base_url('admin/profile/invoice'));
		}	
	}
	 /**
	  ** Billing
	**/ 
	public function invoice($id)
	{
		$data = array(); 
		$data['page_title'] = "Invoicing";
        $data['page'] = "Profile";
        $data['data'] = false;
        $invno = $this->admin_m->select_all_by_user(auth('id'));
        $data['order'] = $this->admin_m->select_order_by_id($id);
        $data['orderinfo'] =$this->admin_m->single_select_by_id($id,'orders');
        $data['orderitems'] =$this->admin_m->select_items_by_order($id,'order_items');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/invoice', $data, TRUE);
		$this->load->view('admin/index',$data);
	}
	/**
	  *** add invoice
	**/ 
	public function add_invoice(){
		$this->form_validation->set_rules('customer', 'Customer', 'trim|required|xss_clean');
		$this->form_validation->set_rules('invoice_no', 'Invoice No', 'trim|required|xss_clean');
		$this->form_validation->set_rules('invoice_dated', 'Invoice Date', 'trim|required|xss_clean');
		$this->form_validation->set_rules('buyer_order_no', 'Buyer Order No', 'trim|required|xss_clean');
		$this->form_validation->set_rules('buyer_dated', 'Order Date', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->customers();
			}
		else{
			$data = array(
				'customer' => $this->input->post('customer',true),
				'user_id' => auth('id'),
				'invoice_no' => $this->input->post('invoice_no',true),
				'delivery_note' => $this->input->post('delivery_note',true),
				'mode_pay' => $this->input->post('mode_pay',true),
				'supplier_ref' => $this->input->post('supplier_ref',true),
				'other_ref' => $this->input->post('other_ref',true),
				'buyer_order_no' => $this->input->post('buyer_order_no',true),
				'invoice_dated' => $this->input->post('invoice_dated',true),
				'buyer_dated' => $this->input->post('buyer_dated',true),
				'dispatch_no' => $this->input->post('dispatch_no',true),
				'delivery_date' => $this->input->post('delivery_date',true),
				'dispatch_through' => $this->input->post('dispatch_through',true),
				'destination' => $this->input->post('destination',true),
				'terms_delivery' => $this->input->post('terms_delivery',true),
			);
			$id = $this->input->post('id');
			if($id==0){
				$insert = $this->admin_m->insert($data,'invoices');
			}else{
				$insert = $this->admin_m->update($data,$id,'invoices');
			}

			if($insert){
				$this->upload_images($insert,'customers');
				$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
				redirect(base_url('admin/profile/billing'));
			}else{
				$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
				redirect(base_url('admin/profile/invoice'));
			}	
		}
	}

	/**
	  ** Customer
	**/ 
	public function customers()
	{
		$data = array();
		$data['page_title'] = "Customers";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['custs'] = $this->admin_m->select_all_by_user('customers');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/customers', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** add customer
	**/ 
	public function add_customer(){
		$this->form_validation->set_rules('customer_name', 'Customer Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('state', 'State', 'trim|required|xss_clean');
		$this->form_validation->set_rules('city', 'City', 'trim|required|xss_clean');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|xss_clean');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean');
		$this->form_validation->set_rules('city_code', 'City Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('street', 'Street', 'trim|required|xss_clean');
		$this->form_validation->set_rules('gstin', 'GSTIN', 'trim|required|xss_clean');
		$this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->customers();
			}
		else{

			$st = $this->input->post('state');
            $result_explode = explode('|', $st);
            $state=$result_explode[0];
            $state_code= $result_explode[1];
			$data = array(
				'cust_name' => $this->input->post('customer_name',true),
				'user_id' => auth('id'),
				'state' => $state,
				'state_code' => $state_code,
				'phone' => $this->input->post('phone',true),
				'email' => $this->input->post('email',true),
				'city' => $this->input->post('city',true),
				'city_code' => $this->input->post('city_code',true),
				'street' => $this->input->post('street',true),
				'gstin' => $this->input->post('gstin',true),
				'address' => $this->input->post('address',true),
			);
			$id = $this->input->post('id');
			if($id==0){
				$insert = $this->admin_m->insert($data,'customers');
			}else{
				$insert = $this->admin_m->update($data,$id,'customers');
			}

			if($insert){
				$this->upload_images($insert,'customers');
				$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
				redirect(base_url('admin/profile/customers'));
			}else{
				$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
				redirect(base_url('admin/profile/customers'));
			}	
		}
	}
		/**
	  ** edit product
	**/ 
	public function edit_customer($id)
	{
		$data = array();
		$data['page_title'] = "Customers";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['cust'] =$this->admin_m->single_select_by_id($id,'customers');
        $data['custs'] = $this->admin_m->select_all_by_user('customers');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/customers', $data, TRUE);
		$this->load->view('admin/index',$data);
	}
    /**
	  ** user Payment Options
	**/ 
	public function ecommerce()
	{
		$data = array();
		$data['page_title'] = "Ecommerce";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['pdts'] = $this->admin_m->select_all_by_user('products');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/ecommerce', $data, TRUE);
		$this->load->view('admin/index',$data);
	}
	/**
	  *** add product
	**/ 
	public function add_product(){
		$this->form_validation->set_rules('product_name', 'Product Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('quantity', 'Product quantity', 'trim|required|integer|xss_clean');
		$this->form_validation->set_rules('description', 'Product Description', 'trim|required|xss_clean');
		$this->form_validation->set_rules('price', 'Product Price', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->ecommerce();
			}
		else{
			$data = array(
				'product_name' => $this->input->post('product_name',true),
				'user_id' => auth('id'),
				'quantity' => $this->input->post('quantity',true),
				'description' => $this->input->post('description',true),
				'price' => $this->input->post('price',true),
			);
			$id = $this->input->post('id');
			if($id==0){
				$insert = $this->admin_m->insert($data,'products');
			}else{
				$insert = $this->admin_m->update($data,$id,'products');
			}

			if($insert){
				$this->upload_images($insert,'products');
				$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
				redirect(base_url('admin/profile/ecommerce'));
			}else{
				$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
				redirect(base_url('admin/profile/ecommerce'));
			}	
		}
	}
	/**
	  ** edit product
	**/ 
	public function edit_product($id)
	{
		$data = array();
		$data['page_title'] = "Ecommerce";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['pdt'] =$this->admin_m->single_select_by_id($id,'products');
        $data['pdtimgs'] =$this->admin_m->select_all_by_product($id,'product_images');
        // echo json_encode($pdtimgs);exit;
        $data['pdts'] = $this->admin_m->select_all_by_user('products');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/ecommerce', $data, TRUE);
		$this->load->view('admin/index',$data);
	}
	/**
	  ** Upload product Images
	**/ 
	public function add_product_images(){
		if ($_FILES['productimages']['name']!= '') {
	        $data = array();
	        $maxsize=100;
	        $count_images = count($_FILES['productimages']['name']);
	        for($i=0;$i<$count_images;$i++){
	          if(!empty($_FILES['productimages']['name'][$i])){
	            $_FILES['file']['name'] = $_FILES['productimages']['name'][$i];
	            $_FILES['file']['type'] = $_FILES['productimages']['type'][$i];
	            $_FILES['file']['tmp_name'] = $_FILES['productimages']['tmp_name'][$i];
	            $_FILES['file']['error'] = $_FILES['productimages']['error'][$i];
	            $_FILES['file']['size'] = $_FILES['productimages']['size'][$i];
	            $uploadPath = 'uploads/products/';
	            $config['upload_path'] = $uploadPath;
	            $config['allowed_types'] = 'jpg|jpeg|png|gif';
	            $config['max_size']     = $maxsize;
	            $config['width'] = 300;
	        	$config['height'] = 300;
	            $config['file_name'] = $_FILES['productimages']['name'][$i];
	            $this->load->library('upload', $config); 
	            $this->upload->initialize($config); 
	            $this->load->library('image_lib');
	            $this->image_lib->resize();
	            if($this->upload->do_upload('file')){
	              $idd=$this->input->post('id');
	              $uploadData = $this->upload->data();
	              $arrData["productimage"] =$uploadPath.$_FILES['productimages']['name'][$i];
	              $arrData["product_id"] =$this->input->post('id');
	              $arrData["user_id"] =auth('id');
	              // $result=$this->products_model->upload_files($arrData);
	              $insert=$this->admin_m->insert($arrData,'product_images');   
	            }
	            else {
	            	  $idd=$this->input->post('id');
	                  $errors=$this->upload->display_errors();
	                  $msg=$errors;
	                  $this->session->set_flashdata('error', 'Something was Wrong!!'.$msg);
					  redirect(base_url('admin/profile/edit_product/'.$idd));
	                }
	            }
	          }
	          if($insert){
	          	$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
				redirect(base_url('admin/profile/edit_product/'.$idd));
	          }
	      }
	      else{
	      	$idd=$this->input->post('id');
	         $this->session->set_flashdata('error', 'No file selected!!');
			 redirect(base_url('admin/profile/edit_product/'.$idd));
	      }
	}
	/**
	  ** user Payment Options
	**/ 
	public function paymentoptions()
	{
		$data = array();
		$data['page_title'] = "Payment Options";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['payinfo'] = $this->admin_m->select_all_by_user('payment_options');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/payment-options', $data, TRUE);
		$this->load->view('admin/index',$data);
	}
	/**
	  *** add payment options
	**/ 
	public function add_payment_options(){
		$this->form_validation->set_rules('payment_method', 'Payment Method', 'trim|required|xss_clean');
		$this->form_validation->set_rules('pay_number', 'Payment Method Number', 'trim|required|xss_clean');
		$this->form_validation->set_rules('pay_link', 'Payment Link', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->paymentoptions();
			}else{	
				$data = array(
					'payment_method' => $this->input->post('payment_method',true),
					'user_id' => auth('id'),
					'pay_number' => $this->input->post('pay_number',true),
					'pay_link' => $this->input->post('pay_link',true),
				);
				$id = $this->input->post('id');
				if($id==0){
					$insert = $this->admin_m->insert($data,'payment_options');
				}else{
					$insert = $this->admin_m->update($data,$id,'payment_options');
				}

				if($insert){
					$this->upload_images($insert,'payment_options');
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/paymentoptions'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/paymentoptions'));
				}	
			}
	}
	/**
	  ** edit payment options
	**/ 
	public function edit_paymentoptions($id)
	{
		$data = array();
		$data['page_title'] = "Payment Options";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['pay_info'] =$this->admin_m->single_select_by_id($id,'payment_options');
        $data['payinfo'] = $this->admin_m->select_all_by_user('payment_options');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/payment-options', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function orders()
	{
		$data = array();
		$data['page_title'] = "Orders";
        $data['page'] = "Orders";
        $data['data'] = false;
        $data['orders'] = $this->admin_m->select_all_by_user('orders');
		$data['main_content'] = $this->load->view('admin/dashboard/orders/orders', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function orderview($id)
	{
		$data = array();
		$data['page_title'] = "Orders";
        $data['page'] = "Orders";
        $data['data'] = false;
        $data['orderinfo'] =$this->admin_m->single_select_by_id($id,'orders');
        $data['orderitems'] =$this->admin_m->select_items_by_order($id,'order_items');
		$data['main_content'] = $this->load->view('admin/dashboard/orders/view', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function orderstatus(){
		$id=$this->input->post('id',true);	
		$data = array(
			'status' => $this->input->post('status',true),
		);
		 $insert = $this->admin_m->update($data,$id,'orders');
		if($insert){
			$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
			redirect($_SERVER['HTTP_REFERER']);
		}	
	}
	
	public function bankdetails()
	{
		$data = array();
		$data['page_title'] = "Bank Details";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['info'] = $this->admin_m->select_all_by_user('bank_details');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/bank-details', $data, TRUE);
		$this->load->view('admin/index',$data);
	}
	/**
	  ** edit bank details
	**/ 
	public function edit_bankdetails($id)
	{
		$data = array();
		$data['page_title'] = "Bank Details";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['b_info'] =$this->admin_m->single_select_by_id($id,'bank_details');
        $data['info'] = $this->admin_m->select_all_by_user('bank_details');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/bank-details', $data, TRUE);
		$this->load->view('admin/index',$data);
	}
		/**
	  *** add_bank details
	**/ 
	public function add_bank_details(){
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('ifsc_code', 'IFSC Code', 'trim|required|xss_clean');
		$this->form_validation->set_rules('holder_name', 'Account Holder Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('account_number', 'Account Number', 'trim|required|xss_clean');
		$this->form_validation->set_rules('account_type', 'Account Type', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->bankdetails();
			}else{	
				$data = array(
					'bank_name' => $this->input->post('bank_name',true),
					'user_id' => auth('id'),
					'ifsc_code' => $this->input->post('ifsc_code',true),
					'holder_name' => $this->input->post('holder_name',true),
					'account_number' => $this->input->post('account_number',true),
					'account_type' => $this->input->post('account_type',true),
				);
				$id = $this->input->post('id');
				if($id==0){
					$insert = $this->admin_m->insert($data,'bank_details');
				}else{
					$insert = $this->admin_m->update($data,$id,'bank_details');
				}

				if($insert){
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/bankdetails'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/bankdetails'));
				}	
		}
	}


	/**
	  ** user portfolio
	**/



	public function portfolio()
	{
		$data = array();
		$data['page_title'] = "Portfolio";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['portfolio_type'] = $this->admin_m->select_all_by_user('portfolio_type');
        $data['portfolio'] = $this->admin_m->select_all_by_user('portfolio');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/portfolio', $data, TRUE);
		$this->load->view('admin/index',$data);
	}


	/**
	  ** edit portfolio
	**/

	public function edit_portfolio_type($id)
	{
		$data = array();
		$data['page_title'] = "Portfolio";
        $data['page'] = "Profile";
        $data['data'] =$this->admin_m->single_select_by_id($id,'portfolio_type');
        $data['portfolio_type'] = $this->admin_m->select_all_by_user('portfolio_type');
        $data['portfolio'] = $this->admin_m->select_all_by_user('portfolio');
        valid_user($data['data']['user_id']);
		$data['main_content'] = $this->load->view('admin/dashboard/profile/portfolio', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	
	/**
	  *** add_portfolio_type
	**/ 
	public function add_portfolio_type(){
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->portfolio();
		}else{	
			$data = array(
				'name' => $this->input->post('name',true),
				'user_id' => auth('id'),
				'created_at' => d_time(),
			);
			$id = $this->input->post('id');
			if($id==0){
				$insert = $this->admin_m->insert($data,'portfolio_type');
			}else{
				$insert = $this->admin_m->update($data,$id,'portfolio_type');
			}

			if($insert){
				$this->upload_images($insert,'reviews');
				$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
				redirect(base_url('admin/profile/portfolio'));
			}else{
				$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
				redirect(base_url('admin/profile/portfolio'));
			}	
		}
	}

	/**
	  ***  add portfolio
	**/ 
	public function add_portfolio()
	{
	    $data = array();
	    if (!empty($_FILES['file']['name'])) {
	        $filesCount = count($_FILES['file']['name']);

	        $total = count($this->admin_m->select_all_by_user('portfolio'));
        	$limit = limit(auth('id'));
        	if($limit !=0){
        		$total_up = $total+$filesCount;
        		if($total_up > $limit){
        			$check =0;
        			$this->session->set_flashdata('error', 'Sorry You Uploaded maximum portfolios');
        			echo json_encode(array('st'=>0,'msg'=>'Sorry You Uploaded maximum portfolios',));
        			exit();
        		}else if($total > $limit){
        			$check =0;
        			echo json_encode(array('st'=>0,'msg'=>'Sorry You reached the maximum limit',));
        			$this->session->set_flashdata('error', 'Sorry You reached the maximum limit');
        			exit();
        		}else{
        			$check =1;
        		}
        		
        	}else{
        		$check = 1;
        	}



        if($check ==1):
	        for ($i = 0; $i < $filesCount; $i++) {
	              $_FILES['uploadFile']['name'] = str_replace(",","_",$_FILES['file']['name'][$i]);
	              $_FILES['uploadFile']['type'] = $_FILES['file']['type'][$i];
	              $_FILES['uploadFile']['tmp_name'] = $_FILES['file']['tmp_name'][$i];
	              $_FILES['uploadFile']['error'] = $_FILES['file']['error'][$i];
	              $_FILES['uploadFile']['size'] = $_FILES['file']['size'][$i];

	              //Directory where files will be uploaded
	              $uploadPath = 'uploads/portfolio/';


	              $config['upload_path'] = $uploadPath;
	              // Specifying the file formats that are supported.
	              $config['allowed_types'] = 'jpg|jpeg|png|gif';
	              $config['overwrite'] = TRUE;
				  $config['encrypt_name'] = TRUE;
	              $this->load->library('upload', $config);
	              $this->upload->initialize($config);
	              // resize library
	              $this->load->library('image_lib');

	              if ($this->upload->do_upload('uploadFile')) {
	                  $fileData = $this->upload->data();
	                  $uploadData[$i]['file_name'] = $fileData['file_name'];
	                  // resize
			            $config = array(
						    'source_image'      => $fileData['full_path'], 
						    'new_image'         => $uploadPath.'/thumb', //path to
						    'maintain_ratio'    => true,
						    'width'             => 600,
						    'height'            => 600
						);
						    $this->image_lib->initialize($config);
						    $this->image_lib->resize();
						// resize
						    
	              }else{
	              	$error = array('error' => $this->upload->display_errors());
                    foreach ($error as $value) {
                    	$msg = $value;
                    }
                    echo json_encode(array('st'=>0,'msg'=>$msg,));
                    exit();
	              }

	        }
	          
	        if (!empty($uploadData)) {
	          $list=array();
	            $j=0;foreach ($uploadData as $value) {
		          	$data = array(
		          		'type_id' => $_POST['type_id'],
		          		'link' => $this->input->post('link')[$j],
		          		'title' => $_POST['title'][$j],
		          		'user_id' => auth('id'),
		          		'image' => $uploadPath.$value['file_name'],
		          		'thumb' => $uploadPath.'thumb/'.$value['file_name'],
		          		'created_at' => d_time(),
		          	);
		          	//insert image into database query
		          	$this->admin_m->insert($data,'portfolio');
		         $j++; 	
	          	}
	    		echo json_encode(array('st'=>1,));
		  	}else{
		    	$msg = 'Please insert an image';
		    	echo json_encode(array('st'=>0,'msg'=>$msg,));
		    }

		endif;

	    }
	}




/**
   ** user image uploader
 **/ 
	public function upload_profile_image()
	{
	    $data = array();
	    if (!empty($_FILES['file']['name'])) {
	        $filesCount = count($_FILES['file']['name']);
	        for ($i = 0; $i < $filesCount; $i++) {
	              $_FILES['uploadFile']['name'] = str_replace(",","_",$_FILES['file']['name'][$i]);
	              $_FILES['uploadFile']['type'] = $_FILES['file']['type'][$i];
	              $_FILES['uploadFile']['tmp_name'] = $_FILES['file']['tmp_name'][$i];
	              $_FILES['uploadFile']['error'] = $_FILES['file']['error'][$i];
	              $_FILES['uploadFile']['size'] = $_FILES['file']['size'][$i];

	              //Directory where files will be uploaded
	              $uploadPath = 'uploads/profile/';


	              $config['upload_path'] = $uploadPath;
	              // Specifying the file formats that are supported.
	              $config['allowed_types'] = 'jpg|jpeg|png';
	              $config['overwrite'] = TRUE;
				  $config['encrypt_name'] = TRUE;
	              $this->load->library('upload', $config);
	              $this->upload->initialize($config);
	              // resize library
	              $this->load->library('image_lib');

	              if ($this->upload->do_upload('uploadFile')) {
	                  $fileData = $this->upload->data();
	                  $uploadData[$i]['file_name'] = $fileData['file_name'];
	                  // resize
			            $config = array(
						    'source_image'      => $fileData['full_path'], 
						    'new_image'         => $uploadPath.'/thumb', //path to
						    'maintain_ratio'    => true,
						    'width'             => 250,
						    'height'            => 250
						);
						    $this->image_lib->initialize($config);
						    $this->image_lib->resize();
						// resize
						    
	              }else{
	              	$error = array('error' => $this->upload->display_errors());
                    foreach ($error as $value) {
                    	$msg = $value;
                    }
                    echo json_encode(array('st'=>0,'msg'=>$msg,));
                    exit();
	              }

	        }
	          
	        if (!empty($uploadData)) {
	          $list=array();
	          	foreach ($uploadData as $value) {
		          	$data = array(
		          		'image' => $uploadPath.$value['file_name'],
		          		'thumb' => $uploadPath.'thumb/'.$value['file_name'],
		          	);
		          	//insert image into database query
		          	$this->admin_m->update(array('thumb' => $uploadPath.'thumb/'.$value['file_name']),auth('id'),'users');
	          	}
	    		echo json_encode(array('st'=>1,'img'=>$uploadPath.'thumb/'.$value['file_name']));
		  	}else{
		    	$msg = 'Please insert an image';
		    	echo json_encode(array('st'=>0,'msg'=>$msg,));
		    }

	    }
	}



	/**
	  ** user settings
	**/

	public function settings()
	{
		$data = array();
		$data['page_title'] = "Settings";
        $data['page'] = "Settings";
        $data['data'] = false;
        $data['setting'] = $this->admin_m->single_select_by_user(auth('id'),'user_settings');
		$data['main_content'] = $this->load->view('admin/dashboard/activities/settings', $data, TRUE);
		$this->load->view('admin/index',$data);
	}
	
	/**
	  *** add themes
	**/ 
	public function add_themes(){
		$this->form_validation->set_rules('animation', 'Animation', 'trim|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->settings();
			}else{	
				$data = array(
					'user_id' => auth('id'),
					'is_download' => !empty($this->input->post('is_download',true))?$this->input->post('is_download',true):0,
					'animation' => !empty($this->input->post('animation',true))?$this->input->post('animation',true):0,
					'is_facebook' => !empty($this->input->post('is_facebook',true))?$this->input->post('is_facebook',true):0,
					'created_at' => d_time(),
				);
				$id = $this->input->post('id');
				if($id==0){
					$insert = $this->admin_m->insert($data,'user_settings');
				}else{
					$insert = $this->admin_m->update($data,$id,'user_settings');
				}

				if($insert){
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/settings'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/settings'));
				}	
		}
	}


	/**
	  *** add themes
	**/ 
	public function add_loader(){
		$this->form_validation->set_rules('preloader', 'Pre-loader', 'trim|xss_clean|required');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->settings();
			}else{	
				$data = array(
					'user_id' => auth('id'),
					'preloader' => $this->input->post('preloader',true),
				);
				
				$id = $this->input->post('id');
				if($id==0){
					$insert = $this->admin_m->insert($data,'user_settings');
				}else{
					$insert = $this->admin_m->update($data,$id,'user_settings');
				}

				if($insert){
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/settings'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/settings'));
				}	
		}
	}


	/**
	  *** add settings
	**/ 
	public function add_settings(){
		$this->form_validation->set_rules('address', 'Address', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->settings();
			}else{	
				$data = array(
					'user_id' => auth('id'),
					'phone' => $this->input->post('phone',true),
					'email' => $this->input->post('email',true),
					'address' => $this->input->post('address',true),
					'gmap' => $this->input->post('gmap'),
					'created_at' => d_time(),
				);
				$id = $this->input->post('id');
				if($id==0){
					$insert = $this->admin_m->insert($data,'user_settings');
				}else{
					$insert = $this->admin_m->update($data,$id,'user_settings');
				}

				if($insert){
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/settings'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/settings'));
				}	
		}
	}

	public function add_email_settings(){
		$this->form_validation->set_rules('contact_email', 'Admain Email', 'required|trim|xss_clean|valid_email');


		if($this->input->post('email_type') ==2){
			$this->form_validation->set_rules('smtp_port', 'SMTP PORT', 'trim|required|xss_clean|numeric');
			$this->form_validation->set_rules('smtp_host', 'SMTP HOST', 'trim|required|xss_clean');
			$this->form_validation->set_rules('smtp_password', 'SMTP Email Password', 'trim|required|xss_clean');
		}

		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect(base_url('admin/profile/settings'));
		}else{	
			$data = array(
				'contact_email' => $this->input->post('contact_email',TRUE),
				'smtp_host' => $this->input->post('smtp_host'),
				'smtp_port' => $this->input->post('smtp_port',TRUE),
				'email_type' => $this->input->post('email_type',TRUE),
				'smtp_password' => $this->input->post('smtp_password',TRUE),
			);
			$id = $this->input->post('id',TRUE);

			if($id != 0):
				$insert = $this->admin_m->update($data,$id,'user_settings');
			else:
				$insert = $this->admin_m->insert($data,'user_settings');
			endif;

			if($insert){
				$this->session->set_flashdata('success', 'Save change Successfull');
				redirect(base_url('admin/profile/settings'));
			}else{
				$this->session->set_flashdata('error', 'Somethings were wrong');
				redirect(base_url('admin/profile/settings'));
			}	
		}
	}



	public function manage_features()
	{
		$data = array();
		$data['page_title'] = "Manage Features";
        $data['page'] = "Manage Features";
        $data['data'] = false;
        $data['pricing'] = $this->admin_m->get_all_users_features_by_id(auth('id'));
        $data['features'] = $this->admin_m->get_users_features();
        // echo"<pre>";print_r($data['features']);exit();
		$data['main_content'] = $this->load->view('admin/dashboard/activities/manage_features', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** delete user by admin
	**/ 
	public function delete_product_image($id)
	{
		$img = single_select_by_id($id,'product_images');

		$del= $this->admin_m->delete($id,"product_images");
		if($del):
			delete_image_from_server($img['productimage']);
			$msg = 'Item Successfully Deleted';
			echo json_encode(array('st' => 1, 'msg'=> $msg));
		endif;
	}


	/**
	  *** delete user by admin
	**/ 
	public function delete_portfolio($id)
	{
		$img = single_select_by_id($id,'portfolio');

		$del= $this->admin_m->delete($id,"portfolio");
		if($del):
			delete_image_from_server($img['thumb']);
			delete_image_from_server($img['image']);
			$msg = 'Item Successfully Deleted';
			echo json_encode(array('st' => 1, 'msg'=> $msg));
		endif;
	}


	/**
	  *** delete layout banner
	**/ 
	public function delete_layout_banner($id)
	{
		$img = $this->admin_m->get_banner_img($id);
		$data = array($id =>'');
		$del= $this->admin_m->update_user($data,auth('id'),'features_images');
		if($del):
			delete_image_from_server($img);
			$msg = 'Banner Successfully Deleted';
			echo json_encode(array('st' => 1, 'msg'=> $msg));
		endif;
	}


	/**
   ** default Image Upload for services and reviews
 **/ 
	public function upload_cv()
	{
		
		if(!empty($_FILES['file']['name'])){
			$directory = 'uploads/files/';	
			$dir = $directory;
			$name = $_FILES['file']['name'];
			list($txt, $ext) = explode(".", $name);
			$image_name = md5(time()).".".$ext;
			$tmp = $_FILES['file']['tmp_name'];
		   if(move_uploaded_file($tmp, $dir.$image_name)){
			    $url = $dir.$image_name;
			    $data = array('document' => $url);
				$this->admin_m->update_user($data,auth('id'),'home');	
				echo json_encode(array('st'=>1,'msg'=>'Upload successfully'));
		   }else{
		      echo "image uploading failed";
		   }
		}

	}



	public function appointment()
	{
		$data = array();
		$data['page_title'] = "Appointment";
        $data['page'] = "Appointment";
        $data['appointment_date'] = $this->admin_m->select_all_by_user('my_appointments');
		$data['main_content'] = $this->load->view('admin/dashboard/profile/appointment', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** add home content
	**/ 
	public function add_appointment(){
		$this->form_validation->set_rules('days[]', 'Days', 'trim|required|xss_clean');
		$this->form_validation->set_rules('start_time[]', 'Start Time', 'trim|required|xss_clean');
		$this->form_validation->set_rules('end_time[]', 'End Time', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect(base_url('admin/profile/appointment/'));
		}else{	
			 $this->admin_m->delete_appointment('appointments');
			$days = $this->input->post('days');
			
			if(!empty($days)):
				$i=1;
				foreach ($days as $key => $day):
					$data = array(
						'days' => $day,
						'start_time' => $this->input->post('start_time',true)[$day],
						'end_time' => $this->input->post('end_time',true)[$day],
						'user_id' => auth('id'),
						'created_at' => d_time(),
					);
					 $insert = $this->admin_m->insert($data,'appointments');
				$i++;
				endforeach;
			endif;
			
			if($insert){
				$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
				redirect(base_url('admin/profile/appointment'));
			}else{
				$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
				redirect(base_url('admin/profile/appointment'));
			}	
		}
	}

	public function delete_appointment($id){
		$del=$this->admin_m->delete($id,"my_appointments");
		if($del){
			$this->session->set_flashdata('success', 'Appointment Deleted');
			redirect(base_url('admin/profile/appointment'));
		}else{
			$this->session->set_flashdata('error', 'Somthing worng. Error!!');
			redirect(base_url('admin/profile/appointment'));
		}
	}


	public function layouts()
	{
		$data = array();
		$data['page_title'] = "Layouts";
        $data['page'] = "Layouts";
		$data['main_content'] = $this->load->view('admin/dashboard/activities/layouts', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function change_layouts($type,$value){
		$data = array(
			$type => $value,
		);
		$this->admin_m->update_profile($data,'users');
		echo json_encode(array('st'=>1,));
	}

	public function layouts_banner()
	{
		$data = array();
		$data['page_title'] = "Layouts Banner";
        $data['page'] = "Layouts";
		$data['main_content'] = $this->load->view('admin/dashboard/activities/layouts_banner', $data, TRUE);
		$this->load->view('admin/index',$data);
	}



	public function upload_banner_img($field_name)
	{
	    $data = array();
	    if (!empty($_FILES['file']['name'])) {
	        $filesCount = count($_FILES['file']['name']);
	        for ($i = 0; $i < $filesCount; $i++) {
	              $_FILES['uploadFile']['name'] = str_replace(",","_",$_FILES['file']['name'][$i]);
	              $_FILES['uploadFile']['type'] = $_FILES['file']['type'][$i];
	              $_FILES['uploadFile']['tmp_name'] = $_FILES['file']['tmp_name'][$i];
	              $_FILES['uploadFile']['error'] = $_FILES['file']['error'][$i];
	              $_FILES['uploadFile']['size'] = $_FILES['file']['size'][$i];

	              //Directory where files will be uploaded
	              $uploadPath = 'uploads/site_images/';

	              $config['upload_path'] = $uploadPath;
	              // Specifying the file formats that are supported.
	              $config['allowed_types'] = 'jpg|jpeg|png|gif';
	              $config['overwrite'] = TRUE;
				  $config['encrypt_name'] = TRUE;
	              $this->load->library('upload', $config);
	              $this->upload->initialize($config);
	              // resize library
	              $this->load->library('image_lib');

	              if ($this->upload->do_upload('file')) {
	                  $fileData = $this->upload->data();
	                  $uploadData[$i]['file_name'] = $fileData['file_name'];
			            
	              }else{
	              	$error = array('error' => $this->upload->display_errors());
                    foreach ($error as $value) {
                    	$msg = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
						  <strong>Error!</strong> '.$value.'
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						  </button>
						</div>' ;
                    }
                    echo json_encode(array('st'=>0,'msg'=>$msg,));
                    exit();
	              }

	        }
	          
	        if (!empty($uploadData)) {
	          $list=array();
	            $j=0;foreach ($uploadData as $value) {
		          	$data = array(
		          		'user_id' => auth('id'),
		          		$field_name => $uploadPath.$value['file_name'],
		          	);

		          	//insert image into database query
		          	$check = $this->admin_m->select_by_user('features_images');
		          	if(!empty($check)){
		          		$this->admin_m->update_user($data,auth('id'),'features_images');
		          	}else{
		          		$this->admin_m->insert($data,'features_images');
		          	}
		          	echo json_encode(array('st'=>1, 'url'=> $uploadPath.$value['file_name']));
		         $j++; 	
	          	}
	    		
		  	}else{
		    	$msg = '<div class="alert alert-warning alert-dismissible fade show" role="alert">
						  <strong>Error!</strong> Please Select image
						  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
						    <span aria-hidden="true">&times;</span>
						  </button>
						</div>';
		    	echo json_encode(array('st'=>0,'msg'=>$msg,));
		    }

	    }
	}

	
	/**
	  ** user Team
	**/

	public function team()
	{
		$data = array();
		$data['page_title'] = "Team Members";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['team'] = $this->admin_m->select_all_by_user('team');
		$data['main_content'] = $this->load->view('admin/dashboard/activities/meet_team', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function edit_team($id)
	{
		$data = array();
		$data['page_title'] = "Reviews";
        $data['page'] = "Profile";
        $data['data'] =$this->admin_m->single_select_by_id($id,'team');
        $data['team'] = $this->admin_m->select_all_by_user('team');
		$data['main_content'] = $this->load->view('admin/dashboard/activities/meet_team', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** add_reviews
	**/ 
	public function add_team(){
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('designation', 'Designation', 'trim|required|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->team();
		}else{	
			$data = array(
				'name' => $this->input->post('name',true),
				'user_id' => auth('id'),
				'designation' => $this->input->post('designation',true),
				'created_at' => d_time(),
			);
			$id = $this->input->post('id');
			if($id==0){
				$insert = $this->admin_m->insert($data,'team');
			}else{
				$insert = $this->admin_m->update($data,$id,'team');
			}

			if($insert){
				$this->upload_images($insert,'team');
				$this->session->set_flashdata('success', 'Save change Successfull');
				redirect(base_url('admin/profile/team'));
			}else{
				$this->session->set_flashdata('error', 'Somethings were wrong');
				redirect(base_url('admin/profile/team'));
			}	
		}
	}



	/**
	  ** Fact items
	**/

	public function facts()
	{
		$data = array();
		$data['page_title'] = "Facts";
        $data['page'] = "Profile";
        $data['data'] = false;
        $data['facts'] = $this->admin_m->select_all_by_user('facts');
		$data['main_content'] = $this->load->view('admin/dashboard/activities/facts', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function edit_facts($id)
	{
		$data = array();
		$data['page_title'] = "Facts";
        $data['page'] = "Profile";
        $data['data'] =$this->admin_m->single_select_by_id($id,'facts');
        $data['facts'] = $this->admin_m->select_all_by_user('facts');
		$data['main_content'] = $this->load->view('admin/dashboard/activities/facts', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	/**
	  *** add_reviews
	**/ 
	public function add_facts(){
		$this->form_validation->set_rules('name', 'Name', 'trim|required|xss_clean');
		$this->form_validation->set_rules('icon', 'Icon', 'trim|required|xss_clean');
		$this->form_validation->set_rules('total_item', 'Items Number', 'trim|required|xss_clean|numeric');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			$this->facts();
		}else{	
			$data = array(
				'name' => $this->input->post('name',true),
				'user_id' => auth('id'),
				'icon' => $this->input->post('icon'),
				'total_item' => $this->input->post('total_item'),
				'created_at' => d_time(),
			);
			$id = $this->input->post('id');
			if($id==0){
				$insert = $this->admin_m->insert($data,'facts');
			}else{
				$insert = $this->admin_m->update($data,$id,'facts');
			}

			if($insert){
				$this->upload_images($insert,'facts');
				$this->session->set_flashdata('success', 'Save change Successfull');
				redirect(base_url('admin/profile/facts'));
			}else{
				$this->session->set_flashdata('error', 'Somethings were wrong');
				redirect(base_url('admin/profile/facts'));
			}	
		}
	}


	public function item_delete($id,$table){
		$del=$this->admin_m->item_delete($id,$table);
		if($del){
			$this->session->set_flashdata('success', 'Item Deleted');
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->session->set_flashdata('error', 'Somthing worng. Error!!');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}


	public function delete_about_content($id,$table){
		$del=$this->admin_m->delete($id,$table);
		if($del){
			$this->session->set_flashdata('success', 'Item Deleted');
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->session->set_flashdata('error', 'Somthing worng. Error!!');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function deactive_account($status,$username){
		$user = get_id_by_slug($username);
		$data = array(
			'is_deactived' => $status
		);

		$up=$this->admin_m->update($data,$user['id'],'users');
		if($up){
			if($status==1):
				$this->session->set_flashdata('success', 'Your account is Deactive Now');
			else:
				$this->session->set_flashdata('success', 'Your account is Active Now');
			endif;
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->session->set_flashdata('error', 'Somthing worng. Error!!');
			redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function add_color(){
		$data = array(
			'colors' => substr($this->input->post('colors'), 1),
		);
		$insert = $this->admin_m->update($data,auth('id'),'users');
		if($insert){
			$this->session->set_flashdata('success','Save Change Successful');
			redirect($_SERVER['HTTP_REFERER']);
		}else{
			$this->session->set_flashdata('error','Somethings Were Wrong!!');
			redirect($_SERVER['HTTP_REFERER']);
		}
		
	}

	public function add_recaptcha(){
		$this->form_validation->set_rules('g_site_key', 'Site Key', 'trim|required|xss_clean|required');
		$this->form_validation->set_rules('g_secret_key', 'Secret Key', 'required|trim|xss_clean');
		if ($this->form_validation->run() == FALSE) {
			$this->session->set_flashdata('error', validation_errors());
			redirect(base_url('admin/profile/settings'));
			}else{	
				$data = array(
					'is_recaptcha' => $this->input->post('is_recaptcha',TRUE),
					'g_site_key' => $this->input->post('g_site_key',TRUE),
					'g_secret_key' => $this->input->post('g_secret_key',TRUE),
				);
				$cap_data = array(
					'recaptcha_config' => json_encode($data),
				);
				$id = $this->input->post('id');
				if($id==0){
					$insert = $this->admin_m->insert($cap_data,'user_settings');
				}else{
					$insert = $this->admin_m->update($cap_data,$id,'user_settings');
				}

				if($insert){
					$this->session->set_flashdata('success', !empty(lang('success_text'))?lang('success_text'):'Save Change Successful');
					redirect(base_url('admin/profile/settings'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/settings'));
				}	
		}
	}

	public function rating()
	{
		$data = array();
		$data['page_title'] = "Rating";
        $data['page'] = "Rating";
        $data['facts'] = $this->admin_m->select_all_by_user('facts');
		$data['main_content'] = $this->load->view('admin/dashboard/activities/rating', $data, TRUE);
		$this->load->view('admin/index',$data);
	}

	public function add_rating(){
		$this->form_validation->set_rules('rating', 'Rating', 'trim|xss_clean|required');
		if ($this->form_validation->run() == FALSE) {
				$this->session->set_flashdata('error', validation_errors());
				redirect(base_url('admin/profile/rating'));
			}else{	
				$check_rating = $this->admin_m->check_my_rating();

				if($check_rating ==1):
					$this->session->set_flashdata('error', 'You already give you feedback');
					redirect(base_url('admin/profile/rating'));
				endif;

				
					$data = array(
						'rating' => $this->input->post('rating',TRUE),
						'msg' => $this->input->post('msg',TRUE),
						'action_id' => auth('id'),
						'created_at' => d_time(),
					);

					$insert = $this->admin_m->insert($data,'users_rating');

					if($insert){
					$this->session->set_flashdata('success', !empty(lang('rating_submit'))?lang('rating_submit'):'Rating Submited Successfully');
					redirect(base_url('admin/profile/rating'));
				}else{
					$this->session->set_flashdata('error', !empty(lang('error_text'))?lang('error_text'):'Somethings Were Wrong!!');
					redirect(base_url('admin/profile/rating'));
				}	
		}
	}


	public function error_404()
	{
		$data = array();
		$data['page_title'] = "Error 404";
		$this->load->view('404');
	}

	

}
