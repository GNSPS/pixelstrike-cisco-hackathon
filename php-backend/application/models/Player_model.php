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

class Player_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('date'));
	}

	/**
	 * Player Model Functions
	 *
	 * @author Gonçalo
	 */
    
    /****************************************************
	 * player METHODS: exists, change (insert/update),
     *                   delete, activation_code_collision
     *
	 * @author Gonçalo
	 ***************************************************/
    
    
    
    /**
	 * get the top players from the db
	 *
	 * @return object
	 * @author Gonçalo
	 **/
    public function player_get_leaderboard()
	{
        try
        {
            $query = $this->db->select('caller_id, bombs_actioned, lives_actioned')
                            ->group_by("id")
                            ->order_by("bombs_actioned", "DESC")
                            ->limit(3)
                            ->get('Player');
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $query->result_array();
	}
    
    /**
	 * check for collisions with existing activation codes
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
	public function activation_code_collision_check($activation_code = '')
	{
        if (empty($activation_code) || !is_numeric($activation_code))
		{
			return TRUE;
		}
        
        try
        {
            return $this->db->where('activation_code', $activation_code)
                             ->group_by("id")
                             ->order_by("id", "ASC")
                             ->limit(1)
		                     ->count_all_results('Player') > 0;
        }
        catch(Exception $e)
        {
            return TRUE;
        }
        
        return FALSE;
	}
    
    /**
	 * get a single player given its id
	 *
	 * @return object
	 * @author Gonçalo
	 **/
    public function player_exists($mac_addr = '')
	{
        if (empty($mac_addr))
		{
			return TRUE;
		}
        
        try
        {
            return $this->db->where('mac_address', $mac_addr)
                            ->group_by("id")
                            ->order_by("id", "ASC")
                            ->limit(1)
                            ->count_all_results('Player') > 0;
        }
        catch(Exception $e)
        {
            return TRUE;
        }
        
        return TRUE;
	}
    
    /**
	 * get a single player given its mac address
	 *
	 * @return object
	 * @author Gonçalo
	 **/
    public function player_get_mac_addr($mac_addr = '')
	{
        if (empty($mac_addr))
		{
			return FALSE;
		}
        
        try
        {
            $query = $this->db->where('mac_address', $mac_addr)
                            ->group_by("id")
                            ->order_by("id", "ASC")
                            ->limit(1)
                            ->get('Player');
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $query->row();
	}
    
    /**
	 * get a single player given its caller id
	 *
	 * @return object
	 * @author Gonçalo
	 **/
    public function player_get($caller_id = '')
	{
        if (empty($caller_id))
		{
			return FALSE;
		}
        
        try
        {
            $query = $this->db->where('caller_id', $caller_id)
                            ->group_by("id")
                            ->order_by("id", "ASC")
                            ->limit(1)
                            ->get('Player');
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $query->row();
	}
    
    /**
	 * create player (keep its active status '0' and create a random 4 digit number for validation over the phone)
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
	public function player_create($mac_addr = '')
	{
        if (empty($mac_addr))
		{
			return FALSE;
		}
        
        do{
            $newgid = sprintf('%04d', rand(0, 9999));
        } while($this->activation_code_collision_check($newgid));
        
        try
        {
            $data = array(
                'mac_address' => $mac_addr,
                'activation_code' => $newgid
            );

            $this->db->insert('Player', $data);

            $payer_id = $this->db->insert_id();
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $newgid;
	}
    
    /**
	 * explode bomb (update its status_id to '0' and set )
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
	public function player_activate($caller_id = '', $activation_code = '', $t_ct = '')
	{
        if (empty($caller_id) || !is_numeric($caller_id) || $activation_code === '' || !is_numeric($activation_code) || $t_ct === '' || !is_numeric($t_ct))
		{
			return FALSE;
        }
        
        $data = array(
            'caller_id' => $caller_id,
            't_ct' => $t_ct,
            'active' => 1
        );
        
        try
        {
            $this->db->update('Player', $data, array('activation_code' => $activation_code));

            if ($this->db->affected_rows() == 0)
            {
                return FALSE;
            }
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return TRUE;
	}
    
    /**
	 * update player location in the DB
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
	public function player_update_loc($mac_addr = '', $lat = '', $lon = '')
	{
        if (empty($mac_addr) || empty($lat) || !is_numeric($lat) || empty($lon) || !is_numeric($lon))
		{
			return FALSE;
		}
        
        try
        {
            $data = array(
                'lat'  => $lat,
                'lon'  => $lon
            );

            $this->db->update('Player', $data, array('mac_address' => $mac_addr));

            if ($this->db->affected_rows() == 0)
            {
                return FALSE;
            }
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return TRUE;
	}
    
    /**
	 * get a list of the 5 nearest bombs available to defuse if you're a CT
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
    public function player_get_nearest_bombs($caller_id = '', $dist = 300)
    {
        if (empty($caller_id) || !is_numeric($caller_id))
		{
			return FALSE;
		}
        
        $player = $this->player_get($caller_id);
        
        if(empty($player)){
            return FALSE;
        }
        
        try
        {
            $tableName = "Bomb";
            $origLat = $player->lat;
            $origLon = $player->lon;
            // The param $dist is the maximum distance (in meters) away from $origLat, $origLon in which to search
            $sql = "SELECT id, lat, lon, active, 6378137 * 2 * 
                      ASIN(SQRT( POWER(SIN(($origLat - lat)*pi()/180/2),2)
                      +COS($origLat*pi()/180 )*COS(lat*pi()/180)
                      *POWER(SIN(($origLon-lon)*pi()/180/2),2))) 
                      as distance
                      FROM $tableName
                      WHERE active > 0 and lon between ($origLon-$dist/cos(radians($origLat))*111044.736) 
                      and ($origLon+$dist/cos(radians($origLat))*111044.736) 
                      and lat between ($origLat-($dist/111044.736)) and ($origLat+($dist/111044.736))
                      having distance < $dist ORDER BY distance limit 5"; 

            $query = $this->db->query($sql);
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return $query->result_array();
    }
    
    /**
	 * get a count of the number of bombs you can still plant (only X at a time can be active) if you're a Terrorist
	 *
	 * @return bool
	 * @author Gonçalo
	 **/
    public function player_get_available_bombs($caller_id = '', $bombs_active_simultaneously = 2)
    {
        if (empty($caller_id) || !is_numeric($caller_id))
		{
			return FALSE;
		}
        
        $player = $this->player_get($caller_id);
        
        if(empty($player)){
            return FALSE;
        }
        
        try
        {
            $active_bombs = $this->db->where('planted_by', $player->caller_id)
                                    ->where('active >', 0)
                                    ->count_all_results('Bomb');
        }
        catch(Exception $e)
        {
            return FALSE;
        }
        
        return max(2-$active_bombs, 0);
    }
}
