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

class Bomb extends REST_Controller
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
    
    function defuse_get()
    {
        if (!($caller_id = $this->get('callerid')) || !($bomb_id = $this->get('bomb_id')))
        {
        	echo json_encode(array('error' => 1, 'emsg' => 'Unsufficient parameters. Please call from a valid device!', 'data' => array()));
        }
        
        $player = $this->player_model->player_get($caller_id);
        
        if (!empty($player))
		{
            if($player->t_ct)
            {
                echo json_encode(array('error' => 1, 'emsg' => 'Good try but you\'re a Terrorist, you shouldn\'t want to defuse bombs, you silly!', 'data' => array()));
            }
        }
        
        if (!($bomb_id = $this->bomb_model->bomb_defuse($bomb_id, $caller_id)))
		{
            echo json_encode(array('error' => 1, 'emsg' => 'Internal Error: An error happened while defusing your bomb. Please try again and make sure you activated your cell!', 'data' => array()));
		}
        else
        {
            echo json_encode(array('error' => 0, 'data' => array('bomb_id' => $bomb_id), 'data' => array()));
            
        }
    }
    
    function index_get()
    {
        if (!($caller_id = $this->get('callerid')))
        {
        	echo json_encode(array('error' => 1, 'emsg' => 'Unsufficient parameters. Please call from a valid device!', 'data' => array()));
        }
        
        $player = $this->player_model->player_get($caller_id);
        
        if (!empty($player))
		{
            if(!$player->t_ct)
            {
                echo json_encode(array('error' => 1, 'emsg' => 'Good try but you\'re a Counter-Terrorist, you shouldn\'t want to plant bombs, you silly!', 'data' => array()));
            }
        }
        
        if (!($bomb_id = $this->bomb_model->bomb_plant($caller_id)))
		{
            echo json_encode(array('error' => 1, 'emsg' => 'Internal Error: An error happened while planting your bomb. Please try again and make sure you activated you cell!', 'data' => array()));
		}
        else
        {
            echo json_encode(array('error' => 0, 'data' => array('bomb_id' => $bomb_id)));
            
        }
    }
}