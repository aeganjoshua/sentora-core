<?php

/**
 *
 * ZPanel - A Cross-Platform Open-Source Web Hosting Control panel.
 * 
 * @package ZPanel
 * @version $Id$
 * @author Bobby Allen - ballen@zpanelcp.com
 * @copyright (c) 2008-2011 ZPanel Group - http://www.zpanelcp.com/
 * @license http://opensource.org/licenses/gpl-3.0.html GNU Public License v3
 *
 * This program (ZPanel) is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
 		$mailserver_db = ctrl_options::GetOption('mailserver_db');
		include('cnf/db.php');
		$z_db_user = $user;
		$z_db_pass = $pass;
		try {	
  			$mail_db = new db_driver("mysql:host=localhost;dbname=" . $mailserver_db . "", $z_db_user, $z_db_pass);
		} catch (PDOException $e) {
	
		}
		
		
		
		// Adding hMail DistList
		if (!fs_director::CheckForEmptyValue(self::$create)) {
		   	$result = $mail_db->query("SELECT domainid FROM hm_domains WHERE domainname='" . $inDomain . "'")->Fetch();
			if ($result) {
        		$domain_id = $result['domainid'];
        		$sql = "INSERT INTO hm_distributionlists (
		    						distributionlistdomainid,
									distributionlistaddress,
									distributionlistenabled,
									distributionlistrequireauth,
									distributionlistrequireaddress,
									distributionlistmode) VALUES (
									 " . $domain_id . ",
									 '" . $fulladdress . "',
									 1,
									 0,
									 '',
									 0)";
									 
				$sql = $mail_db->prepare($sql);
				$sql->execute();
			}
		}

		// Adding hMail DistListUser
		if (!fs_director::CheckForEmptyValue(self::$createuser)) {
	        $result = $mail_db->query("SELECT distributionlistid FROM hm_distributionlists WHERE distributionlistaddress='" . $rowdl['dl_address_vc'] . "'")->Fetch();
			if ($result) {	
                $sql = "INSERT INTO hm_distributionlistsrecipients (
									distributionlistrecipientlistid,
									distributionlistrecipientaddress) VALUES (
									" . $result['distributionlistid'] . ",
									'" . $fulladdress . "')";

				$sql = $mail_db->prepare($sql);
				$sql->execute();
			}			
		}

		// Deleting hMail DistList
		if (!fs_director::CheckForEmptyValue(self::$delete)) {
	        $result = $mail_db->query("SELECT distributionlistid FROM hm_distributionlists WHERE distributionlistaddress='" . $rowdl['dl_address_vc'] . "'")->Fetch();
			if ($result) {	
            	$sql = "DELETE FROM hm_distributionlistsrecipients WHERE distributionlistrecipientlistid='" . $result['distributionlistid'] . "'";
				$sql = $mail_db->prepare($sql);
				$sql->execute();
	            $sql = "DELETE FROM hm_distributionlists WHERE distributionlistaddress='" . $rowdl['dl_address_vc'] . "'";
				$sql = $mail_db->prepare($sql);
				$sql->execute();
			}			
		}

		// Deleting hMail DistListUser
		if (!fs_director::CheckForEmptyValue(self::$deleteuser)) {
	        $result = $mail_db->query("SELECT distributionlistid FROM hm_distributionlists WHERE distributionlistaddress='" . $rowdl['dl_address_vc'] . "'")->Fetch();
			if ($result) {	
                $sql = "DELETE FROM hm_distributionlistsrecipients WHERE distributionlistrecipientaddress='" . $rowdlu['du_address_vc'] . "' AND distributionlistrecipientlistid=" . $result['distributionlistid'] . "";
				$sql = $mail_db->prepare($sql);
				$sql->execute();
			}			
		}
?>