<?php

class Especies {
    
    private $db ;

    public function Especies($db) {
        $this->db = $db;
    }

    public function getFamilies() {
        $families = array();
        $rs = $this->db->query("select distinct(Familia) from especies");
        while($family = $rs->fetchColumn(0)){
            $families[] = $family;
        }
        return $families;
    }

    public function getEspecies($family) {
        $spps = array();
        $query = $this->db->prepare("select * from especies where Familia = ? order by Familia, Genero, Especie, Infranome, Autor");
        $query->execute(array($family));
        while($spp = $query->fetchObject()){
            $spp->Nome = $spp->Familia." ".$spp->Genero." ".$spp->Especie." ".$spp->Tipo." ".$spp->Infranome." ".$spp->Autor;
            $spp->Nome = utf8_decode($spp->Nome);
            $spp->Nome2 = $spp->Genero." ".$spp->Especie." ".$spp->Tipo." ".$spp->Infranome." ".$spp->Autor;
            $spp->Nome2 = utf8_decode($spp->Nome2);
            $spp->NomeHTML = strtoupper( $spp->Familia )." <i>".$spp->Genero." ".$spp->Especie."</i> ".$spp->Tipo." ".$spp->Infranome." ".$spp->Autor;
            $spp->NomeHTML = utf8_decode($spp->NomeHTML);
            $spp->NomeHTML2 = "<i>".$spp->Genero." ".$spp->Especie."</i> ".$spp->Tipo." ".$spp->Infranome." ".$spp->Autor;
            $spp->NomeHTML2 = utf8_decode($spp->NomeHTML2);
            $spps[] = $spp;
        }
        return $spps;
    }

    public function countFamiliaEtapa($familia,$etapa) {
        return $this->db->query("select count(*) from especies where Familia = '{$familia}' and Etapa = '{$etapa}' ")->fetchColumn(0);
    }

    public function countEtapa($etapa) {
        return $this->db->query("select count(*) from especies where Etapa = '{$etapa}' ")->fetchColumn(0);
    }

    public function setEtapa($id,$etapa) {
        $this->db->query("update especies set etapa = '{$etapa}' where Id = '{$id}'");
    }

    public function getFamiliaEtapa($familia,$etapa, $start,$limit) {
        $spps = array();
        $query = $this->db->prepare("select * from especies where Familia = ? and Etapa = ?
                                     order by Familia, Genero, Especie, Infranome, Autor
                                     limit {$start},{$limit}");
        $query->execute(array($familia,$etapa));
        while($spp = $query->fetchObject()) {
            $spp = Especies::nameIt($spp);
            $spps[] = $spp;
        }
        return $spps;
    }

    public function getEtapa($etapa, $start,$limit) {
        $spps = array();
        $query = $this->db->prepare("select * from especies where Etapa = ? order by Familia, Genero, Especie, Infranome, Autor limit {$start},{$limit}");
        $query->execute(array($etapa));
        while($spp = $query->fetchObject()){
            $spp = Especies::nameIt($spp);
            $spps[] = $spp;
        }
        return $spps;
    }

    public static function nameIt($spp) {
        $autor = utf8_decode($spp->Autor);
        if(strpos($autor,"M?ll.Arg.") !== false) {
            $spp->Autor = utf8_encode(str_replace("M?ll.Arg.","Müll.Arg.",$autor));
        }
        if(strpos($autor,"Crist?bal") !== false) {
            $spp->Autor = utf8_encode(str_replace("Crist?bal","Cristóbal",$autor));
        }
        if(strpos($autor,"F?e") !== false) {
            $spp->Autor = utf8_encode(str_replace("F?e","Fée",$autor));
        }
        if(strpos($autor,"Allem?o") != false) {
            $spp->Autor = utf8_encode(str_replace("Allem?o","Allemão",$autor));
        }
        if(strpos($autor,"Dus?n") !== false) {
            $spp->Autor = utf8_encode(str_replace("Dus?n","Dusén",$autor));
        }
        if(strpos($autor,"K?rn") !== false) {
            $spp->Autor = utf8_encode(str_replace("K?orn","Körn",$autor));
        }
        if(strpos($autor,"B.?llg.") !== false) {
            $spp->Autor = utf8_encode(str_replace("B.?llg.","B.öllg.",$autor));
        }
        if(strpos(utf8_decode($autor),"�") != false) {
            $spp->Autor = utf8_encode($spp->Autor);
        }
        $spp->Nome = $spp->Familia." ".$spp->Genero." ".$spp->Especie." ".$spp->Tipo." ".$spp->Infranome." ".$spp->Autor;
        $spp->Nome2 = $spp->Genero." ".$spp->Especie." ".$spp->Tipo." ".$spp->Infranome." ".$spp->Autor;
        $spp->Nome3 = $spp->Genero." ".$spp->Especie." ".$spp->Tipo." ".$spp->Infranome;
        $spp->NomeHTML = strtoupper($spp->Familia)." <i>".$spp->Genero." ".$spp->Especie."</i> ".$spp->Tipo." ".$spp->Infranome." ".$spp->Autor ;
        $spp->NomeHTML2 = "<i>".$spp->Genero." ".$spp->Especie."</i> ".$spp->Tipo." ".$spp->Infranome." ".$spp->Autor ;
        $spp->NomeHTML3 = "<i>".$spp->Genero." ".$spp->Especie."</i> ".$spp->Tipo." ".$spp->Infranome;
        if(isset($spp->Assessment)) {
            if(in_array($spp->Assessment,array("DD","LC","NT",'NE',"EX","EW"))) {
                $spp->Criteria = "";
            }
        }
        return $spp;
    }

    public function getEspecie($id) {
        if(is_array($id)) {
            $q = $this->db->query("select * from especies where Id in (".implode(",",$id).") order by familia, genero, especie, infranome;");
            $spps = array();
            while($spp = $q->fetchObject()) {
                $spp = Especies::nameIt($spp);
                $spps[] = $spp;
            }
            return $spps;
        } else {
            $spp = $this->db->query("select * from especies where Id = ".$id)->fetchObject();
            $spp = Especies::nameIt($spp);
            return $spp;
        }
    }
}
?>
