<?php

/**
 * 
 */
class FlowModel extends CI_model
{
    public function create($arr)
    {
        $this->db->insert('Flow', $arr);
        return $this->db->insert_id();
    }

    public function update($arr, $where)
    {
        $this->db->set($arr);
        $this->db->where($where);
        $this->db->update('Flow');
    }

    public function delelte($where)
    {
        $this->db->where($where);
        $this->db->delete('Flow');
    }

    public function selectData($where = "")
    {
        if ($where != "") {
            $query = $this->db->select('*')
                ->from('Flow')
                ->where($where)
                ->get();
            $result = $query->result();
        } else {
            $query = $this->db->select('*')
                ->from('Flow')
                ->get();
            $result = $query->result();
        }

        return $result;
    }


    public function countFlow($where = "")
    {
        if ($where != "") {
            $query = $this->db->select('COUNT(*) AS count')
                ->from('Flow')
                ->where($where)
                ->get();
            $result = $query->result();
            return $result;
        } else {
            $query = $this->db->select('COUNT(*) AS count')
                ->from('Flow')
                ->get();
            $result = $query->result();
            return $result;
        }
    }
}
