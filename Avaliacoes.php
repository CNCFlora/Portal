<?php

class Avaliacoes {
    public function Avaliacoes() {
        $db = new PDO("mysql:dbname=".MYSQL_DB.";host=".MYSQL_HOST,MYSQL_USER,MYSQL_PASSWORD);
        $this->db = $db;
        $this->users = new Usuarios();
        $this->drupal = $this->users->db;
    }

    function getById($id) {
        $get = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, 
                                        especies.Tipo, especies.Infranome, especies.Autor
                                    from avaliacao inner join especies on especies.Id = avaliacao.Id where avaliacao.Aid = ?");
        $get->execute(array($id));
        $a = $get->fetchObject();
        $a = Especies::nameIt($a);
        $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
        $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
        return $a;
    }

    function getComentarios($aid) {
        $q = $this->drupal->query("select * from portal_comment where nid = '1337".$aid."'");
        $comms = array();
        while($com = $q->fetchObject()) {
            $com->Usuario =  $this->users->getUserById($com->uid)->nameHTML;
            $comms[] = $com;
        }
        return $comms;
    }

    function getByEspecieComentarios($id, $status=null) {
        $get = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, 
                                        especies.Tipo, especies.Infranome, especies.Autor
            from avaliacao inner join especies on especies.Id = avaliacao.Id where avaliacao.Id = ? " );
        $get->execute(array($id));
        $a = $get->fetchObject();
        if(is_object( $a )) {
            $a = Especies::nameIt($a);
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;

            $a->Comments = $this->getComentarios($a->Aid);
        }
        return $a;
    }

    function getByComentarios($aid) {
        $get = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, 
                                        especies.Tipo, especies.Infranome, especies.Autor
            from avaliacao inner join especies on especies.Id = avaliacao.Id where avaliacao.Aid = ?");
        $get->execute(array($aid));
        $a = $get->fetchObject();
        if(is_object( $a )) {
            $a = Especies::nameIt($a);
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;

            $a->Comments = $this->getComentarios($a->Aid);
        }
        return $a;
    }

    function getByEspecie($id, $status=null) {
        if($status==null) {
            $get = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, 
                                            especies.Tipo, especies.Infranome, especies.Autor
                from avaliacao inner join especies on especies.Id = avaliacao.Id where avaliacao.Id = ? order by Aid DESC");
            $get->execute(array($id));
        } else {
            $get = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, 
                                            especies.Tipo, especies.Infranome, especies.Autor
                from avaliacao inner join especies on especies.Id = avaliacao.Id
                                                                 where avaliacao.Id = ? and avaliacao.status = ? order by Aid DESC");
            $get->execute(array($id,$status));
        }
        $a = $get->fetchObject();
        if(is_object( $a )) {
            $a = Especies::nameIt($a);
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
        }
        return $a;
    }

    function update($kv) {

        unset($kv->Nome);
        unset($kv->Nome2);
        unset($kv->Nome3);
        unset($kv->NomeHTML);
        unset($kv->NomeHTML2);
        unset($kv->NomeHTML3);
        unset($kv->AssessorRealname);
        unset($kv->EvaluatorRealname);
        unset($kv->Familia);
        unset($kv->Genero);
        unset($kv->Especie);
        unset($kv->Tipo);
        unset($kv->Infranome);
        unset($kv->Autor);

        $yearAgo = time() - (60*60*24*365);
        if($kv->Status == "Finished" && $kv->CommentsTimestamp > $yearAgo) {
            //$kv->Status = "Published";
        } else if($kv->Status == "Comments") {
            $kv->CommentsTimestamp = time();
        } else if($kv->Status == "Finished") {
            //$kv->RevisaoStatus = "";
            //$kv->RevisaoComentario = "";
        }

        $fields = array();
        $values = array();
        foreach($kv as $k=>$v) {
            if($k == "Aid") continue;
            $fields[] = trim( $k );
            $values[] = trim( str_replace("'","\'", $v ) );
        }
        $values[] = $kv->Aid;

        $update = $this->db->prepare("update avaliacao set ".implode(" = ? ,",$fields)." = ? where Aid = ?");
        $update->execute($values);

        $err = $update->errorinfo();
        if($err[0] != "00000") {
            throw new Exception(  $err[2] );
        }

        return $this;
    }

    function insert($kv) {

        //$c = $this->db->query("select count(*) as c from avaliacao where Id = '".$kv->Id."' and Status != 'Published'")->fetchColumn(0);
        $c = $this->db->query("select count(*) as c from avaliacao where Id = '".$kv->Id."'")->fetchColumn(0);
        if($c >= 1) throw new Exception("Já existe uma avaliação em andamento para esta espécie.");

        $fields = array();
        $values = array();
        $vs = array();
        foreach($kv as $k=>$v) {
            $fields[] = trim( $k );
            $values[] = trim( str_replace("'","\'", $v ) );
            $vs[] = "?";
        }

        $insert = $this->db->prepare("insert into avaliacao (".implode(",",$fields).") values (".implode(",",$vs).")");
        $insert->execute($values);
        $kv->Aid = $this->db->lastInsertId();

        $err = $insert->errorinfo();
        if($err[0] != "00000") {
            throw new Exception(  $err[2] );
        }

        return $this;
    }

