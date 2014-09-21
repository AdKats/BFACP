<?php namespace ADKGamers\Webadmin\Libs;

	/**
	 * gameME SDK
	 * Webpage: http://www.gameme.com
	 * Docs: http://www.gameme.com/docs/api/sdk
	 * Copyright (C) 2011-2013 TTS Oetzel & Goerz GmbH
	 *
	 * This program is free software; you can redistribute it and/or
	 * modify it under the terms of the GNU General Public License
	 * as published by the Free Software Foundation; either version 2
	 * of the License, or (at your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful,
	 * but WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	 * GNU General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program; if not, write to the Free Software
	 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
	 *
	 *
	 * All API results are limited to personal, non-commercial use only
     * Copyright(C) gameME 2011-2013 TTS Oetzel & Goerz GmbH. All rights reserved.
	 */


	define("GAMEME_SDK_VERSION", 5);


	/**
	 * Global API urls
	 */
	define("GAMEME_GLOBALAPI_URL", "http://api.gameme.net");
	define("GAMEME_GLOBALAPI_SECURE_URL", "https://api.gameme.net");


	/**
	 * Retrieve data compressed (faster)
	 */
	define("GAMEME_FETCHDATA_COMPRESSED", 1);


	/**
	 * Inbuilt cache to improve performance
	 */
	define("GAMEME_APICACHE_ENABLED", 0);
	// Optional cache dir, if not setup the system temp dir is used
	define("GAMEME_APICACHE_DIRECTORY", "");
	// Max entry size limit, default 10 mbyte
	define("GAMEME_APICACHE_MAX_SIZE_LIMIT", 1048576 * 10);
	// Auto-Prune cache files
	define("GAMEME_APICACHE_AUTO_PRUNE", 1);


	/**
	 * Limited by backend, do not change
	 */
	define("GAMEME_GLOBALAPI_DEFAULT_LIMIT", 100);
	define("GAMEME_GLOBALAPI_MAXIMUM_LIMIT", 250);
	define("GAMEME_GLOBALAPI_MULTIPLE_SERVERINFO_LIMIT", 32);
	define("GAMEME_GLOBALAPI_MULTIPLE_PLAYERINFO_LIMIT", 64);

	define("GAMEME_CLIENTAPI_DEFAULT_LIMIT", 100);
	define("GAMEME_CLIENTAPI_MAXIMUM_LIMIT", 1000);
	define("GAMEME_CLIENTAPI_MULTIPLE_SERVERINFO_LIMIT", 32);
	define("GAMEME_CLIENTAPI_MULTIPLE_PLAYERINFO_LIMIT", 64);


	/**
	 * Special packet information
	 */
	define("GAMEME_DATA_DEFAULT",  -1);
	define("GAMEME_DATA_STATUS",    1);
	define("GAMEME_DATA_PLAYERS",   2);
	define("GAMEME_DATA_ALIASES",   3);
	define("GAMEME_DATA_AWARDS",    4);
	define("GAMEME_DATA_MAPS",      5);
	define("GAMEME_DATA_ROLES",     6);
	define("GAMEME_DATA_TARGETS",   7);
	define("GAMEME_DATA_TEAMS",     8);
	define("GAMEME_DATA_WEAPONS",   9);
	define("GAMEME_DATA_EXTENDED", 10);


	/**
	 * Available games
	 */
	define("GAMEME_GAME_NONE",    -1);
	define("GAMEME_GAME_CSS",      1);
	define("GAMEME_GAME_TF2",      2);
	define("GAMEME_GAME_DODS",     3);
	define("GAMEME_GAME_MOH",      4);
	define("GAMEME_GAME_BFBC2",    5);
	define("GAMEME_GAME_L4D2",     6);
	define("GAMEME_GAME_CS16",     7);
	define("GAMEME_GAME_DOD13",    8);
	define("GAMEME_GAME_HL2MP",    9);
	define("GAMEME_GAME_CZERO",    10);
	define("GAMEME_GAME_INSMOD",   11);
	define("GAMEME_GAME_L4D",      12);
	define("GAMEME_GAME_TFC",      13);
	define("GAMEME_GAME_HL2CTF",   14);
	define("GAMEME_GAME_CSGO",     15);


	/**
	 * Available filters
	 */
	define("GAMEME_FILTER_NONE", 		-1);
	define("GAMEME_FILTER_NAME",      	 1);
	define("GAMEME_FILTER_MAP",      	 2);
	define("GAMEME_FILTER_ADDRESS",  	 3);
	define("GAMEME_FILTER_GAME", 	     4);
	define("GAMEME_FILTER_COUNTRY", 	 5);
	define("GAMEME_FILTER_CITY", 	     6);
	define("GAMEME_FILTER_UNIQUEID",	 7);
	define("GAMEME_FILTER_ONLINE",  	 8);
	define("GAMEME_FILTER_DATE",     	 9);
	define("GAMEME_FILTER_RIBBONCODE",	10);
	define("GAMEME_FILTER_RANKID",     	11);


	/**
	 * Available sort columns
	 */

	define("GAMEME_SORT_DEFAULT",     		 -1);
	define("GAMEME_SORT_NAME_ASC",            1);
	define("GAMEME_SORT_NAME_DESC",           2);
	define("GAMEME_SORT_ADDRESS_ASC",         3);
	define("GAMEME_SORT_ADDRESS_DESC",        4);
	define("GAMEME_SORT_GAME_ASC",            5);
	define("GAMEME_SORT_GAME_DESC",           6);
	define("GAMEME_SORT_MAP_ASC",             7);
	define("GAMEME_SORT_MAP_DESC",            8);
	define("GAMEME_SORT_SLOTS_ASC",           9);
	define("GAMEME_SORT_SLOTS_DESC",         10);
	define("GAMEME_SORT_POS_ASC",            11);
	define("GAMEME_SORT_POS_DESC",           12);
	define("GAMEME_SORT_SKILL_ASC",          13);
	define("GAMEME_SORT_SKILL_DESC",         14);
	define("GAMEME_SORT_KILLS_ASC",          15);
	define("GAMEME_SORT_KILLS_DESC",         16);
	define("GAMEME_SORT_DEATHS_ASC",         17);
	define("GAMEME_SORT_DEATHS_DESC",        18);
	define("GAMEME_SORT_HEADSHOTS_ASC",      19);
	define("GAMEME_SORT_HEADSHOTS_DESC",     20);
	define("GAMEME_SORT_ASSISTS_ASC",        21);
	define("GAMEME_SORT_ASSISTS_DESC",       22);
	define("GAMEME_SORT_WEAPON_ASC",         23);
	define("GAMEME_SORT_WEAPON_DESC",        24);
	define("GAMEME_SORT_ACTIVITY_ASC",       25);
	define("GAMEME_SORT_ACTIVITY_DESC",      26);
	define("GAMEME_SORT_TIME_ASC",           27);
	define("GAMEME_SORT_TIME_DESC",          28);
	define("GAMEME_SORT_SUICIDES_ASC",       29);
	define("GAMEME_SORT_SUICIDES_DESC",      30);
	define("GAMEME_SORT_SHOTS_ASC",          31);
	define("GAMEME_SORT_SHOTS_DESC",         32);
	define("GAMEME_SORT_HITS_ASC",           33);
	define("GAMEME_SORT_HITS_DESC",          34);
	define("GAMEME_SORT_KILLSTREAK_ASC",     35);
	define("GAMEME_SORT_KILLSTREAK_DESC",    36);
	define("GAMEME_SORT_DEATHSTREAK_ASC",    37);
	define("GAMEME_SORT_DEATHSTREAK_DESC",   38);
	define("GAMEME_SORT_ASSISTED_ASC",       39);
	define("GAMEME_SORT_ASSISTED_DESC",      40);
	define("GAMEME_SORT_TEAMKILLS_ASC",      41);
	define("GAMEME_SORT_TEAMKILLS_DESC",     42);
	define("GAMEME_SORT_TEAMKILLED_ASC",     43);
	define("GAMEME_SORT_TEAMKILLED_DESC",    44);
	define("GAMEME_SORT_HEALEDPOINTS_ASC",   45);
	define("GAMEME_SORT_HEALEDPOINTS_DESC",  46);
	define("GAMEME_SORT_FLAGSCAPTURED_ASC",  47);
	define("GAMEME_SORT_FLAGSCAPTURED_DESC", 48);
	define("GAMEME_SORT_CUSTOMWINS_ASC",     49);
	define("GAMEME_SORT_CUSTOMWINS_DESC",    50);
	define("GAMEME_SORT_ROUNDS_ASC",         51);
	define("GAMEME_SORT_ROUNDS_DESC",        52);
	define("GAMEME_SORT_WINS_ASC",           53);
	define("GAMEME_SORT_WINS_DESC",          54);
	define("GAMEME_SORT_LOSSES_ASC",         55);
	define("GAMEME_SORT_LOSSES_DESC",        56);
	define("GAMEME_SORT_SURVIVED_ASC",       57);
	define("GAMEME_SORT_SURVIVED_DESC",      58);


	/**
	 * Available hash columns
	 */
	define("GAMEME_HASH_NONE",     -1);
	define("GAMEME_HASH_ADDRESS",   1);
	define("GAMEME_HASH_UNIQUEID",  2);


	/**
	 * Default gameMEAPI Exception
 	 */
	class gameMEAPI_Exception extends Exception {}


	/**
	 * Abstract class to access gameME API interfaces
 	 */
	class gameMEAPI {

		/**
		 * Client API url
		 */
		public $client_api_url;

		/**
		 * GlobalAPI url
		 */
		public $global_api_url;

		/**
		 * Global API object
		 */
		private $global_api_object;

		/**
		 * Client API object
		 */
		private $client_api_object;


		function gameMEAPI($client_api_url = "", $global_api_url = GAMEME_GLOBALAPI_URL) {
			/**
			 * Setup client API url
			 */
			if (filter_var($client_api_url, FILTER_VALIDATE_URL) !== FALSE) {
				if (strpos($client_api_url, "/api") === FALSE) {
					$client_api_url .= "/api";
				}
				$this->client_api_url = $client_api_url;
			}
			$this->global_api_url = $global_api_url;
		}


		/**
		 * Client API wrapper
		 */

		private function check_client_api_object() {
			if (!$this->client_api_object) {
				$this->client_api_object = new gameMEClientAPI($this->client_api_url);
			}
		}


		public function client_api_serverlist($filter = GAMEME_FILTER_NONE, $filter_value = "", $limit = GAMEME_CLIENTAPI_DEFAULT_LIMIT, $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			$this->check_client_api_object();
			return $this->client_api_object->get_serverlist($filter, $filter_value, $limit, $sort_column, $hash_key);
		}


		public function client_api_serverinfo($address, $data = GAMEME_DATA_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			$this->check_client_api_object();
			return $this->client_api_object->get_serverinfo($address, $data, $hash_key);
		}


		public function client_api_playerlist($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "", $limit = GAMEME_CLIENTAPI_DEFAULT_LIMIT, $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			$this->check_client_api_object();
			return $this->client_api_object->get_playerlist($ranking, $filter, $filter_value, $limit, $sort_column, $hash_key);
		}


		public function client_api_full_playerlist($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "", $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			$this->check_client_api_object();
			return $this->client_api_object->get_full_playerlist($ranking, $filter, $filter_value, $sort_column, $hash_key);
		}


		public function client_api_playerinfo($ranking, $uniqueid, $data = GAMEME_DATA_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			$this->check_client_api_object();
			return $this->client_api_object->get_playerinfo($ranking, $uniqueid, $data, $hash_key);
		}


		public function client_api_voiceserver_status() {
			$this->check_client_api_object();
			return $this->client_api_object->get_voiceserver_status();
		}


		public function client_api_awards($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "") {
			$this->check_client_api_object();
			return $this->client_api_object->get_awards($ranking, $filter, $filter_value);
		}


		public function client_api_ribbons($ranking) {
			$this->check_client_api_object();
			return $this->client_api_object->get_ribbons($ranking);
		}


		public function client_api_ribboninfo($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "") {
			$this->check_client_api_object();
			return $this->client_api_object->get_ribboninfo($ranking, $filter, $filter_value);
		}


		public function client_api_ranks($ranking) {
			$this->check_client_api_object();
			return $this->client_api_object->get_ranks($ranking);
		}


		public function client_api_rankinfo($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "") {
			$this->check_client_api_object();
			return $this->client_api_object->get_rankinfo($ranking, $filter, $filter_value);
		}


		/**
		 * Global API wrapper
		 */

		private function check_global_api_object() {
			if (!$this->global_api_object) {
				$this->global_api_object = new gameMEGlobalAPI($this->global_api_url);
			}
		}


		public function global_api_serverlist($filter = GAMEME_FILTER_NONE, $filter_value = "", $limit = GAMEME_GLOBALAPI_DEFAULT_LIMIT, $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			$this->check_global_api_object();
			return $this->global_api_object->get_serverlist($filter, $filter_value, $limit, $sort_column, $hash_key);
		}


		public function global_api_serverinfo($address, $data = GAMEME_DATA_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			$this->check_global_api_object();
			return $this->global_api_object->get_serverinfo($address, $data, $hash_key);
		}


		public function global_api_playerlist($game, $filter = GAMEME_FILTER_NONE, $filter_value = "", $limit = GAMEME_GLOBALAPI_DEFAULT_LIMIT, $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			$this->check_global_api_object();
			return $this->global_api_object->get_playerlist($game, $filter, $filter_value, $limit, $sort_column, $hash_key);
		}


		public function global_api_playerinfo($game, $uniqueid, $data = GAMEME_DATA_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			$this->check_global_api_object();
			return $this->global_api_object->get_playerinfo($game, $uniqueid, $data, $hash_key);
		}

	}


	/**
	 * Class to access global gameME Stats API
	 * Docs: http://www.gameme.com/docs/api/globalapi
	 */
	class gameMEGlobalAPI {

		/**
		 * Global API url
		 */
		private $url;

		/**
		 * Allowed global API games
		 */
		private $game_filters = array(
			GAMEME_GAME_CSGO    => "csgo",
			GAMEME_GAME_CSS     => "css",
			GAMEME_GAME_TF2     => "tf2",
			GAMEME_GAME_DODS    => "dods",
			GAMEME_GAME_MOH     => "moh",
			GAMEME_GAME_BFBC2   => "bcii",
			GAMEME_GAME_L4D2    => "l4dii",
			GAMEME_GAME_CS16    => "cs16",
			GAMEME_GAME_DOD13   => "dod13",
			GAMEME_GAME_HL2MP   => "hl2mp",
			GAMEME_GAME_CZERO   => "czero",
			GAMEME_GAME_INSMOD  => "insmod",
			GAMEME_GAME_L4D     => "l4d",
			GAMEME_GAME_TFC     => "tfc",
			GAMEME_GAME_HL2CTF  => "hl2ctf"
		);

 		/**
		 * Allowed serverlist filters
		 */
		private $serverlist_filters = array(
			GAMEME_FILTER_NAME     => "name",
			GAMEME_FILTER_MAP      => "map",
			GAMEME_FILTER_ADDRESS  => "address",
			GAMEME_FILTER_GAME     => "game",
			GAMEME_FILTER_COUNTRY  => "country",
			GAMEME_FILTER_CITY     => "city"
		);

 		/**
		 * Allowed serverinfo filters
		 */
		private $serverinfo_filters = array(
		);

		/**
		 * Allowed serverinfo packet data
		 */
		private $serverinfo_packet_data = array(
			GAMEME_DATA_STATUS   => "status",
			GAMEME_DATA_PLAYERS  => "players"
		);

		/**
		 * Allowed playerlist filters
		 */
		private $playerlist_filters = array(
			GAMEME_FILTER_UNIQUEID => "uniqueid",
			GAMEME_FILTER_NAME     => "name",
			GAMEME_FILTER_COUNTRY  => "country",
			GAMEME_FILTER_ONLINE   => "online"
		);

 		/**
		 * Allowed playerinfo filters
		 */
		private $playerinfo_filters = array(
		);

		/**
		 * Allowed playerinfo packet data
		 */
		private $playerinfo_packet_data = array(
			GAMEME_DATA_STATUS   => "status",
		);


		/**
		 * Default cache times
		 */
		private $cache_times = array(
			"serverlist" =>  60,
			"serverinfo" =>  60,
			"playerlist" => 300,
			"playerinfo" => 300
		);



		function gameMEGlobalAPI($url = GAMEME_GLOBALAPI_URL) {
			$this->url = $url;
		}


		public function get_serverlist($filter = GAMEME_FILTER_NONE, $filter_value = "", $limit = GAMEME_GLOBALAPI_DEFAULT_LIMIT, $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			return $this->get_result("serverlist", $this->serverlist_filters, $filter, $filter_value, $limit, $sort_column, $hash_key, $this->cache_times['serverlist']);
		}


		public function get_serverinfo($address, $data = GAMEME_DATA_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			if ($address == "") {
				throw new gameMEAPI_Exception("No valid address given: ".$address);
			}
			if (is_array($address)) {
				$address = join("/", $address);
			}
			if (($data > GAMEME_DATA_DEFAULT) && (isset($this->serverinfo_packet_data[$data]))) {
				return $this->get_result("serverinfo/".$address."/".$this->serverinfo_packet_data[$data], $this->serverinfo_filters, GAMEME_FILTER_NONE, "", GAMEME_GLOBALAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, $hash_key, $this->cache_times['serverinfo']);
			} else {
				return $this->get_result("serverinfo/".$address, $this->serverinfo_filters, GAMEME_FILTER_NONE, "", GAMEME_GLOBALAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, $hash_key, $this->cache_times['serverinfo']);
			}
		}


		public function get_playerlist($game, $filter = GAMEME_FILTER_NONE, $filter_value = "", $limit = GAMEME_GLOBALAPI_DEFAULT_LIMIT, $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			if (!isset($this->game_filters[$game])) {
				throw new gameMEAPI_Exception("No valid game given: ".$game);
			}
			return $this->get_result("playerlist/".$this->game_filters[$game], $this->playerlist_filters, $filter, $filter_value, $limit, $sort_column, $hash_key, $this->cache_times['playerlist']);
		}


		public function get_playerinfo($game, $uniqueid, $data = GAMEME_DATA_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			if (!isset($this->game_filters[$game])) {
				throw new gameMEAPI_Exception("No valid game given: ".$game);
			}
			if (is_array($uniqueid)) {
				$uniqueid = join("/", $uniqueid);
			}
			if ($uniqueid == "") {
				throw new gameMEAPI_Exception("No unique-id given");
			}
			if (($data > GAMEME_DATA_DEFAULT) && (isset($this->playerinfo_packet_data[$data]))) {
				return $this->get_result("playerinfo/".$this->game_filters[$game]."/".$uniqueid."/".$this->playerinfo_packet_data[$data], $this->playerinfo_filters, GAMEME_FILTER_NONE, "", GAMEME_GLOBALAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, $hash_key, $this->cache_times['playerinfo']);
			} else {
				return $this->get_result("playerinfo/".$this->game_filters[$game]."/".$uniqueid, $this->playerinfo_filters, GAMEME_FILTER_NONE, "", GAMEME_GLOBALAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, $hash_key, $this->cache_times['playerinfo']);
			}
		}


		private function get_result($command, $filters, $filter, $filter_value, $limit, $sort_column, $hash_key, $cache_time = 0, $pagination = array()) {
			$content_url = "";
			if (($filter > GAMEME_FILTER_NONE) && (isset($filters[$filter])) && ($filter_value != "")) {
				if ($filters[$filter] == "online") {
					$filter_value = 1;
				}
				$content_url = $this->url."/".$command."/".$filters[$filter]."/".$filter_value."/?limit=".$limit;
			} else {
				$content_url = $this->url."/".$command."/?limit=".$limit;
			}

			if ((GAMEME_APICACHE_ENABLED == 1) && ($cache_time > 0)) {
				$cache_identifier = md5($command.$filters.$filter.$filter_value.$limit.$sort_column.$hash_key);
				$content_cache = new gameMECache();
				if ($content_cache->is_hit($cache_identifier, $cache_time)) {
					$xml_parser_result = $content_cache->get($cache_identifier);
					return $xml_parser_result;
				} else {
					$xml_parser = new gameMEXMLParser($content_url, $sort_column, $hash_key);
					$content_cache->add($cache_identifier, $xml_parser->result);
				}
			} else {
				$xml_parser = new gameMEXMLParser($content_url, $sort_column, $hash_key);
			}
			return $xml_parser->result;
		}

	}



	/**
	 * Class to access client gameME Stats API
	 * Docs: http://www.gameme.com/docs/api/clientapi
	 */

	class gameMEClientAPI {

		/**
		 * Client API url
		 */
		private $url;

 		/**
		 * Allowed serverlist filters
		 */
		private $serverlist_filters = array(
			GAMEME_FILTER_NAME     => "name",
			GAMEME_FILTER_MAP      => "map",
			GAMEME_FILTER_ADDRESS  => "address",
			GAMEME_FILTER_GAME     => "game",
			GAMEME_FILTER_COUNTRY  => "country"
		);

 		/**
		 * Allowed serverinfo filters
		 */
		private $serverinfo_filters = array(
		);

		/**
		 * Allowed serverinfo packet data
		 */
		private $serverinfo_packet_data = array(
			GAMEME_DATA_STATUS   => "status",
			GAMEME_DATA_PLAYERS  => "players"
		);

		/**
		 * Allowed playerlist filters
		 */
		private $playerlist_filters = array(
			GAMEME_FILTER_UNIQUEID => "uniqueid",
			GAMEME_FILTER_NAME     => "name",
			GAMEME_FILTER_COUNTRY  => "country",
			GAMEME_FILTER_ONLINE   => "online"
		);

 		/**
		 * Allowed playerinfo filters
		 */
		private $playerinfo_filters = array(
		);


		/**
		 * Allowed awards filters
		 */
		private $awards_filters = array(
			GAMEME_FILTER_DATE  => "date"
		);

		/**
		 * Allowed ribboninfo filters
		 */
		private $ribboninfo_filters = array(
			GAMEME_FILTER_RIBBONCODE  => "code"
		);

		/**
		 * Allowed rankinfo filters
		 */
		private $rankinfo_filters = array(
			GAMEME_FILTER_RANKID  => "id"
		);


		/**
		 * Allowed playerinfo packet data
		 */
		private $playerinfo_packet_data = array(
			GAMEME_DATA_STATUS   => "status",
			GAMEME_DATA_ALIASES  => "aliases",
			GAMEME_DATA_AWARDS   => "awards",
			GAMEME_DATA_MAPS     => "maps",
			GAMEME_DATA_ROLES    => "roles",
			GAMEME_DATA_TARGETS  => "targets",
			GAMEME_DATA_TEAMS    => "teams",
			GAMEME_DATA_WEAPONS  => "weapons",
			GAMEME_DATA_EXTENDED => "extended",
		);


		/**
		 * Default cache times
		 */
		private $cache_times = array(
			"serverlist"  =>  60,
			"serverinfo"  =>  60,
			"playerlist"  => 300,
			"playerinfo"  => 300,
			"voiceserver" => 300,
			"awards"      => 600,
			"ribbons"     => 600,
			"ribboninfo"  => 600,
			"ranks"       => 600,
			"rankinfo"    => 600
		);


		function gameMEClientAPI($client_api_url) {
			$this->url = $client_api_url;
		}


		public function get_serverlist($filter = GAMEME_FILTER_NONE, $filter_value = "", $limit = GAMEME_CLIENTAPI_DEFAULT_LIMIT, $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			return $this->get_result("serverlist", $this->serverlist_filters, $filter, $filter_value, $limit, $sort_column, $hash_key, $this->cache_times['serverlist']);
		}


		public function get_serverinfo($address, $data = GAMEME_DATA_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			if ($address == "") {
				throw new gameMEAPI_Exception("No valid address given: ".$address);
			}
			if (is_array($address)) {
				$address = join("/", $address);
			}
			if (($data > GAMEME_DATA_DEFAULT) && (isset($this->serverinfo_packet_data[$data]))) {
				return $this->get_result("serverinfo/".$address."/".$this->serverinfo_packet_data[$data], $this->serverinfo_filters, GAMEME_FILTER_NONE, "", GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, $hash_key, $this->cache_times['serverinfo']);
			} else {
				return $this->get_result("serverinfo/".$address, $this->serverinfo_filters, GAMEME_FILTER_NONE, "", GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, $hash_key, $this->cache_times['serverinfo']);
			}
		}


		public function get_playerlist($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "", $limit = GAMEME_CLIENTAPI_DEFAULT_LIMIT, $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			if ($ranking == "") {
				throw new gameMEAPI_Exception("No valid ranking given");
			}
			return $this->get_result("playerlist/".$ranking, $this->playerlist_filters, $filter, $filter_value, $limit, $sort_column, $hash_key, $this->cache_times['playerinfo']);
		}


		public function get_full_playerlist($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "", $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			if ($ranking == "") {
				throw new gameMEAPI_Exception("No valid ranking given");
			}
			return $this->get_result("playerlist/".$ranking, $this->playerlist_filters, $filter, $filter_value, GAMEME_CLIENTAPI_MAXIMUM_LIMIT, $sort_column, $hash_key, $this->cache_times['playerlist'], array("playerlist", "player"));
		}


		public function get_playerinfo($ranking, $uniqueid, $data = GAMEME_DATA_DEFAULT, $hash_key = GAMEME_HASH_NONE) {
			if ($ranking == "") {
				throw new gameMEAPI_Exception("No valid ranking given");
			}
			if (is_array($uniqueid)) {
				$uniqueid = join("/", $uniqueid);
			}
			if ($uniqueid == "") {
				throw new gameMEAPI_Exception("No unique-id given");
			}
			if (($data > GAMEME_DATA_DEFAULT) && (isset($this->playerinfo_packet_data[$data]))) {
				return $this->get_result("playerinfo/".$ranking."/".$uniqueid."/".$this->playerinfo_packet_data[$data], $this->playerinfo_filters, GAMEME_FILTER_NONE, "", GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, $hash_key, $this->cache_times['playerinfo']);
			} else {
				return $this->get_result("playerinfo/".$ranking."/".$uniqueid, $this->playerinfo_filters, GAMEME_FILTER_NONE, "", GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, $hash_key, $this->cache_times['playerinfo']);
			}
		}


		public function get_voiceserver_status() {
			return $this->get_result("voiceserver", "", GAMEME_FILTER_NONE, "", GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, "", $this->cache_times['voiceserver']);
		}


		public function get_awards($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "") {
			if ($ranking == "") {
				throw new gameMEAPI_Exception("No valid ranking given");
			}
			return $this->get_result("awards/".$ranking, $this->awards_filters, $filter, $filter_value, GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, "", $this->cache_times['awards']);
		}


		public function get_ribbons($ranking) {
			if ($ranking == "") {
				throw new gameMEAPI_Exception("No valid ranking given");
			}
			return $this->get_result("ribbons/".$ranking, "", GAMEME_FILTER_NONE, "", GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, "", $this->cache_times['ribbons']);
		}


		public function get_ribboninfo($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "") {
			if ($ranking == "") {
				throw new gameMEAPI_Exception("No valid ranking given");
			}
			return $this->get_result("ribboninfo/".$ranking, $this->ribboninfo_filters, $filter, $filter_value, GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, "", $this->cache_times['ribboninfo']);
		}


		public function get_ranks($ranking) {
			if ($ranking == "") {
				throw new gameMEAPI_Exception("No valid ranking given");
			}
			return $this->get_result("ranks/".$ranking, "", GAMEME_FILTER_NONE, "", GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, "", $this->cache_times['ranks']);
		}


		public function get_rankinfo($ranking, $filter = GAMEME_FILTER_NONE, $filter_value = "") {
			if ($ranking == "") {
				throw new gameMEAPI_Exception("No valid ranking given");
			}
			return $this->get_result("rankinfo/".$ranking, $this->rankinfo_filters, $filter, $filter_value, GAMEME_CLIENTAPI_DEFAULT_LIMIT, GAMEME_SORT_DEFAULT, "", $this->cache_times['rankinfo']);
		}


		private function get_result($command, $filters, $filter, $filter_value, $limit, $sort_column, $hash_key, $cache_time = 0, $pagination = array()) {
			$content_url = "";
			if (($filter > GAMEME_FILTER_NONE) && (isset($filters[$filter])) && ($filter_value != "")) {
				if ($filters[$filter] == "online") {
					$filter_value = 1;
				}
				$content_url = $this->url."/".$command."/".$filters[$filter]."/".$filter_value."/?limit=".$limit;
			} else {
				$content_url = $this->url."/".$command."/?limit=".$limit;
			}

			if ((GAMEME_APICACHE_ENABLED == 1) && ($cache_time > 0)) {
				$cache_identifier = md5($command.$filters.$filter.$filter_value.$limit.$sort_column.$hash_key.$pagination);
				$content_cache = new gameMECache();
				if ($content_cache->is_hit($cache_identifier, $cache_time)) {
					$xml_parser_result = $content_cache->get($cache_identifier);
					return $xml_parser_result;
				} else {
					$xml_parser = new gameMEXMLParser($content_url, $sort_column, $hash_key, $pagination);
					$content_cache->add($cache_identifier, $xml_parser->result);
				}
			} else {
				$xml_parser = new gameMEXMLParser($content_url, $sort_column, $hash_key, $pagination);
			}
			return $xml_parser->result;
		}

	}



	/**
	 * Class to parse xml
	 */

	class gameMEXMLParser {

		/**
		 * Private xml content
		 */
		private $xml;

		/**
		 * Private parsed xml content
		 */
		private $parsed_xml;

		/**
		 * Parser result
		 */
		public $result = array();

		/**
		 * Available sort columns
		 */
		private $sort_columns = array(
			GAMEME_SORT_NAME_ASC => array(
				"data" => "name",
				"type" => "asc"
			),
			GAMEME_SORT_NAME_DESC => array(
				"data" => "name",
				"type" => "desc"
			),
			GAMEME_SORT_ADDRESS_ASC => array(
				"data" => "address",
				"type" => "asc"
			),
			GAMEME_SORT_ADDRESS_DESC => array(
				"data" => "address",
				"type" => "desc"
			),
			GAMEME_SORT_GAME_ASC => array(
				"data" => "game",
				"type" => "asc"
			),
			GAMEME_SORT_GAME_DESC => array(
				"data" => "game",
				"type" => "desc"
			),
			GAMEME_SORT_MAP_ASC => array(
				"data" => "map",
				"type" => "asc"
			),
			GAMEME_SORT_MAP_DESC => array(
				"data" => "map",
				"type" => "desc"
			),
			GAMEME_SORT_SLOTS_ASC => array(
				"data" => "act",
				"type" => "asc"
			),
			GAMEME_SORT_SLOTS_DESC => array(
				"data" => "act",
				"type" => "desc"
			),
			GAMEME_SORT_POS_ASC => array(
				"data" => "rank",
				"type" => "asc"
			),
			GAMEME_SORT_POS_DESC => array(
				"data" => "rank",
				"type" => "desc"
			),
			GAMEME_SORT_SKILL_ASC => array(
				"data" => "skill",
				"type" => "asc"
			),
			GAMEME_SORT_SKILL_DESC => array(
				"data" => "skill",
				"type" => "desc"
			),
			GAMEME_SORT_KILLS_ASC => array(
				"data" => "kills",
				"type" => "asc"
			),
			GAMEME_SORT_KILLS_DESC => array(
				"data" => "kills",
				"type" => "desc"
			),
			GAMEME_SORT_DEATHS_ASC => array(
				"data" => "deaths",
				"type" => "asc"
			),
			GAMEME_SORT_DEATHS_DESC => array(
				"data" => "deaths",
				"type" => "desc"
			),
			GAMEME_SORT_HEADSHOTS_ASC => array(
				"data" => "hs",
				"type" => "asc"
			),
			GAMEME_SORT_HEADSHOTS_DESC => array(
				"data" => "hs",
				"type" => "desc"
			),
			GAMEME_SORT_ASSISTS_ASC => array(
				"data" => "assists",
				"type" => "asc"
			),
			GAMEME_SORT_ASSISTS_DESC => array(
				"data" => "assists",
				"type" => "desc"
			),
			GAMEME_SORT_WEAPON_ASC => array(
				"data" => "weapon",
				"type" => "asc"
			),
			GAMEME_SORT_WEAPON_DESC => array(
				"data" => "weapon",
				"type" => "desc"
			),
			GAMEME_SORT_ACTIVITY_ASC => array(
				"data" => "activity",
				"type" => "asc"
			),
			GAMEME_SORT_ACTIVITY_DESC => array(
				"data" => "activity",
				"type" => "desc"
			),
			GAMEME_SORT_TIME_ASC => array(
				"data" => "time",
				"type" => "asc"
			),
			GAMEME_SORT_TIME_DESC => array(
				"data" => "time",
				"type" => "desc"
			),
			GAMEME_SORT_SUICIDES_ASC => array(
				"data" => "suicides",
				"type" => "asc"
			),
			GAMEME_SORT_SUICIDES_DESC => array(
				"data" => "suicides",
				"type" => "desc"
			),
			GAMEME_SORT_SHOTS_ASC => array(
				"data" => "shots",
				"type" => "asc"
			),
			GAMEME_SORT_SHOTS_DESC => array(
				"data" => "shots",
				"type" => "desc"
			),
			GAMEME_SORT_HITS_ASC => array(
				"data" => "hits",
				"type" => "asc"
			),
			GAMEME_SORT_HITS_DESC => array(
				"data" => "hits",
				"type" => "desc"
			),
			GAMEME_SORT_KILLSTREAK_ASC => array(
				"data" => "killstreak",
				"type" => "asc"
			),
			GAMEME_SORT_KILLSTREAK_DESC => array(
				"data" => "killstreak",
				"type" => "desc"
			),
			GAMEME_SORT_DEATHSTREAK_ASC => array(
				"data" => "deathstreak",
				"type" => "asc"
			),
			GAMEME_SORT_DEATHSTREAK_DESC => array(
				"data" => "deathstreak",
				"type" => "desc"
			),
			GAMEME_SORT_ASSISTED_ASC => array(
				"data" => "assisted",
				"type" => "asc"
			),
			GAMEME_SORT_ASSISTED_DESC => array(
				"data" => "assisted",
				"type" => "desc"
			),
			GAMEME_SORT_TEAMKILLS_ASC => array(
				"data" => "teamkills",
				"type" => "asc"
			),
			GAMEME_SORT_TEAMKILLS_DESC => array(
				"data" => "teamkills",
				"type" => "desc"
			),
			GAMEME_SORT_TEAMKILLED_ASC => array(
				"data" => "teamkilled",
				"type" => "asc"
			),
			GAMEME_SORT_TEAMKILLED_DESC => array(
				"data" => "teamkilled",
				"type" => "desc"
			),
			GAMEME_SORT_HEALEDPOINTS_ASC => array(
				"data" => "healedpoints",
				"type" => "asc"
			),
			GAMEME_SORT_HEALEDPOINTS_DESC => array(
				"data" => "healedpoints",
				"type" => "desc"
			),
			GAMEME_SORT_FLAGSCAPTURED_ASC => array(
				"data" => "flagscaptured",
				"type" => "asc"
			),
			GAMEME_SORT_FLAGSCAPTURED_DESC => array(
				"data" => "flagscaptured",
				"type" => "desc"
			),
			GAMEME_SORT_CUSTOMWINS_ASC => array(
				"data" => "customwins",
				"type" => "asc"
			),
			GAMEME_SORT_CUSTOMWINS_DESC => array(
				"data" => "customwins",
				"type" => "desc"
			),
			GAMEME_SORT_ROUNDS_ASC => array(
				"data" => "rounds",
				"type" => "asc"
			),
			GAMEME_SORT_ROUNDS_DESC => array(
				"data" => "rounds",
				"type" => "desc"
			),
			GAMEME_SORT_WINS_ASC => array(
				"data" => "wins",
				"type" => "asc"
			),
			GAMEME_SORT_WINS_DESC => array(
				"data" => "wins",
				"type" => "desc"
			),
			GAMEME_SORT_LOSSES_ASC => array(
				"data" => "losses",
				"type" => "asc"
			),
			GAMEME_SORT_LOSSES_DESC => array(
				"data" => "losses",
				"type" => "desc"
			),
			GAMEME_SORT_SURVIVED_ASC => array(
				"data" => "survived",
				"type" => "asc"
			),
			GAMEME_SORT_SURVIVED_DESC => array(
				"data" => "survived",
				"type" => "desc"
			),
		);


		/**
		 * Current sort column
		 */
		 private $sort_column;


		/**
		 * Available hash keys
		 */
		private $hash_keys = array(
			GAMEME_HASH_ADDRESS => array(
				"delimiter" => ":",
				"data"   => array(
					"addr",
					"port"
				)
			),
			GAMEME_HASH_UNIQUEID => "uniqueid"
		);

		/**
		 * Private stream context for compressed transmission
		 */
		private $stream_context;


		function gameMEXMLParser($url, $sort_column = GAMEME_SORT_DEFAULT, $hash_key = GAMEME_HASH_NONE, $pagination = array()) {
			try {
				$this->xml = $this->fetch_content($url);
				if ($this->xml != "") {

					$this->parsed_xml = new SimpleXMLElement($this->xml, LIBXML_NOCDATA);
					$this->result = $this->prepare_result($this->parsed_xml, $sort_column, $hash_key);

					// check errors
					if (isset($this->result['error'])) {
						if ((isset($this->result['error']['type'])) && (isset($this->result['error']['description']))) {
							throw new gameMEAPI_Exception("An error occurs: ".$this->result['error']['description']." [".$this->result['error']['type']."]");
						} else {
							throw new gameMEAPI_Exception("Unknown error occurs!");
						}
					}

					// check pagination
					if ((count($pagination) == 2) && ($pagination[0] != "") && ($pagination[1] != ""))  {
						$pagination_definition = $pagination[0];
						$pagination_result = $pagination[1];

						if (isset($this->parsed_xml->$pagination_definition->pagination)) {
							$next_page_link = $this->parsed_xml->$pagination_definition->pagination->nextpagelink;
							while ($next_page_link != "") {
								$next_page_xml = $this->fetch_content($next_page_link);

								if ($next_page_xml != "") {
									$next_page_result = new SimpleXMLElement($next_page_xml, LIBXML_NOCDATA);
									if (isset($next_page_result->$pagination_definition->pagination)) {
										$next_page_link = $next_page_result->$pagination_definition->pagination->nextpagelink;
									} else {
										$next_page_link = "";
									}
									$next_page_result = $this->prepare_result($next_page_result, $sort_column, $hash_key);
									if ((isset($next_page_result[$pagination_definition])) && (is_array($next_page_result[$pagination_definition])) && (count($next_page_result[$pagination_definition]) > 0)) {
										$this->result[$pagination_definition] = array_merge($this->result[$pagination_definition], $next_page_result[$pagination_definition]);
									}
								} else {
									$next_page_link = "";
									throw new gameMEAPI_Exception("Cannot retrieve next page xml at url ".$next_page_link);
								}
							}

							// sort results
	                     	if (($sort_column > GAMEME_SORT_DEFAULT) && (isset($this->sort_columns[$sort_column]))) {
								$this->sort_column = $this->sort_columns[$sort_column]['data'];
								if (($hash_key > GAMEME_HASH_NONE) && (isset($this->hash_keys[$hash_key]))) {
									if ($this->sort_columns[$sort_column]['type'] == "asc") {
										uasort($this->result[$pagination_definition], array($this, 'compare_asc'));
									} elseif ($this->sort_columns[$sort_column]['type'] == "desc") {
										uasort($this->result[$pagination_definition], array($this, 'compare_desc'));
									}
								} else {
									if ($this->sort_columns[$sort_column]['type'] == "asc") {
										usort($this->result[$pagination_definition], array($this, 'compare_asc'));
									} elseif ($this->sort_columns[$sort_column]['type'] == "desc") {
										usort($this->result[$pagination_definition], array($this, 'compare_desc'));
									}
								}
	                     	}

						}
					}
				}
			} catch (Exception $e) {
				throw new gameMEAPI_Exception("Cannot retrieve xml at url ".$url." [".$e->getMessage()."]");
			}
		}

		private function fetch_content($url) {
			if (GAMEME_FETCHDATA_COMPRESSED == 1) {
				if (!$this->stream_context) {
					$context_options = array(
  						'http'=>array(
    						'method' => "GET",
    						'header' => "Accept-Encoding: gzip;\r\n"
						)
					);
					$this->stream_context = stream_context_create($context_options);
					if(!function_exists("gzdecode")) {
						return $this->gzdecode(file_get_contents($url, false, $this->stream_context));

					} else {
						return gzdecode(file_get_contents($url, false, $this->stream_context));
					}
				}

			}
			return file_get_contents($url);
		}

		private function compare_asc($a, $b) {
		    if ($a[$this->sort_column] == $b[$this->sort_column]) {
        		return 0;
    		}
    		return ($a[$this->sort_column] < $b[$this->sort_column]) ? -1 : 1;
		}

		private function compare_desc($a, $b) {
		    if ($a[$this->sort_column] == $b[$this->sort_column]) {
        		return 0;
    		}
    		return ($a[$this->sort_column] > $b[$this->sort_column]) ? -1 : 1;
		}


		private function prepare_result($object, $sort_column, $hash_key, $level = 0) {
			$result_items = array();

			if (!is_object($object)) {
				return $result_items;
			}

    		$child = (array)$object;

		    if (sizeof($child) > 0) {
		         foreach($child as $key => $entry) {
             		if (is_array($entry)) {
                 		foreach($entry as $entry_key => $entry_entry) {
                     		if (!is_object($entry_entry)) {
                         		$result_items[$entry_key] = $entry_entry;
                     		} else {
                     			if (get_class($entry_entry) == 'SimpleXMLElement') {
                     				if (($level == 0) && (($hash_key > GAMEME_HASH_NONE) && (isset($this->hash_keys[$hash_key])))) {
                     					$array_hash_key = "";
                     					if (is_array($this->hash_keys[$hash_key])) {
	                     					$array_hash_keys = array();
                     						foreach($this->hash_keys[$hash_key]['data'] as $data) {
                     							if ((string)$entry_entry->$data != "") {
			                     					$array_hash_keys[] = (string)$entry_entry->$data;
                     							}
                     						}
                     						$array_hash_key = join($this->hash_keys[$hash_key]['delimiter'], $array_hash_keys);
                     					} else {
	                     					$array_hash_key = (string)$entry_entry->{$this->hash_keys[$hash_key]};
                     					}
                     					if ($array_hash_key == "") {
		                         			$result_items[$entry_key] = $this->prepare_result($entry_entry, $sort_column, $hash_key, $level + 1);
                     					} else {
		                         			$result_items[$array_hash_key] = $this->prepare_result($entry_entry, $sort_column, $hash_key, $level + 1);
                     					}
                     				} else {
	                         			$result_items[$entry_key] = $this->prepare_result($entry_entry, $sort_column, $hash_key, $level + 1);
	                         		}
								}
                     		}
                     	}

                     	if (($sort_column > GAMEME_SORT_DEFAULT) && (isset($this->sort_columns[$sort_column]))) {
							$this->sort_column = $this->sort_columns[$sort_column]['data'];
							if (($hash_key > GAMEME_HASH_NONE) && (isset($this->hash_keys[$hash_key]))) {
								if ($this->sort_columns[$sort_column]['type'] == "asc") {
									uasort($result_items, array($this, 'compare_asc'));
								} elseif ($this->sort_columns[$sort_column]['type'] == "desc") {
									uasort($result_items, array($this, 'compare_desc'));
								}
							} else {
								if ($this->sort_columns[$sort_column]['type'] == "asc") {
									usort($result_items, array($this, 'compare_asc'));
								} elseif ($this->sort_columns[$sort_column]['type'] == "desc") {
									usort($result_items, array($this, 'compare_desc'));
								}
							}
                     	}
             		} else {
						if (!is_object($entry)) {
							$result_items[$key] = $entry;
            			} else {
							if (get_class($entry) == 'SimpleXMLElement') {
								if ($key == "pagination") {
									continue;
								}
								$allowed_new_columns = array(
									"vendor"      => 1,
									"software"    => 1,
									"account"     => 1,
									"error"       => 1,
									"globalinfo"  => 1,
									"rankinginfo" => 1,
									"serverinfo"  => 1,
									"serverlist"  => 1,
									"playerlist"  => 1,
									"playerinfo"  => 1,
									"voiceserver" => 1,
									"awards"      => 1,
									"ribbons"     => 1,
									"ribboninfo"  => 1,
									"ranks"       => 1,
									"rankinfo"    => 1
								);
								if ((isset($allowed_new_columns[$key])) || ($level > 0)) {
									$result_items[$key] = $this->prepare_result($entry, $sort_column, $hash_key);
								} else {
                     				if (($hash_key > GAMEME_HASH_NONE) && (isset($this->hash_keys[$hash_key]))) {
                     					$array_hash_key = "";
                     					if (is_array($this->hash_keys[$hash_key])) {
	                     					$array_hash_keys = array();
                     						foreach($this->hash_keys[$hash_key]['data'] as $data) {
                     							if ((string)$entry->$data != "") {
			                     					$array_hash_keys[] = (string)$entry->$data;
                     							}
                     						}
                     						$array_hash_key = join($this->hash_keys[$hash_key]['delimiter'], $array_hash_keys);
                     					} else {
	                     					$array_hash_key = (string)$entry->{$this->hash_keys[$hash_key]};
                     					}
                     					if ($array_hash_key == "") {
											$result_items[] = $this->prepare_result($entry, $sort_column, $hash_key, $level + 1);
                     					} else {
											$result_items[$array_hash_key] = $this->prepare_result($entry, $sort_column, $hash_key, $level + 1);
                     					}
                     				} else {
										$result_items[] = $this->prepare_result($entry, $sort_column, $hash_key, $level + 1);
	                         		}
								}
             				}
         				}
             		}
		         }
		    }

			if (count($result_items) == 0) {
				return "";
			} else {
				return $result_items;
			}
		}

		/**
		 * gzdecode function by katzlbtjunk
		 * http://de.php.net/manual/en/function.gzdecode.php
		 */
		private function gzdecode($data,&$filename='',&$error='',$maxlength = null) {

			$len = strlen($data);
			if ($len < 18 || strcmp(substr($data,0,2),"\x1f\x8b")) {
				$error = "Not in GZIP format.";
				return null;  // Not GZIP format (See RFC 1952)
			}
			$method = ord(substr($data,2,1));  // Compression method
			$flags  = ord(substr($data,3,1));  // Flags
			if ($flags & 31 != $flags) {
				$error = "Reserved bits not allowed.";
				return null;
			}
     		// NOTE: $mtime may be negative (PHP integer limitations)
     		$mtime = unpack("V", substr($data,4,4));
     		$mtime = $mtime[1];
     		$xfl   = substr($data,8,1);
     		$os    = substr($data,8,1);
     		$headerlen = 10;
     		$extralen  = 0;
     		$extra     = "";
     		if ($flags & 4) {
     			// 2-byte length prefixed EXTRA data in header
     			if ($len - $headerlen - 2 < 8) {
     				return false;  // invalid
     			}
     			$extralen = unpack("v",substr($data,8,2));
	     		$extralen = $extralen[1];
    	 		if ($len - $headerlen - 2 - $extralen < 8) {
     				return false;  // invalid
     			}
	     		$extra = substr($data,10,$extralen);
    	 		$headerlen += 2 + $extralen;
     		}
     		$filenamelen = 0;
     		$filename = "";
     		if ($flags & 8) {
     			// C-style string
     			if ($len - $headerlen - 1 < 8) {
     				return false; // invalid
     			}
     			$filenamelen = strpos(substr($data,$headerlen),chr(0));
     			if ($filenamelen === false || $len - $headerlen - $filenamelen - 1 < 8) {
     				return false; // invalid
     			}
     			$filename = substr($data,$headerlen,$filenamelen);
     			$headerlen += $filenamelen + 1;
     		}
     		$commentlen = 0;
     		$comment = "";
     		if ($flags & 16) {
     			// C-style string COMMENT data in header
     			if ($len - $headerlen - 1 < 8) {
     				return false;    // invalid
     			}
     			$commentlen = strpos(substr($data,$headerlen),chr(0));
     			if ($commentlen === false || $len - $headerlen - $commentlen - 1 < 8) {
     				return false;    // Invalid header format
     			}
     			$comment = substr($data,$headerlen,$commentlen);
     			$headerlen += $commentlen + 1;
     		}
     		$headercrc = "";
     		if ($flags & 2) {
				// 2-bytes (lowest order) of CRC32 on header present
     			if ($len - $headerlen - 2 < 8) {
     				return false;    // invalid
     			}
     			$calccrc = crc32(substr($data,0,$headerlen)) & 0xffff;
     			$headercrc = unpack("v", substr($data,$headerlen,2));
     			$headercrc = $headercrc[1];
     			if ($headercrc != $calccrc) {
					$error = "Header checksum failed.";
     				return false;    // Bad header CRC
     			}
     			$headerlen += 2;
     		}
			// GZIP FOOTER
     		$datacrc = unpack("V",substr($data,-8,4));
     		$datacrc = sprintf('%u',$datacrc[1] & 0xFFFFFFFF);
     		$isize = unpack("V",substr($data,-4));
     		$isize = $isize[1];
     		// decompression:
     		$bodylen = $len-$headerlen-8;
     		if ($bodylen < 1) {
     			// IMPLEMENTATION BUG!
     			return null;
     		}
     		$body = substr($data,$headerlen,$bodylen);
     		$data = "";
     		if ($bodylen > 0) {
     			switch ($method) {
     				case 8:
     					// Currently the only supported compression method:
     					$data = gzinflate($body,$maxlength);
     				break;
     			default:
     				$error = "Unknown compression method.";
     				return false;
     			}
     		}  // zero-byte body content is allowed
     		// Verifiy CRC32
     		$crc   = sprintf("%u",crc32($data));
     		$crcOK = $crc == $datacrc;
     		$lenOK = $isize == strlen($data);
     		if (!$lenOK || !$crcOK) {
     			$error = ( $lenOK ? '' : 'Length check FAILED. ') . ( $crcOK ? '' : 'Checksum FAILED.');
     			return false;
     		}
     		return $data;
     	}

 	}


	/**
	 * Class handling caching
	 */

	class gameMECache {

		/**
		 * Private cache directory
		 */
		private $prefix = "gameme_cache";

		/**
		 * Private cache directory
		 */
		private $suffix = ".cache";

		/**
		 * Private cache directory
		 */
		private $cache_dir = "";


		function gameMECache() {

			if (GAMEME_APICACHE_ENABLED == 1) {
				if (GAMEME_APICACHE_DIRECTORY != "") {
					$this->cache_dir = GAMEME_APICACHE_DIRECTORY;
				} else {
					if (!function_exists('sys_get_temp_dir')) {
						function sys_get_temp_dir() {
							if ($temp = getenv('TMP')) {
								return $temp;
							}
							if ($temp = getenv('TEMP')) {
								return $temp;
							}
							if ($temp = getenv('TMPDIR')) {
								return $temp;
							}
							$temp = tempnam(__FILE__, '');
							if (file_exists($temp)) {
								unlink($temp);
								return dirname($temp);
							}
							return null;
						}
					}
					$this->cache_dir = realpath(sys_get_temp_dir());
				}

				if ($this->cache_dir == "") {
					throw new gameMEAPI_Exception("Cannot setup cache directory");
				}
			}
		}


		function is_hit($identifier, $cache_time) {
			if ((GAMEME_APICACHE_ENABLED == 1) && ($identifier != "")) {
				$cache_file = $this->cache_dir."/".$this->prefix."_".$identifier.$this->suffix;
				if (file_exists($cache_file)) {
					$file_timestamp = filemtime($cache_file);
					if (($file_timestamp + $cache_time) >= time()) {
						return true;
					} else {
						$this->remove($identifier);
						return false;
					}
				} else {
					return false;
				}
			}
			return false;
		}


		function exists($identifier) {
			$data = "";
			if ((GAMEME_APICACHE_ENABLED == 1) && ($identifier != "")) {
				$cache_file = $this->cache_dir."/".$this->prefix."_".$identifier.$this->suffix;
				if (file_exists($cache_file)) {
					$data = $this->get($identifier);
				}
			}
			$this->cleanup();
			return $data;
		}


		function get($identifier) {
			$data = "";
			if ((GAMEME_APICACHE_ENABLED == 1) && ($identifier != "")) {
				$cache_file_name = $this->cache_dir."/".$this->prefix."_".$identifier.$this->suffix;
				$cache_file = fopen($cache_file_name, "r");
				$data = unserialize(fread($cache_file, GAMEME_APICACHE_MAX_SIZE_LIMIT));
				fclose($cache_file);
			}
			$this->cleanup();
			return $data;
		}


		function add($identifier, $data) {
			if ((GAMEME_APICACHE_ENABLED == 1) && ($identifier != "") && ($data != "")) {
				$cache_file_name = $this->cache_dir."/".$this->prefix."_".$identifier.$this->suffix;
				$cache_file = fopen($cache_file_name, "w+");
				fwrite($cache_file, serialize($data));
				fclose($cache_file);
				return true;
			}
			return false;
		}


		function remove($identifier) {
			if ((GAMEME_APICACHE_ENABLED == 1) && (GAMEME_APICACHE_AUTO_PRUNE == 1)) {
				$cache_file = $this->cache_dir."/".$this->prefix."_".$identifier.$this->suffix;
				if (file_exists($cache_file)) {
					unlink($cache_file);
				}
			}
			$this->cleanup();
		}


		function cleanup($force = 0) {
			if ((GAMEME_APICACHE_ENABLED == 1) && (GAMEME_APICACHE_AUTO_PRUNE == 1)) {
				if ((rand(1, 400) == 100) && ($force == 0)) {
					if (is_dir($this->cache_dir)) {
		    			if ($directory_handle = opendir($this->cache_dir)) {
							while (($file = readdir($directory_handle)) !== false) {
								if (($file != ".") && ($file != "..") && (strpos($file, $this->prefix) !== false)) {
									unlink($this->cache_dir."/".$file);
								}
							}
							closedir($directory_handle);
						}
					}
				}
			}
		}

	}


	/**
	 * Init SDK
	 */

	if ((!extension_loaded('SimpleXML')) && (!extension_loaded('simplexml'))) {
		throw new gameMEAPI_Exception("Extension SimpleXML is not loaded.");
	}
?>
