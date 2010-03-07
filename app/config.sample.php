<?php
/**
 * Class for handling the configuration
 */
class Config {
    private static $config = array(
                "ldap" => array(
                          "base_dn" => 'dc=some,dc=domain,dc=tld',
                          "host"    => 'some.ip',
                          "bind_dn"  => 'cn=root,dc=some,dc=domain,dc=tld',
                          "bind_pw"  => '',
                          "use_ssl" => false,
                        ),
                "sinu_uri" => "http://whatever",
                "site_email" => "some@email.tld",
                "site_title" => "Some title",
                "site_simple_block" => "simple_block.php",
                "site_notice_block" => "notice_block.php",
                "site_footer" => "footer.php",
                "recaptcha_pubkey" => "",
                "recaptcha_privkey" => "");
    
    public static function get_key($id = null, $name = null) {
        if($name)
            return self::$config[$id][$name];
        else
            return self::$config[$id];
    }
}
?>