    function delete($id) {
        $q = $this->db->prepare("delete from avaliacao where aid = ?");
        $q->execute(array($id));
        return $this;
    }

    function countFamiliaEtapa($id,$status){
        $query = $this->db->prepare("select count(*) from avaliacao where Id in (select Id from especies where familia = ?) and status = ? ");
        $query->execute(array($id,$status));
        return $query->fetchColumn(0);
    }

    function getFamiliaEtapa($id,$status,$start,$limit){
        $query = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, especies.Tipo, especies.Infranome, especies.Autor
                                                from avaliacao inner join especies on especies.Id = avaliacao.Id
                                                                where avaliacao.Id in (select Id from especies where familia = ?) 
                                                                and avaliacao.status = ? order by Familia, Genero, Especie, Infranome limit {$start},{$limit}");
        $query->execute(array( $id,$status ));
        $as =array();
        while($a = $query->fetchObject()) {
            $a = Especies::nameIt($a);
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
            $as[] = $a;
        }
        return $as;
    }

    function getFamilia($familia,$start,$limit){
        $query = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, especies.Tipo, especies.Infranome, especies.Autor
                                                from avaliacao inner join especies on especies.Id = avaliacao.Id
                                                                 where avaliacao.Id in (select Id from especies where familia = ?) 
                                                                 order by Familia, Genero, Especie, Infranome limit {$start},{$limit}");
        $query->execute(array(strtoupper( $familia )));
        $as =array();
        while($a = $query->fetchObject()) {
            $a = Especies::nameIt($a);
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
            $as[] = $a;
        }
        return $as;
    }

    function getEtapa($status,$start,$limit){
        $query = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, especies.Tipo, especies.Infranome, especies.Autor
                                                from avaliacao inner join especies on especies.Id = avaliacao.Id
            where avaliacao.status = ? order by Familia limit {$start},{$limit}");
        $query->execute(array( $status ));
        $as =array();
        while($a = $query->fetchObject()) {
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
            $a = Especies::nameIt($a);
            $as[] = $a;
        }
        return $as;
    }

    function getFinal(){
        $query = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, 
                                            especies.Tipo, especies.Infranome, especies.Autor, snapshots.Data
                                            from avaliacao inner join especies on especies.Id = avaliacao.Id
                                                           inner join snapshots on snapshots.Sid = avaliacao.SnapshotID
                                            where avaliacao.status = ?  and avaliacao.RevisaoRationale = ?
                                            order by Familia, Genero, Especie DESC limit 0,2000");
        $query->execute(array('Published','0'));
        $as = array();
        //$maps = new Mapas("../arquivos/mapas",$this->db);
        while($a = $query->fetchObject()) {
            //$mapa = $maps->selectLatest($a->Id,"Definitivo");
            //if(!is_object($mapa)) continue;
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
            $a = Especies::nameIt($a);
            $as[] = $a;
        }
        return $as;
    }

    function getPortal() {
        $query = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, 
                                            especies.Tipo, especies.Infranome, especies.Autor, snapshots.Data
                                            from avaliacao inner join especies on especies.Id = avaliacao.Id
                                                           inner join snapshots on snapshots.Sid = avaliacao.SnapshotID
                                            where avaliacao.status = ? or avaliacao.status = ? 
                                            order by snapshots.Data DESC limit 0,2000");
        $query->execute(array('Comments','Published'));
        $as = array();
        //$maps = new Mapas("../arquivos/mapas",$this->db);
        while($a = $query->fetchObject()) {
            //$mapa = $maps->selectLatest($a->Id,"Definitivo");
            //if(!is_object($mapa)) continue;
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
            $a = Especies::nameIt($a);
            $as[] = $a;
        }
        return $as;
    }

    function getGov() {
        $query = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, 
                                            especies.Tipo, especies.Infranome, especies.Autor, snapshots.Data
                                            from avaliacao inner join especies  on especies.Id = avaliacao.Id
                                                           inner join snapshots on snapshots.Sid = avaliacao.SnapshotID
                                            where avaliacao.status = ? or avaliacao.status = ?
                                            order by snapshots.Data DESC ");
        $query->execute(array('Comments','Published'));
        $done = array();
        $as = array();
        //$maps = new Mapas("../arquivos/mapas",$this->db);
        while($a = $query->fetchObject()) {
            if(in_array($a->Id,$done)) continue;
            //$mapa = $maps->selectLatest($a->Id,"Definitivo");
            //if(!is_object($mapa)) continue;
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
            $a = Especies::nameIt($a);
            $as[] = $a;
            $done[] = $a->Id;
        }

        $dist = $this->dist($done);
        foreach($dist as $d) {
            foreach($as as $a) {
                if($a->Id == $d->Id) {
                    $a->Estados = implode(" ; ",$d->Estados);
                    $a->Biomas = implode(" ; ",$d->Biomas);
                }
            }
        }

        return $as;
    }

