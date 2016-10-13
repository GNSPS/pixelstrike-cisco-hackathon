<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Pages extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
        $this->load->model(array('bomb_model', 'player_model'));
	}
	
	//Function to handle all unknown or inexistent tabs written by the user
	public function show()
	{
        $data = array();
        
        if($this->input->get('client_mac'))
        {
            if ($this->player_model->player_exists($this->input->ip_address()))
            {
                $data['activation_code'] = $this->player_model->player_get_mac_addr($this->input->get('client_mac'))->activation_code;
            }
            else
            {
                $data['activation_code'] = $this->player_model->player_create($this->input->get('client_mac'));
            }
        }
        
        $data['leaderboard'] = $this->player_model->player_get_leaderboard();
        
        $this->load->view("header");
        $this->load->view("pixel_striker_splash", $data);
        $this->load->view("footer");
	}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */