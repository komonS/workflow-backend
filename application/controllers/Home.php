<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public function __construct()
    {
        parent::__construct();
        
    }
	public function index()
	{
		// echo "Home";
        $this->load->view("welcome_message");
	}

    public function test2()
    {
        echo "Test2";
    }

	public function test()
	{
		$host = "10.20.31.22";
        $port = "389";
        $dn = 'sciadm01';
        $pass = 'Klong%bb0';
        $basedns = "@siamchem.com";
        $base = "dc=SIAMCHEM,dc=com";
        $scope = "sub";
        $filter = "sAMAccountName=komon*";
        $ds=ldap_connect($host, $port);
        ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION,3);
        ldap_set_option($ds, LDAP_OPT_REFERRALS,0);
        if ($ds) {
            if (!ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3)) {
                exit;
            }
           $r=ldap_bind($ds, $dn.$basedns, $pass);
           
           $sr=ldap_search($ds, $base, "($filter)"); 
           
           $info = ldap_get_entries($ds, $sr);
          
           for ($i=0; $i<$info["count"]; $i++) {
                $name = $info[$i]["givenname"][0]." ".$info[$i]["sn"][0];
                $manage = $info[$i]["manager"][0];
                $title = $info[$i]["title"][0];
           }
           //print_r($info);

            echo $name;
            echo '<br>';
            echo $title;
            echo '<br>';
          	echo $manage;
          	echo '<br>';
          	echo '<hr>';
          	echo '<br>';
          	print_r($info);

           ldap_close($ds);
        } else {
           echo "<h4>Unable to connect to LDAP server</h4>";
        }
	}
}
