<?php
/**
 * Class to handle registrarion against ldap and SINU
 */
class Cont {
    
    function __construct() {
        $this->sinu_uri = Config::get_key('sinu_uri');
        $this->l = new Ldap();
        $this->l->setLDAPHost(Config::get_key('ldap', 'host'));
        $this->l->setBindDN(Config::get_key('ldap', 'bind_dn'));
        $this->l->setBaseDN(Config::get_key('ldap', 'base_dn'));
        $this->l->setBindPassword(Config::get_key('ldap', 'bind_pw'));
        $this->l->connect();
    }
    
    function Cont() {}
    
    function load_home_dn() {
        $this->l->setBaseDN(Config::get_key('ldap', 'base_dn'));
    }
    
    function group_info($g) {
        $this->l->search("(&(objectClass=organizationalUnit)(ou=$g))");
        return $this->l->fetch();
    }
    
    function get_groups() {
        $this->l->search("(&(objectClass=organizationalUnit)(ou=*))");
        $attrs = array();
        while($attrs[] = $this->l->fetch());
        return $attrs;
    }
    
    function count_groups() {
        $this->l->search("(&(objectClass=organizationalUnit)(ou=*))");
        return $this->l->count();
    }
    
    function user_info($u) {
        $this->l->search("(&(objectClass=inetOrgPerson)(uid=$u))");
        return $this->l->fetch();
    }
    
    function auth($u, $p) {
        $this->l->search("(&(objectClass=inetOrgPerson)(uid=$u))");
        $this->l->fetch();
        $dn = $this->l->getDN();
        if(ldap_bind( $this->l->cid, $dn, $p))
            return true;
        else
            return false;
    }
    
    function get_sinu_user($u, $p) {
        $u = strtolower($u);
        $s = curl_init($this->sinu_uri."&username=$u&password=$p");
        curl_setopt($s, CURLOPT_RETURNTRANSFER, 1);
        $o = curl_exec($s);
        curl_close($s);
        $raw_data = array_filter(explode('\n', $this->html2txt($o)));
        $raw_data[] = $u;
        $keys = array("status", "nume", "prenume", "cnp",
                        "facultatea", "sectia", "anul", "grupa", "uid");
        return array_combine($keys, $raw_data);
    }
    
    function valid_user($u, $p, $cnp, $alias) {
        $valid = false;
        $test = $this->user_info($u);
        $a_test = $this->alias_info($alias);
        //test ldap
        if($test == null && $a_test == null)
            $valid = true;
        //test sinu
        $test = $this->get_sinu_user($u, $p);
        if($valid && $test != null && $test["cnp"] == $cnp) {
            $test['alias'] = $alias;
            $test['password'] = $p;
            return $test;
        }
        else
            return false;
    }
    
    function create_user($s_user) {
        $gid = "students";
        $r = null;
        if($s_user) {
            $attrs["objectClass"][] = "inetOrgPerson";
            $attrs["objectClass"][] = "tuStudent";
            $attrs["objectClass"][] = "posixAccount";
            $attrs["objectClass"][] = "tuService";
            $attrs["objectClass"][] = "tuStudentService";
            $attrs["objectClass"][] = "tuEduRoamService";
            $attrs["objectClass"][] = "sambaSamAccount";
            $attrs["cn"] = $s_user['uid'];
            $attrs["sn"] = $s_user['nume'];
            $attrs["ou"] = $s_user['grupa'];
            $attrs["gidNumber"] = $this->get_gid($gid);
            $attrs["uidNumber"] = $this->get_max_uid()+1;
            $attrs["sambaSID"] = $this->get_samba_sid($attrs["uidNumber"]);
            $attrs["displayName"] = $s_user['nume']." ".$s_user['prenume'];
            $attrs["gecos"] = implode(',', array($attrs["displayName"], $s_user['grupa'], '', ''));
            $attrs["givenName"] = $s_user['prenume'];
            $attrs["homeDirectory"] = $this->get_home_dir($s_user['uid']);
            $attrs["mail"] = $this->get_email($s_user['uid']);
            $attrs["tuMail"] = $this->get_email($s_user['uid']);
            $attrs["tuMailAlternateAddress"] = $this->get_email_alias($s_user['alias']);
            $attrs["tuCNP"] = $s_user['cnp'];
            $attrs["userPassword"] = $this->get_password($s_user['password']);
            $this->load_home_dn();
            $this->l->cd("ou=users,".$this->l->getBaseDN());
            $r = $this->l->mkdir("uid", $attrs["cn"], $attrs);
        }
        return $r;
    }
    
