<?php namespace App\Modules\Dashboard\Models;

class SettingModel
{
	
	 public function __construct()
    {
        $this->db = db_connect();
    }

       public function languageList()
    { 
        if ($this->db->tableExists("language")) { 

                $fields = $this->db->getFieldData("language");

                $i = 1;
                foreach ($fields as $field)
                {  
                    if ($i++ > 2)
                    $result[$field->name] = ucfirst($field->name);
                }

                if (!empty($result)) return $result;
 

        } else {
            return false; 
        }
    }

public function settings_data(){
     $builder = $this->db->table('setting')
                             ->get()
                             ->getRow(); 
        return $builder;
}

  public function save_setting($data=[])
  {
        $builder = $this->db->table('setting');
        return  $builder->insert($data);
    }

    public function update_setting($data=[])
    {
     $query = $this->db->table('setting');   
     $query->where('id', $data['id']);
     return $query->update($data);   
    }

public function currency_list()
{
        $builder = $this->db->table('currency_tbl');
        $builder->select('*');
        $query=$builder->get();
        $data=$query->getResult();
        
       $list = array('' => 'Select One...');
        if(!empty($data)){
            foreach ($data as $value){
                $list[$value->icon]=$value->currency_name."(".$value->icon.")";
            }
        }
        return $list;  
}

   
}