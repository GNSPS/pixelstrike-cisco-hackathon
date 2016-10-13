<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Bubble Model
*
* Version: 2.5.2
*
* Author:  Gonçalo Sá
* 		   goncalo05@gmail.com
*	  	   @GNSPS
*
* Created:  18.05.2015
*
* Description:  Model to serve as a driver for all things DB related (fetching, posting, putting) of the clickly bubble
*
* Requirements: PHP5 or above
*
*/

class Bomb_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('date'));
        $this->load->model(array('player_model'));
	}

	/**
	 * Bomb Model Functions
	 *
	 * @author Gonçalo
	 */
    
    /****************************************************
	 * BOMB METHODS: exists, change (insert/update),
     *                   delete
     *
	 * @author Gonçalo
	 ***************************************************/
    /**
	 * check if bomb exists
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
	public function bomb_exists($bomb_id = '')
	{
        if (empty($bomb_id) || !is_numeric($bomb_id))
		{
			return FALSE;
		}
        
        try
        {
            return $this->db->where('id', $bomb_id)
                             ->group_by("id")
                             ->order_by("id", "ASC")
                             ->limit(1)
		                     ->count_all_results('Bomb') > 0;
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return FALSE;
        
	}
    
    /**
	 * check if bomb is active
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
	public function bomb_is_active($bomb_id = '')
	{
        if (empty($bomb_id) || !is_numeric($bomb_id))
		{
			return FALSE;
		}
        
        try
        {
            return $this->db->where('id', $bomb_id)
                            ->where('active', 1)
                             ->group_by("id")
                             ->order_by("id", "ASC")
                             ->limit(1)
		                     ->count_all_results('Bomb') > 0;
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return FALSE;
        
	}
    
    /**
	 * get a single bomb given its id
	 *
	 * @return object
	 * @author Gonçalo
	 **/
    public function bomb_get($bomb_id = '')
	{
        if (empty($bomb_id))
		{
			return FALSE;
		}
        
        try
        {
            $query = $this->db->where('id', $bomb_id)
                            ->group_by("id")
                            ->order_by("id", "ASC")
                            ->limit(1)
                            ->get('Bomb');
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $query->row();
	}
    
    /**
	 * get all bombs that were set X+ secs ago (7 min default) and are active ('active' != 0)
	 *
	 * @return object
	 * @author Gonçalo
	 **/
    public function bomb_get_all_old($secs_ago = 420)
	{
        try
        {
            $query = $this->db->where('planted_on < DATE_SUB(NOW(),INTERVAL '.$secs_ago.' SECOND)')
                            ->where('active >', 0)
                            ->order_by("id", "ASC")
                            ->get('Bomb');
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $query->result_array();
	}
    
    /**
	 * explode all bombs that were set X+ secs ago (7 min default)
	 *
	 * @return object
	 * @author Gonçalo
	 **/
    public function bomb_explode_all_old($secs_ago = 420)
	{
        $bombs = $this->bomb_model->bomb_get_all_old($secs_ago);
        
        foreach($bombs as $bomb)
        {
            $this->bomb_explode($bomb['id']);
        }
        
        return TRUE;
	}
    
    /**
	 * defuse bomb (update its active status to '0', set defuse time and player id of the cop)
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
	public function bomb_defuse($bomb_id = '', $caller_id = '')
	{
        if (empty($bomb_id) || !is_numeric($bomb_id) || empty($caller_id) || !is_numeric($caller_id))
		{
			return FALSE;
		}
        
        if(!$this->bomb_is_active($bomb_id))
        {
			return FALSE;
		}
        
        $data = array(
            'active' => 0,
            'deactivated_on' => date('Y-m-d H:i:s'),
            'defused_by' => $caller_id,
            'exploded' => 0
        );
        
        try
        {
            $this->db->update('Bomb', $data, array('id' => $bomb_id));

            if ($this->db->affected_rows() == 0)
            {
                return FALSE;
            }
            
            $lives_actioned = $this->bomb_get_nearest_players_number($bomb_id);
            
            $this->db->where('caller_id', $caller_id);
            $this->db->set('bombs_actioned', 'bombs_actioned+1', FALSE);
            $this->db->set('lives_actioned', 'lives_actioned+'.$lives_actioned, FALSE);
            $this->db->update('Player');

            if ($this->db->affected_rows() == 0)
            {
                return FALSE;
            }
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $bomb_id;
	}
    
    /**
	 * explode bomb (update its status_id to '0' and set )
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
	public function bomb_explode($bomb_id = '')
	{
        if (empty($bomb_id))
		{
			return FALSE;
		}
        
        if(empty($bomb_id) || !is_numeric($bomb_id))
        {
            return FALSE;
        }
        
        $data = array(
            'active' => 0,
            'deactivated_on' => date('Y-m-d H:i:s'),
            'exploded' => 1
        );
        
        try
        {
            
            
            $this->db->update('Bomb', $data, array('id' => $bomb_id));

            if ($this->db->affected_rows() == 0)
            {
                return FALSE;
            }
            
            $lives_actioned = $this->bomb_get_nearest_players_number($bomb_id);
            
            $this->db->where('caller_id', $this->bomb_get($bomb_id)->planted_by);
            $this->db->set('bombs_actioned', 'bombs_actioned+1', FALSE);
            $this->db->set('lives_actioned', 'lives_actioned+'.$lives_actioned, FALSE);
            $this->db->update('Player');

            if ($this->db->affected_rows() == 0)
            {
                return FALSE;
            }
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $bomb_id;
	}
    
    /**
	 * plant bomb (create the bomb row)
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
	public function bomb_plant($caller_id = '')
	{
        if (empty($caller_id) || !is_numeric($caller_id))
		{
			return FALSE;
		}
        
        $player = $this->player_model->player_get($caller_id);
        
        if(empty($player) || empty($player->lat) || empty($player->lon)){
            return FALSE;
        }
        
        try
        {
            $data = array(
                'lat'  => $player->lat,
                'lon'  => $player->lon,
                'planted_by'   => $caller_id
            );

            $this->db->insert('Bomb', $data);

            $bomb_id = $this->db->insert_id();
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return (isset($bomb_id)) ? $bomb_id : FALSE;
	}
    
    /**
	 * get number of players near the bomb with an Haversine query to MySQL (the players need not be active to be killed)
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
    public function bomb_get_nearest_players_number($bomb_id = '', $dist = 10)
    {
        if (empty($bomb_id) || !is_numeric($bomb_id))
		{
			return FALSE;
		}
        
        $bomb = $this->bomb_get($bomb_id);
        
        if(empty($bomb)){
            return FALSE;
        }
        
        try
        {
            $tableName = "Player";
            $origLat = $bomb->lat;
            $origLon = $bomb->lon;
            // The param $dist is the maximum distance (in meters) away from $origLat, $origLon in which to search
            $sql = "SELECT count(*) as ncount
                    FROM
                    (SELECT lat, lon, 6378137 * 2 * 
                        ASIN(SQRT( POWER(SIN(($origLat - lat)*pi()/180/2),2)
                        +COS($origLat*pi()/180 )*COS(lat*pi()/180)
                        *POWER(SIN(($origLon-lon)*pi()/180/2),2))) 
                        as distance
                        FROM $tableName
                        WHERE lon between ($origLon-$dist/cos(radians($origLat))*111044.736) 
                        and ($origLon+$dist/cos(radians($origLat))*111044.736) 
                        and lat between ($origLat-($dist/111044.736)) 
                        and ($origLat+($dist/111044.736))
                    ) as bombs
                    WHERE distance < $dist"; 

            $query = $this->db->query($sql);
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $query->row()->ncount;
    }
    /****************************************************
	 * BOMB METHODS END
     ***************************************************/
}
