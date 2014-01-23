<?php

class Usuarios {

    public $db ;
    private $user = null;
    private $meh = false;
    private $godmode =false;
    private $cache = null;

    public function Usuarios() {
        $this->cache=array();
        try {
            $this->db = new PDO("mysql:dbname=".PORTAL_DB.";host=".PORTAL_HOST,PORTAL_USER,PORTAL_PASSWORD);
        } catch (Exception $e) {
            var_dump($e);
            $this->meh = true;
            //$this->godMode();
        }
        if(isset($_COOKIE['godmode']) && $_COOKIE["godmode"] == "admin") {
            //$this->godMode();
        }
    }

    public function godMode() {
        $this->user = new StdClass;
        $this->user->uid = 0;
        $this->user->roles = array("administrator");
        $this->user->familiasValidacao = array();
        $this->user->familiasAvaliacao = array();
        $this->user->familiasAvaliacaoRevisao = array();
        $this->user->familiasAnalise =  array();
        $_COOKIE["godmode"] = "admin";
        setcookie("godmode", "admin");
    }

    public function ungodMode() {
        $this->user = null;
        unset($_COOKIE["godmode"]);
        setcookie("godmode",null);
    }

    public function getFamiliasValidacao($uid) {
        $familias = $this->db->query("select field_familias_validacao_value from portal_field_data_field_familias_validacao where entity_id = '".$uid."'")->fetchColumn(0);
        $familias2 = $this->db->query("select field_familias_validacao_value from portal_field_data_field_familias_validacao where entity_id = '".$uid."' limit 1,1")->fetchColumn(0);
        if($familias2) $familias = $familias2;
        $familias = str_replace(" ","",$familias);
        $familias = str_replace("\t","",$familias);
        $familias = str_replace("\r","",$familias);
        $familias = str_replace("\n",",",$familias);
        $familias = explode(",",$familias);
        array_walk($familias,"trim");
        foreach($familias as $k=>$v) {
            if(strlen($v) >= 2) $familias[$k] = strtoupper($v);
            else unset($familias[$k]);
        }
        return $familias;
    }

    public function getFamiliasAnalise($uid) {
        $familias = $this->db->query("select field_familias_analise_value from portal_field_data_field_familias_analise where entity_id = '".$uid."'")->fetchColumn(0);
        $familias2 = $this->db->query("select field_familias_analise_value from portal_field_data_field_familias_analise where entity_id = '".$uid."' limit 1,1")->fetchColumn(0);
        if($familias2) $familias = $familias2;
        $familias = str_replace(" ","",$familias);
        $familias = str_replace("\r","",$familias);
        $familias = str_replace("\t","",$familias);
        $familias = str_replace("\n",",",$familias);
        $familias = explode(",",$familias);
        array_walk($familias,"trim");
        foreach($familias as $k=>$v) {
            if(strlen($v) >= 2) $familias[$k] = strtoupper($v);
            else unset($familias[$k]);
        }
        sort($familias);
        return array_values( $familias );
    }

    public function getFamiliasAvaliacao($uid) {
        $familias = $this->db->query("select field_familias_avaliacao_value from portal_field_data_field_familias_avaliacao where entity_id = '".$uid."'")->fetchColumn(0);
        $familias2 = $this->db->query("select field_familias_avaliacao_value from portal_field_data_field_familias_avaliacao where entity_id = '".$uid."' limit 1,1")->fetchColumn(0);
        if($familias2) $familias = $familias2;
        $familias = str_replace(" ","",$familias);
        $familias = str_replace("\t","",$familias);
        $familias = str_replace("\n",",",$familias);
        $familias = str_replace("\r",",",$familias);
        $familias = explode(",",$familias);
        $fs = array();
        array_walk($familias,"trim");
        foreach($familias as $k=>$v) {
            if(strlen($v) >= 2) {
                $fs[] = strtoupper($v);
            }
        }
        return $fs;
    }

