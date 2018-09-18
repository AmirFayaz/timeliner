<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if(!function_exists('getFieldsList'))
{
    function getFieldsList($table_name)
    {
        $CI =& get_instance();
        $CI->load->database();
        return $CI->db->list_fields($table_name);
    }
}

if(!function_exists('getFieldsData'))
{ 
    function getFieldsData($table_name)
    {
        $CI =& get_instance();
        $CI->load->database();
        return $CI->db->field_data($table_name);
    }
}
    
if(!function_exists('getDatabaseObject'))
{
    function getDatabaseObject($table_name , $full_data=FALSE)
    {
        $structure = getFieldsData($table_name);
        foreach($structure as $field) 
        {
            $obj[$field->name] = $field->default ? : NULL;
            $data[$field->name]['type'] = $field->type;
            $data[$field->name]['max_length'] = $field->max_length;
            $data[$field->name]['default'] = $field->default;
            $data[$field->name]['primary_key'] = $field->primary_key;
        }

        if($full_data)  return $data;
        else            return $obj;
    }
}
