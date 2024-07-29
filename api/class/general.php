<?php 
    class General{

        public function __construct($conn) {
            $this->db = $conn;
        }

        public function saveData($table, $primaryKey, $data, $jsonFieldArr = []) {

            $cur_date = date('Y-m-d H:i:s');

            $id = isset($data[$primaryKey]) ? $data[$primaryKey] : -1;
         
            if(!isset($data['deleted'])){
                $data['deleted'] = 0;
            }
            
            $sql = "SELECT * FROM {$table} WHERE {$primaryKey} = '{$id}' LIMIT 1";
       
            $rs = $this->db->Execute($sql);

            $updateJsonFields = [];
            if (isset($jsonFieldArr['jsonObj']) && $jsonFieldArr['jsonObj']) {
                foreach ($jsonFieldArr['jsonObj'] as $i => $k) {
                    if ($primaryKey == $k) {
                        unset($jsonFieldArr[$i]);
                    }
                    if ($primaryKey != $k && isset($data[$k])) {
                        if (empty($data[$k])) {
                            $updateJsonFields[] = "{$k} = NULL";
                            unset($data[$k]);
                        } else {
                            $val = (is_array($data[$k]) && !empty($data[$k])) ? json_encode($data[$k], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : NULL;
    
                            if (!is_null($val) && !empty($val)) {
                                $updateJsonFields[] = "{$k} = '{$val}'";
                            }
                            unset($data[$k]);
                        }
                    }
                }
            }
          
            if ($rs->NumRows()) {
                $data['updated_on'] = $cur_date;
                $data['updated_by'] = $_POST["updated_by"];
                
                $sql = $this->db->GetUpdateSQL($rs, $data, true);
            } 
            else {
                if($data[$primaryKey] == -1){
                    unset($data[$primaryKey]);               
                }
                if($table == 'm_year'){
                    $data['m_year_id'] = $data['m_year'];
                }
                $sql = $this->db->GetInsertSQL($rs, $data);
            }

            $result = $this->db->Execute($sql);

            if ($result) {
                $id = ($id > 0) ? $id : $this->db->Insert_ID();
                //Update Json Fields
                if ($updateJsonFields) {
                    $updateSql = "UPDATE {$table} SET ";
    
                    $updateSql .= implode(", ", $updateJsonFields);

                    $updateSql .= " WHERE {$primaryKey} = '{$id}'";
    
                    $this->db->Execute($updateSql);
                }
    
                return $id;
            } else {
                return 0;
            }
            return false;
        }


        public function deleteData($table, $primaryKey, $id) {

            $cur_date = date('Y-m-d H:i:s');

            $sql = "SELECT * FROM {$table} WHERE {$primaryKey} = '{$id}' LIMIT 1";
            $rs = $this->db->Execute($sql);

            if ($rs->NumRows()) {
                $data['deleted'] = 1;
                $data['is_deleted'] = 1;
                $data['updated_on'] = $cur_date;
                $data['updated_by'] = $_POST["updated_by"];
                $sql = $this->db->GetUpdateSQL($rs, $data, true);
            }
      
            $result = $this->db->Execute($sql);

            if ($result) {
                return ($id > 0) ? $id : $this->db->Insert_ID();
            } else {
                return 0;
            }
            return false;
        }

        public function getYear(){
            $sql = "SELECT m_year as `value` , m_year as label , is_status as selected FROM m_year  WHERE is_deleted = 0 ORDER BY m_year_id DESC"; 
            $rs = $this->db->getAll($sql);

            $result = array();
            foreach($rs as $key => $val){
                $result[$key]['value'] = $val['value'];
                $result[$key]['label'] = $val['label'];
               if($val['selected'] == 1){
                $result[$key]['selected'] = true;
               }
            }

            return $result;
        }
    }