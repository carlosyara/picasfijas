<?php
class Db
{
    private $conex;

    /**
     * Terrible code on this implementation.
     *
     * @param [type] $host
     * @param [type] $user
     * @param [type] $pass
     * @param [type] $schema
     * @return void
     */
    public function init($host,$user,$pass,$schema)
    {
            $this->conex = new mysqli($host,$user,$pass,$schema);
            $this->conex->set_charset("utf8");
    }

    protected function dbSelect($table = null,$fields = array(), $conditions = array(), $aditionals = array(), $joins = array())
    {
        $query  = 'SELECT ';
        $query .= implode(',', $fields);
        $query .= ' FROM '.$table;
        $query .= $this->setJoins($joins);
        $query .= $this->setConditions($conditions);
        $query .= ' '.implode(' ', $aditionals);
        $result = $this->exeQuery($query);

        $items = array();

        while($row = $result->fetch_assoc())
        {
            $keys           = array_keys($row);
            $current_row    = array();

            foreach($keys as $key)
            {
                $current_row[$key] = $row[$key];
            }

            $items[] = $current_row;
        }

        return $items;
    }
    protected function dbInsert($table = null, $values = array())
    {
      $query  = 'INSERT INTO '.$table;
      $query .= $this->setValues($values);
      $result = $this->exeQuery($query);
      return $result;
    }
    protected function dbUpdate($table = null, $sets = array(), $conditions){
        $query  = 'UPDATE '.$table;
        $query .= $this->setSets($sets);
        $query .= $this->setConditions($conditions);
        $result = $this->exeQuery($query);
        return $result;
    }
    private function setSets($sets){
      $output = array();
      foreach ($sets as $key => $value) {
          if(!is_numeric($value)){
            $value = "'".$value."'";
          }
          $output[] = $key.'='.$value;
      }
      $output = implode(' , ', $output);
      return ' SET '.$output;
    }
    private function setValues($values){
      $output = array();
      foreach ($values as $key => $value) {
        $output['keys'][]   = $key;
        if(!is_numeric($value)){
          $value = "'".$value."'";
        }
        $output['values'][] = $value;
      }

      if(count($output) > 0)
      {
        $keys   = implode(' , ', $output['keys']);
        $output = implode(' , ', $output['values']);

        $output = ' ('.$keys.') VALUES ('.$output .')';
      }
      else
      {
          $output = '';
      }

        return $output;
    }

    private function setConditions($conditions)
    {
        $output = array();

        $condition_enable = array('>','<','<=','=','>=','LIKE','<>','IN');

        foreach ($conditions as $key => $condition)
        {
            if(!empty($condition['field']) && !empty($condition['condition']) && !is_null($condition['comparation']))
            {

                if(in_array($condition['condition'],$condition_enable))
                {
                    if(!is_numeric($condition['comparation']) && $condition['condition'] != 'IN')
                    {
                        $condition['comparation'] = "'".$condition['comparation']."'";
                    }

                    $output[] = ' '.$condition['field'].' '.$condition['condition'].' '.$condition['comparation'];
                }
            }
        }

        if(count($output) > 0)
        {
            $output = implode(' AND ', $output);

            $output = ' WHERE '.$output;

        }
        else
        {
            $output = '';
        }

        return $output;
    }

    private function setJoins($joins)
    {
        $output = array();

        $condition_enable = array('>','<','<=','=','>=','LIKE','<>');

        foreach ($joins as $key => $join)
        {
            if(!empty($join['table']) && !empty($join['field_left']) && !is_null($join['condition']) && !empty($join['field_rigth']))
            {
                if(in_array($join['condition'],$condition_enable))
                {
                    if(!is_numeric($join['field_left']) && $join['condition'] != 'IN')
                    {
                        $join['field_left'] = $join['field_left'];
                    }

                    if(!is_numeric($join['field_rigth']))
                    {
                        if($join['condition'] != 'IN')
                        {
                            $join['field_rigth'] = $join['field_rigth'];
                        }

                    }

                    $output[] = ' INNER JOIN '.$join['table'].' ON '.$join['field_left'].' '.$join['condition'].' '.$join['field_rigth'].' ';
                }
            }
        }

        if(count($output) > 0)
        {
            $output = implode(' ', $output);
            $output = ' '.$output;
        }
        else
        {
            $output = '';
        }

        return $output;
    }
    private function exeQuery($query)
    {
        $result = $this->conex->query($query);
        return $result;
    }

}
?>
