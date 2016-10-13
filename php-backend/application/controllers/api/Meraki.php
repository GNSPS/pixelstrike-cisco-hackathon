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

class Meraki extends REST_Controller
{
    function __construct()
    {
//        /*****************************
//         ********** DANGER ***********
//         *****************************/ 
//        
//        header('Access-Control-Allow-Headers: X-REFERER');
//        header('Access-Control-Allow-Credentials: true');
//        
//        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
//            //EXTREMELY DANGEROUS CONFIGURATION (SECURITY-WISE) BELOW. DEAL WITH IT LATER
//            header('Access-Control-Allow-Origin: '.($_SERVER['HTTP_ORIGIN'] ? $_SERVER['HTTP_ORIGIN'] : '*'));
//            header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE');
//            exit;
//        }
//        else
//        {
//            //EXTREMELY DANGEROUS CONFIGURATION (SECURITY-WISE) BELOW. DEAL WITH IT LATER
//            $origin_host = (isset($_SERVER['HTTP_ORIGIN'])
//                           ? $_SERVER['HTTP_ORIGIN']
//                           : (isset($_SERVER['HTTP_X_REFERER']) ? $_SERVER['HTTP_X_REFERER'] : '*'));
//            header('Access-Control-Allow-Origin: '.$origin_host);
//            header('Access-Control-Allow-Methods: GET,PUT,POST,DELETE');
//        }
//        
//        /*****************************
//         ********** DANGER ***********
//         *****************************/
        
        // Construct our parent class
        parent::__construct();
        $this->load->model(array('bomb_model', 'player_model'));
        $this->load->helper('url');
    }
    
    function index_get()
    {
        http_response_code(200);
        echo "504b77fa22db2b0d192cadf2d2af398230578ab3";
        return;
    }
    
    function index_post()
    {
        $post_body = $this->post(NULL);
        
        if(empty($post_body))
        {
        	$this->response(array('error' => 1, 'emsg' => 'Wrong or inexistent params. Please don\'t mess with us! ^_^'), 400);
        }
        
        if(empty($post_body['secret']) || $post_body['secret'] != 'Gon?lo')
        {
            $this->response(array('error' => 1, 'emsg' => 'Wrong or inexistent secret. Please don\'t mess with us! ^_^'), 400);
        }
        
        foreach($post_body['data']['observations'] as $observation)
        {
//            if(!empty($observation['location']['lat']))
//                file_get_contents("http://pixelstrike-gnsps.rhcloud.com/test?lat=".$observation['location']['lat']);
//            if(!empty($observation['location']['lng']))
//                file_get_contents("http://pixelstrike-gnsps.rhcloud.com/test?lng=".$observation['location']['lng']);
//            if(isset($observation['clientMac']))
//                file_get_contents("http://pixelstrike-gnsps.rhcloud.com/test?clientmac=".$observation['clientMac']);
            
            if(!empty($observation['clientMac']) || !empty($observation['location']['lat']) || !empty($observation['location']['lng']))
            {
                if($this->player_model->player_exists($observation['clientMac']))
                {
                    $this->player_model->player_update_loc($observation['clientMac'], $observation['location']['lat'], $observation['location']['lng']);
                }
                else
                {
                    $this->player_model->player_create($observation['clientMac']);
                    $this->player_model->player_update_loc($observation['clientMac'], $observation['location']['lat'], $observation['location']['lng']);
                }
            }
        }
        
        $this->bomb_model->bomb_explode_all_old();
        
        $this->response('', 200);
    }
}