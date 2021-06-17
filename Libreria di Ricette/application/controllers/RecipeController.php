<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RecipeController extends CI_Controller {

	// construct function that loads Resep Model and Pagination Library
    function __construct()
	{
		parent::__construct();
		$this->load->model('Resep');
		$this->load->model('Member');
		$this->load->model('Review');
		$this->load->model('Bahan');
		$this->load->library('pagination');
		$this->load->helper('date');
	}

	// function index that loads all available recipe in database
	// loading is configured by pagination
    public function index() {
        // pagination configuration
		$config['base_url'] = site_url('RecipeController/index');
		$config['total_rows'] = $this->db->count_all('resep');
		$config['per_page'] = 9;
		$config["uri_segment"] = 3;  // uri parameter
        $choice = $config["total_rows"] / $config["per_page"];
        $config["num_links"] = floor($choice);

		// pagination style
        $config['first_link']       = 'First';
        $config['last_link']        = 'Last';
        $config['next_link']        = 'Next';
        $config['prev_link']        = 'Prev';
        $config['full_tag_open']    = '<div class="pagging text-center"><nav><ul class="pagination justify-content-center">';
        $config['full_tag_close']   = '</ul></nav></div>';
        $config['num_tag_open']     = '<li class="page-item"><span class="page-link">';
        $config['num_tag_close']    = '</span></li>';
        $config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close']    = '<span class="sr-only">(current)</span></span></li>';
        $config['next_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['next_tagl_close']  = '<span aria-hidden="true">&raquo;</span></span></li>';
        $config['prev_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['prev_tagl_close']  = '</span>Next</li>';
        $config['first_tag_open']   = '<li class="page-item"><span class="page-link">';
        $config['first_tagl_close'] = '</span></li>';
        $config['last_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['last_tagl_close']  = '</span></li>';

		// initialize pagination and content
		$this->pagination->initialize($config);
        $content['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $content['recipe'] = $this->Resep->get_resep_list($config["per_page"], $content['page']);
        
        $resepPage = $this->Resep->get_resep_list($config["per_page"], $content['page']);
        $arrMember =array();
		foreach ($resepPage as $y) {
			$arrMember[] = $this->Member->get_member_id($y['idMember']);
		}
		$content['member'] = $arrMember;
        $content['pagination'] = $this->pagination->create_links();

		// load the page
		$this->load->view('header');
		$this->load->view('homepage',$content);
		$this->load->view('footer');
    }

	// function to read a recipe based on its id
	public function view_recipe($id_recipe) {
		$content['recipe'] = $this->Resep->get_resep_id($id_recipe);
		$content['langkah'] = $this->Resep->get_langkah($id_recipe);
		
		$bahanid = $this->Resep->get_resep_bahan($id_recipe);
		$content['takaran'] = $bahanid;
		$arrBahan = array();
		foreach ($bahanid as $x) {
			$arrBahan[] = $this->Bahan->get_bahan($x['idBahan']);	
		}
		$content['bahan'] = $arrBahan;

		$reviewMember = $this->Review->getAllReview($id_recipe);
		$content['review'] = $reviewMember;
		$arrMember =array();
		foreach ($reviewMember as $y) {
			$arrMember[] = $this->Member->get_member_id($y['idMember']);
		}
		$content['memberReview'] = $arrMember;

		$member = $this->Resep->get_resep_id($id_recipe);
		$content['member'] = $this->Member->get_member_id($member['idMember']);
		$toko = array();
		foreach ($arrBahan as $index => $b) {
			$toko[] = $this->Bahan->get_toko_bahan($b['idBahan']);
		}
		$content['toko'] = $toko;
		$this->load->view('header');
		$this->load->view('fullRecipe', $content);
		$this->load->view('footer');
	}

	// function to load create recipe form
	public function form_recipe() {
		$data['bahan'] = $this->Bahan->get_all_bahan();

		$this->load->view('header');
		$this->load->view('formCreateResep', $data);
		$this->load->view('footer');
	}

	// function to create a recipe
	public function create_recipe() {
		$config['upload_path']          = 'assets/img/';
        $config['allowed_types']        = 'jpg|png';
        $config['max_size']             = 2000;
        $config['max_width']            = 1920;
        $config['max_height']           = 1080;

        $this->load->library('upload', $config);
		$cek_upload = $this->upload->do_upload('userfile');
		$file_name= $this->upload->data('file_name');

		$x = $this->Resep->get_last_resep();
        if ($x == null) {
            $x['ids'] = 0;
        }

		$data_resep = array(
			'idMember' => $this->session->user_id,
			'resepPic' => $file_name,
			'idResep' => 'R-'.strval($x['ids']+1),
			'deskripsi' => $this->input->post('deskripsi'),
			'judul' => $this->input->post('judul'),
			'rating' => 0
		);
		$cek = $this->Resep->buat_resep($data_resep);

		$bahan = $this->input->post('bahan[]');
		$takaran = $this->input->post('takaran[]');
		$cek1 = $this->_input_bahan_takaran($data_resep['idResep'], $bahan, $takaran);

		$data_langkah = $this->input->post('langkah[]');
		$cek2 = $this->_input_step_resep($data_resep['idResep'], $data_langkah);

		if ($cek and $cek1 and $cek2 and $cek_upload) {
			redirect('RecipeController/view_recipe/'.$data_resep['idResep'], 'refresh');
		}
	}

	private function _input_bahan_takaran($id_recipe, $bahan, $takaran) {
		foreach ($bahan as $index => $b) {
			$data_bahan = array(
				'idBahan' => $b,
				'idResep' => $id_recipe,
				'takaran' => $takaran[$index]
			);
			$cek_bahan = $this->Resep->create_bahan_resep($data_bahan);
			if (!$cek_bahan) {
				return FALSE;
			}
		}
		return TRUE;
	}

	private function _input_step_resep($id_recipe, $langkah) {
		$last_step = $this->Resep->get_last_step();
        if ($last_step == null) {
			$x = 0;
        } else {
			$x =$last_step['ids'];
		}

		$step = 1;
		foreach ($langkah as $l) {
			$idStep = "S-".$x;

			$data_langkah = array(
				'idStep' => $idStep,
				'idResep' => $id_recipe,
				'deskripsi' => $l,
				'stepKe' => $step,
				'stepPic' => '-'
			);
			$cek_langkah = $this->Resep->tambah_langkah($data_langkah);
			if (!$cek_langkah) {
				return FALSE;
			}
			$x++;
			$step++;
		}
		return TRUE;
	}

	// function to load edit recipe form
	public function form_edit_recipe($id_recipe) {
		$data['bahan'] = $this->Bahan->get_all_bahan();
		$data['resep'] = $this->Resep->get_resep_id($id_recipe);
		$data['bahan_resep'] = $this->Resep->get_resep_bahan($id_recipe);
		$data['langkah'] = $this->Resep->get_langkah($id_recipe);

		$this->load->view('header');
		$this->load->view('formEditResep', $data);
		$this->load->view('footer');
	}

	// function to edit a recpie
	public function edit_recipe($id_recipe) {
		$data_resep = array(
			'deskripsi' => $this->input->post('deskripsi'),
			'judul' => $this->input->post('judul'),
		);
		$cek = $this->Resep->update_resep($id_recipe, $data_resep);

		$bahan = $this->input->post('bahan[]');
		$takaran = $this->input->post('takaran[]');
		$cek1 = $this->_update_bahan_takaran($id_recipe, $bahan, $takaran);

		$data_langkah = $this->input->post('langkah[]');
		$cek2 = $this->_update_step_resep($id_recipe, $data_langkah);

		if ($cek and $cek1 and $cek2) {
			redirect('RecipeController/view_recipe/'.$id_recipe, 'refresh');
		}
	}

	private function _update_bahan_takaran($id_recipe, $bahan, $takaran) {
		$old_bahan = $this->Resep->get_resep_bahan($id_recipe);
		foreach ($old_bahan as $ob) {
			$this->Resep->delete_bahan_resep($id_recipe);
		}

		foreach ($bahan as $index => $b) {
			$data_bahan = array(
				'idBahan' => $b,
				'idResep' => $id_recipe,
				'takaran' => $takaran[$index]
			);
			$cek_bahan = $this->Resep->create_bahan_resep($data_bahan);
			if (!$cek_bahan) {
				return FALSE;
			}
		}
		return TRUE;
	}

	private function _update_step_resep($id_recipe, $langkah) {
		$last_step = $this->Resep->get_last_step();
        if ($last_step == null) {
			$x = 0;
        } else {
			$x =$last_step['ids'];
		}

		$old_langkah = $this->Resep->get_langkah($id_recipe);
		foreach ($old_langkah as $ol) {
			$this->Resep->delete_langkah($ol['idStep']);
		}

		$step = 1;
		foreach ($langkah as $l) {
			$idStep = "S-".$x;

			$data_langkah = array(
				'idStep' => $idStep,
				'idResep' => $id_recipe,
				'deskripsi' => $l,
				'stepKe' => $step,
				'stepPic' => '-'
			);
			$cek_langkah = $this->Resep->tambah_langkah($data_langkah);
			if (!$cek_langkah) {
				return FALSE;
			}
			$x++;
			$step++;
		}
		return TRUE;
	}

	// function to delete a recipe based on its id
	public function delete_recipe($id_recipe) {
		$review = $this->Review->getAllReview($id_recipe);
		foreach ($review as $r) {
			$this->Review->delete_review($r['idReview']);
		}
		$langkah = $this->Resep->get_langkah($id_recipe);
		foreach ($langkah as $l) {
			$this->Resep->delete_langkah($l['idStep']);
		}
		$cek = $this->Resep->delete_resep($id_recipe);
		if ($cek) {
			redirect('AccountController/view_profile/'.$this->session->username, 'refresh');
		}
	}
	public function add_review($id_recipe){
		$x = $this->Review->get_last_review();
        if ($x == null) {
            $x['ids'] = 0;
        }
		$username = $this->session->userdata('username');
		$member = $this->Member->get_member_username($username);
		$data = array(
			'idReview' => 'Rev-'.$x['ids']+1,
			'rating' => $this->input->post('rating'),
			'isi' => $this->input->post('isi'),
			'idMember' => $member['idMember'],
			'idResep' => $id_recipe,
			'tglReview' => time(),
		);
		$resep = $this->Resep->get_resep_id($id_recipe);
		if ($resep['rating'] == 0) {
			$rating = array('rating' => $this->input->post('rating'));
		} else {
			$rating = array('rating' => round(($this->input->post('rating')+$resep['rating'])/2));
		}
		$cek = $this->Review->tambahReviewBaru($data);
		$this->Resep->update_resep($id_recipe,$rating);
		redirect('RecipeController/view_recipe/'.$id_recipe, 'refresh');
	}

	public function search(){
		$keyword = $this->input->post('keyword');
		$data['recipe']= $this->Resep->get_Resep_keyword($keyword);
		$resepPage =  $this->Resep->get_Resep_keyword($keyword);
    	$arrMember =array();
		foreach ($resepPage as $y) {
			$arrMember[] = $this->Member->get_member_id($y['idMember']);
		}
		$data['member'] = $arrMember;
		$this->load->view('header');
		$this->load->view('search',$data);
		$this->load->view('footer');
	}
	public function sort_resep($x){
		$config['total_rows'] = $this->db->count_all('resep');
		$config['per_page'] = 9;
		$config["uri_segment"] = 3;  // uri parameter
        $choice = $config["total_rows"] / $config["per_page"];
        $config["num_links"] = floor($choice);

		// pagination style
        $config['first_link']       = 'First';
        $config['last_link']        = 'Last';
        $config['next_link']        = 'Next';
        $config['prev_link']        = 'Prev';
        $config['full_tag_open']    = '<div class="pagging text-center"><nav><ul class="pagination justify-content-center">';
        $config['full_tag_close']   = '</ul></nav></div>';
        $config['num_tag_open']     = '<li class="page-item"><span class="page-link">';
        $config['num_tag_close']    = '</span></li>';
        $config['cur_tag_open']     = '<li class="page-item active"><span class="page-link">';
        $config['cur_tag_close']    = '<span class="sr-only">(current)</span></span></li>';
        $config['next_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['next_tagl_close']  = '<span aria-hidden="true">&raquo;</span></span></li>';
        $config['prev_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['prev_tagl_close']  = '</span>Next</li>';
        $config['first_tag_open']   = '<li class="page-item"><span class="page-link">';
        $config['first_tagl_close'] = '</span></li>';
        $config['last_tag_open']    = '<li class="page-item"><span class="page-link">';
        $config['last_tagl_close']  = '</span></li>';

		// initialize pagination and content
		$this->pagination->initialize($config);
        $content['page'] = ($this->uri->segment(3)) ? $this->uri->segment(3) : 0;
        $content['recipe'] = $this->Resep->get_resep_sorted($x,$config["per_page"], $content['page']);
        
        $resepPage = $this->Resep->get_resep_sorted($x,$config["per_page"], $content['page']);
        $arrMember =array();
		foreach ($resepPage as $y) {
			$arrMember[] = $this->Member->get_member_id($y['idMember']);
		}
		$content['member'] = $arrMember;
        $content['pagination'] = $this->pagination->create_links();

		// load the page
		$this->load->view('header');
		$this->load->view('homepage',$content);
		$this->load->view('footer');
	}
}
?>