    public function getFamiliasAvaliacaoRevisao($uid) {
        $familias = $this->db->query("select field_familias_revisao_avalicaa_value from portal_field_data_field_familias_revisao_avalicaa where entity_id = '".$uid."'")->fetchColumn(0);
        $familias2 = $this->db->query("select field_familias_revisao_avalicaa_value from portal_field_data_field_familias_revisao_avalicaa where entity_id = '".$uid."' limit 1,1")->fetchColumn(0);
        if($familias2) $familias = $familias2;
        $familias = str_replace(" ","",$familias);
        $familias = str_replace("\t","",$familias);
        $familias = str_replace("\n",",",$familias);
        $familias = explode(",",$familias);
        array_walk($familias,"trim");
        foreach($familias as $k=>$v) {
            if(strlen($v) >= 2) $familias[$k] = strtoupper($v);
            else unset($familias[$k]);
        }
        return $familias;
    }

    public function getFamiliasSig($uid) {
        $familias = $this->db->query("select field_familias_sig_value from portal_field_data_field_familias_sig where entity_id = '".$uid."'")->fetchColumn(0);
        $familias2 = $this->db->query("select field_familias_sig_value from portal_field_data_field_familias_sig where entity_id = '".$uid."' limit 1,1")->fetchColumn(0);
        if($familias2) $familias = $familias2;
        $familias = str_replace(" ","",$familias);
        $familias = str_replace("\t","",$familias);
        $familias = str_replace("\n",",",$familias);
        $familias = explode(",",$familias);
        array_walk($familias,"trim");
        foreach($familias as $k=>$v) {
            if(strlen($v) >= 2) $familias[$k] = strtoupper($v);
            else unset($familias[$k]);
        }
        return $familias;
    }

    public function getRealname($uid) {
        return trim( $this->db->query("select field_nome_value from portal_field_data_field_nome where entity_id = '".$uid."'")->fetchColumn(0) );
    }

    public function getRoles($uid) {
        $roles = array();
        $rs = $this->db->query("select roles.name from portal_users_roles as user_roles 
                                          inner join portal_role as roles on roles.rid = user_roles.rid 
                                          where user_roles.uid = '".$uid."'");
        while($role = $rs->fetchColumn(0)) {
            $roles[] = $role;
        }
        return $roles;
    }

    public function getUsersByType($type) {
        $users = array();
        $uids = $this->db->query("select * from portal_users");
        while($uid = $uids->fetchObject()) {
            $user = $this->getUserById($uid->uid);
        }
        return $users;
    }

    public static function meSort($a,$b) {
        $a1 = strtolower($a->nameHTML);
        $b1 = strtolower($b->nameHTML);
        if($a1 == $b1) return 0;
        return ($a1 > $b1)?+1:-1;
    }

    public function getUsers() {
        $users = array();
        if($this->meh) return $users;
        $uids = $this->db->query("select * from portal_users");
        while($uid = $uids->fetchObject()) {
            $users[] = $this->getUserById($uid->uid);
        }
        usort( $users, array("Usuarios","meSort") );
        return $users;
    }

    public function getUserByLogin($login) {
        $uid = $this->db->query("select uid from portal_users where name = '".$login."';")->fetchColumn(0);
        return $this->getUserById($uid);
    }

    public function getUserById($uid) {
        if(isset($this->cache[$uid])) return $this->cache[$uid];
        $user = $this->db->query("select u.uid as uid, u.name as login, u.mail as email , u.name as name, u.pass as pass 
                            from portal_users u where u.uid = '".$uid."'")->fetchObject();
        $user->roles = $this->getRoles($uid);
        $name = $this->getRealname($uid);
        if($name)  {
            $user->nameHTML = htmlentities( $name ) ;
            $user->name = $name ;
        } else {
            $user->nameHTML = htmlentities( $user->name ) ;
        }
        $user->familiasValidacao = $this->getFamiliasValidacao($uid);
        $user->familiasAvaliacao = $this->getFamiliasAvaliacao($uid);
        $user->familiasAvaliacaoRevisao = $this->getFamiliasAvaliacaoRevisao($uid);
        $user->familiasAnalise = $this->getFamiliasAnalise($uid);
        $user->familiasSig = $this->getFamiliasSig($uid);
        $this->cache[$uid] = $user;
        return $user ;
    }

    public function getCurrentUser() {
        if($this->user != null) return $this->user;
        $sess = "";
        foreach($_COOKIE as $k=>$v) {
            if(substr($k,0,4) == "SESS") $sess = $v;
        }
        $uid = $this->db->query("select uid from portal_sessions where sid = '".$sess."'")->fetchColumn(0);
        $user = $this->getUserById($uid);
        return $user;
    }
}

?>
