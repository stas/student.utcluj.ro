<?
/*****************************************************************************

ldap.inc - version 1.1 

Copyright (C) 1998  Eric Kilfoil eric@ipass.net

Modified by Stas Sușcov <stas@nerd.ro>, March 2010

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.

The author can be reached at eric@ypass.net

*****************************************************************************/

class LDAP {
	var $hostname;
	var $basedn;
	var $binddn;
	var $bindpw;
	var $cid = 0; // LDAP Server Connection ID
	var $bid = 0; // LDAP Server Bind ID
	var $sr = 0; // Search Result
	var $re = 0; // Result Entry
	var $error = ""; // Any error messages to be returned can be put here
	var $start = 0; // 0 if we are fetching the first entry, otherwise 1

	function __construct() {
		if($this->getBindDN() && $this->getBindPassword() && $this->getLDAPHost())
			$this->connect();
	}

	function LDAP()	{ }

	function setLDAPHost($hostname)
	{
		$this->hostname = $hostname;
	}

	function getLDAPHost($hostname)
	{
		return($this->hostname);
	}

	function setBindDN($binddn)
	{
		$this->binddn = $binddn;
	}

	function getBindDN($binddn)
	{
		return($this->binddn);
	}

	function setBaseDN($basedn)
	{
		$this->basedn = $basedn;
	}

	function getBaseDN($basedn)
	{
		return($this->basedn);
	}

	function setBindPassword($bindpw)
	{
		$this->bindpw = $bindpw;
	}

	function getBindPassword($bindpw)
	{
		return($this->bindpw);
	}


	function cd($dir)
	{
		if ($dir == "..")
			$this->basedn = $this->getParentDir();
		else 
			$this->basedn = $dir;
	}

	function getParentDir($basedn = "")
	{
		if (!$basedn)
			$basedn = $this->basedn;
		if ($this->basedn == LDAP_BASEDN)
			return("");
		return(ereg_replace("[^,]*[,]*[ ]*(.*)", "\\1", $basedn));
	}

	function connect()
	{
		$binddn = $this->getBindDN();
		$bindpw = $this->getBindPassword();
		$hostname = $this->getLDAPHost();
		$e = error_reporting(0);
		if (!$this->cid) 
		{
			if ($this->cid=ldap_connect($hostname)) {
				$this->error = "No Error";
				ldap_set_option($this->cid, LDAP_OPT_PROTOCOL_VERSION, 3);
				if ($this->bid = ldap_bind($this->cid, $binddn, $bindpw)) {
					$this->error = "Success";
					error_reporting($e);
					return($this->bid);
				} else {
					$this->error = "Could not bind to " . $binddn;
					error_reporting($e);
					return($this->bid);
				}
			} else {
				$this->error = "Could not connect to LDAP server";
				error_reporting($e);
				return($this->cid);
			}
		} else {
			error_reporting($e);
			return($this->cid);
		}
	}

	function disconnect()
	{
		ldap_close($this->cid);
	}

	function search($filter)
	{
		$e = error_reporting(0);
		$result = array();
		if (!$this->connect())
		{
			error_reporting($e);
			return(0);
		}
		$this->sr = ldap_search($this->cid, $this->basedn, $filter);
		$ldap->error = ldap_error($this->cid);
		$this->resetResult();
		error_reporting($e);
		return($this->sr);
	}

	function ls($filter = "(objectclass=*)", $basedn = "")
	{
		if ($basedn == "")
			$basedn = $this->basedn;
		if ($filter == "")
			$filter = "(objectclass=*)";

		$e = error_reporting(0);
		$result = array();
		if (!$this->connect())
		{
			error_reporting($e);
			return(0);
		}
		
		$this->sr = ldap_list($this->cid, $basedn, $filter);
		$ldap->error = ldap_error($this->cid);
		$this->resetResult();
		error_reporting($e);
		return($this->sr);
	}

	function cat($dn)
	{
		$e = error_reporting(0);
		$result = array();
		if (!$this->connect())
		{
			error_reporting($e);
			return(0);
		}
		$filter = "(objectclass=*)";
		
		$this->sr = ldap_read($this->cid, $dn, $filter);
		$ldap->error = ldap_error($this->cid);
		$this->resetResult();
		error_reporting($e);
		return($this->sr);
	}

	function fetch()
	{
		$e = error_reporting(0);
		if ($this->start == 0)
		{
			$this->start = 1;
			$this->re = ldap_first_entry($this->cid, $this->sr);
		} else {
			$this->re = ldap_next_entry($this->cid, $this->re);
		}
		if ($this->re)
		{
			$att = ldap_get_attributes($this->cid, $this->re);
		}
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($att);
	}

	function resetResult()
	{
		$this->start = 0;
	}

	function getDN()
	{
		$e = error_reporting(0);
		$rv = ldap_get_dn($this->cid, $this->re);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($rv);
	}
	
	function count()
	{
		$e = error_reporting(0);
		$rv = ldap_count_entries($this->cid, $this->sr);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($rv);
	}

	function mkdir($attrname, $dirname, $info = null, $basedn = "")
	{
		if ($basedn == "")
			$basedn = $this->basedn;
		$e = error_reporting(0);
		//$info["objectclass"] = "top";
		//$info[$attrname] = $dirname;
		$r = ldap_add($this->cid, "$attrname=$dirname, " . $basedn, $info);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($r ? $r : 0);
	}

	function rm($attrs = "", $dn = "")
	{
		if ($dn == "")
			$dn = $this->basedn;

		$e = error_reporting(0);
		$r = ldap_mod_del($this->cid, $dn, $attrs);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($r);
	}

	function rename($attrs, $dn = "")
	{
		if ($dn == "")
			$dn = $this->basedn;

		$e = error_reporting(0);
		$r = ldap_mod_replace($this->cid, $dn, $attrs);
		$ldap->error = ldap_error($this->cid);
		error_reporting($e);
		return($r);
	}

	function rmdir($deletedn)
	{
		$e = error_reporting(0);
		$r = ldap_delete($this->cid, $deletedn);
		$this->error = ldap_error($this->cid);
		error_reporting($e);
		return($r ? $r : 0);
	}

	function modify($attrs)
	{
		$e = error_reporting(0);
		$r = ldap_modify($this->cid, $this->basedn, $attrs);
		$this->error = ldap_error($this->cid);
		error_reporting($e);
		return($r ? $r : 0);
	}
}
?>