    function dist($ids) {
        $ps  = new PDO("pgsql:dbname=".POSTGRESQL_DB.";host=".POSTGRESQL_HOST,POSTGRESQL_USER,POSTGRESQL_PASSWORD);

        $spps = array();
        foreach($ids as $id) {
            $spps[$id] = new StdClass;
            $spps[$id]->Id = $id;
            $spps[$id]->Estados = array();
            $spps[$id]->Biomas = array();
        }


        $sQ = $ps->query('select "id", "estado" as "uf" from estados_especies where id in ('.implode(",",$ids).');');
        while($pair = $sQ->fetchObject()) {
            $spps[$pair->id]->Estados[] = $pair->uf;
        }

        $sQ = $ps->query('select "id", "bioma" as "bioma" from biomas_especies where id in ('.implode(',',$ids).') ;');
        while($pair = $sQ->fetchObject()) {
            $spps[$pair->id]->Biomas[] = $pair->bioma;
        }

        foreach($spps as $spp) {
            $spp->Biomas = array_unique($spp->Biomas);
            sort($spp->Biomas);
            $spp->Estados = array_unique($spp->Estados);
            sort($spp->Estados);
        }

        return $spps;
    }

    function getGovById($id) {
        $query = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie, 
                                            especies.Tipo, especies.Infranome, especies.Autor, snapshots.Data
                                            from avaliacao inner join especies on especies.Id = avaliacao.Id
                                                           inner join snapshots on snapshots.Sid = avaliacao.SnapshotID
                                            where avaliacao.Id = ? order by snapshots.Data DESC ");
        $query->execute(array($id));
        $a = $query->fetchObject();
        $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
        $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
        $a = Especies::nameIt($a);
        return $a;
    }

    function getLivro() {
        $query = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie,
                                            especies.Tipo, especies.Infranome, especies.Autor, snapshots.Data,
                                            extensao.SingleEOONacional as EOO, extensao.SingleAOONacional as AOO,
                                            ecologia.Biomas
                                            from avaliacao inner join especies on especies.Id = avaliacao.Id
                                                           inner join snapshots on snapshots.Sid = avaliacao.SnapshotID
                                                           inner join ecologia  on ecologia.Id = avaliacao.Id
                                                           inner join extensao  on extensao.Id = avaliacao.Id
                                            where avaliacao.Assessment in ('EN','CR','VU','EX','EW') and avaliacao.status = ? 
                                            order by snapshots.Data DESC ");
        $query->execute(array('Published'));
        $as = array();
        //$maps = new Mapas("../arquivos/mapas",$this->db);
        $done = array();
        while($a = $query->fetchObject()) {
            if(isset($done[$a->Id])) continue;
            $done[$a->Id] = true;
            //$mapa = $maps->selectLatest($a->Id,"Definitivo");
            //if(!is_object($mapa)) continue;
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
            $a = Especies::nameIt($a);
            $as[] = $a;
        }
        return $as;
    }

    function getLivroAnexo() {
        $query = $this->db->prepare("select avaliacao.*, especies.Familia, especies.Genero, especies.Especie,
                                            especies.Tipo, especies.Infranome, especies.Autor, snapshots.Data,
                                            extensao.SingleEOONacional as EOO, extensao.SingleAOONacional as AOO,
                                            ecologia.Biomas
                                            from avaliacao inner join especies on especies.Id = avaliacao.Id
                                                           inner join snapshots on snapshots.Sid = avaliacao.SnapshotID
                                                           inner join ecologia  on ecologia.Id = avaliacao.Id
                                                           inner join extensao  on extensao.Id = avaliacao.Id
                                            where avaliacao.status = ? 
                                            order by snapshots.Data DESC ");
        $query->execute(array('Published'));
        $as = array();
        //$maps = new Mapas("../arquivos/mapas",$this->db);
        $done = array();
        while($a = $query->fetchObject()) {
            if(isset($done[$a->Id])) continue;
            $done[$a->Id] = true;
            //$mapa = $maps->selectLatest($a->Id,"Definitivo");
            //if(!is_object($mapa)) continue;
            $a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            $a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
            $a = Especies::nameIt($a);
            $as[] = $a;
        }
        return $as;
    }

    function comentar($com){
        $ins = $this->db->prepare("insert into comentariosAvaliacao (Aid, Uid, Comentario) values (?,?,?)");
        $ins->execute(array($com->Aid,$com->Uid,$com->Comentario));
        return $com;
    }

	function getTextoFamilia(){
        $query = $this->db->prepare("select * from textoFamilias");
        $query->execute(array());
        $as =array();
        while($a = $query->fetchObject()) {
            //$a->AssessorRealname =  $this->users->getUserById($a->NameOfAssessor)->nameHTML;
            //$a->EvaluatorRealname =  $this->users->getUserById($a->NameOfEvaluator)->nameHTML;
            $as[] = $a;
        }
        return $as;
    }    
}
?>