    function get_max_uid() {
        $uids = array();
        $this->load_home_dn();
        $this->l->search("(&(objectClass=inetOrgPerson)(uid=*))");
        while($t = $this->l->fetch())
            $uids[] = $t['uidNumber'][0];
        sort($uids);
        return $uids[count($uids)-1];
    }
    
    function get_password($p) {
        $salt = sprintf("$1$%.8s", md5(time() . getmypid() . rand()));
        return "{CRYPT}".crypt($p, $salt);
    }
    
    function get_email_alias($alias) {
        $domain = "@student.utcluj.ro";
        if(filter_var($alias.$domain, FILTER_VALIDATE_EMAIL))
            return $alias.$domain;
    }
    
    function alias_info($a) {
        $domain = "@student.utcluj.ro";
        $this->load_home_dn();
        $this->l->search("(&(objectClass=inetOrgPerson)(tuMailAlternateAddress=$a$domain))");
        return $this->l->fetch();
    }
    
    function get_email($u) {
        $domain = "@student.utcluj.ro";
        return $u.$domain;
    }
    
    function get_home_dir($u) {
        $pref = "/home/students/";
        return $pref.$u;
    }
    
    function get_samba_sid($uid_n) {
        return 'S-1-0-0-'.$uid_n * 2 + 1000;
    }
    
    function get_gid($gid) {
        $this->load_home_dn();
        $this->l->search("(&(objectClass=posixGroup)(cn=$gid))");
        $attrs = $this->l->fetch();
        return $attrs['gidNumber'][0];
    }
    
    function get_users($skip = null, $stop = null) {
        $this->l->search("(&(objectClass=inetOrgPerson)(uid=*))");
        $attrs = array();
        if($stop == null)
            $stop = $this->l->count(); //get the maximum of entries
        if($skip == null) //read entries until $end
            do {
                $attrs[] = $this->l->fetch();
                $stop--;
            }
            while($stop != 0);
        else { //skip first $start entries
            $i = 0;
            do {
                $this->l->fetch();
                $i++;
            }
            while($skip != $i);
            // read the rest of them until $end
            do {
                $attrs = $this->l->fetch();
                $i++;
            }
            while($stop != $i);
        }
        return $attrs;
    }
    
    function count_users() {
        $this->l->search("(&(objectClass=inetOrgPerson)(uid=*))");
        return $this->l->count();
    }
    
    function html2txt($text) {
        $search = array ('@<script[^>]*?>.*?</script>@si',	// Strip out javascript
                         '@<[\/\!]*?[^<>]*?>@si',		// Strip out HTML tags
                         '@([\n])[\s]+@',			// Strip out white space
                         '@\s\s+@',
                         '@&(quot|#34);@i',			// Replace HTML entities
                         '@&(lt|#60);@i',
                         '@&(gt|#62);@i',
                         '@&(nbsp|#160);@i',
                         '@&#(\d+);@e');			// evaluate as php
    
        $replace = array ('',
                         '',
                         '\1',
                         '\n',
                         '"',
                         '<',
                         '>',
                         ' ',
                         'chr(\1)');
    
        return trim(preg_replace($search, $replace, $text));
    }
    
    function __destruct() {
        $this->l->disconnect();
    }
}

?>