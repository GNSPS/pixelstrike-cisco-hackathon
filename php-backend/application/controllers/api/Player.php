<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Bubble
 *
 * Bubble API RESTful controller to retrieve, create & edit bubbles for
 * clickly.co and outside use cases.
 *
 * @package		CodeIgniter
 * @subpackage	Rest Server
 * @category	Controller
 * @author		GNSPS
 * @link		http://clickly.co/
*/

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
require APPPATH.'/libraries/REST_Controller.php';

class Player extends REST_Controller
{
	function __construct()
    {
        http_response_code(200);
        header('Content-Type: application/json; charset=utf-8');
        
        // Construct our parent class
        parent::__construct();
        
        $this->load->helper(array('email'));
        $this->load->model(array('bomb_model', 'player_model'));
    }
    
    function index_get()
    {
        if (!($caller_id = $this->get('callerid')))
        {
        	echo json_encode(array('error' => 1, 'emsg' => 'Unsufficient parameters. Please call from a valid device!'));
        }
        
        $player = $this->player_model->player_get($caller_id);
        
        if (!empty($player))
		{
            if(!$player->t_ct)
            {
                $data['bombs'] = $this->player_model->player_get_nearest_bombs($caller_id);
            }
            else
            {
                $data['bombs']['available'] = $this->player_model->player_get_available_bombs($caller_id);
            }
            
            $data['player'] = $player;
            
			echo json_encode(array('error' => 0, 'data' => $data));
		}
        else
        {
            echo json_encode(array('error' => 2, 'emsg' => 'User is not activated yet. Please phone our helper! :)', 'data' => array()));
            
        }
    }
    
    function activate_get()
    {
        if (!($caller_id = $this->get('callerid')) || ($activation_code = $this->get('code')) === FALSE || ($t_ct = $this->get('t_ct')) === FALSE)
        {
        	echo json_encode(array('error' => 1, 'emsg' => 'Unsufficient parameters. Please don\'t mess with us! ^_^'));
        }
        
        else
        {
            if($this->player_model->player_activate($caller_id, $activation_code, $t_ct))
            {
                echo json_encode(array('error' => 0, 'data' => array()));
            }
            else
            {
                echo json_encode(array('error' => 1, 'emsg' => 'Internal Error: While activating the player something went wrong. PLease try again! :)', 'data' => array()));
            }
        }
    }
}