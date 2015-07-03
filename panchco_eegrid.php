<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Panchco_eegrid	{


				function __construct(){
					
					$this->grid_field_types('team_grid');
					
				}
					
				//-----------------------------------------------------------------------------	
				
				/**
				 *	Get field id.
				 *	@param $field_name string
				 *	@return mixed integer/boolean
				 */
				 public function get_field_id($field_name)
				 {

						ee()->db->select('field_id');
						ee()->db->where('field_name',$field_name);
						ee()->db->limit(1);
						
						$query	= ee()->db->get('channel_fields');
						
						if($query->num_rows()==1)
						{
							return $query->row()->field_id;
						} else {
							return false;
						}
				 }
					
				//-----------------------------------------------------------------------------				
				
				/**
				 *	Get array of grid column field types with column indentifiers as index.
				 *	@param $field_name string
				 *	@return array
				 */
				 public function grid_field_types($field_name)
				 {
				 	
				 		$data			= array();
				 		$columns	= $this->grid_columns($field_name);

						foreach($columns as $row)
						{
							$data[$row->col_id]		= $row->col_type;
							$data[$row->col]			= $row->col_type;
							$data[$row->col_name]	= $row->col_type;
						}
						
						return $data;
						
				 }
					
				//-----------------------------------------------------------------------------
				
				/**
				 *	Get data about our grid columns.
				 *	@param $field_name string
				 *	@return array
				 */
				 public function grid_columns($field_name="")
				 {

					 		$field_id	= $this->get_field_id('team_grid');

					 		$select[]	= "CONCAT('channel_grid_field_',field_id) AS grid_data_table";
					 		$select[]	= "col_id";
					 		$select[]	= "CONCAT('col_id_',col_id) AS col";
					 		$select[]	= "col_order";
					 		$select[]	= "col_type";
					 		$select[]	= "col_label";
					 		$select[]	= "col_name";
							
							ee()->db->select($select);
							ee()->db->from('grid_columns');
							ee()->db->where('field_id',$field_id);
							ee()->db->order_by('col_order');
							$query	= ee()->db->get();
							
							return $query->result();
				 }
				 
				//----------------------------------------------------------------------------- 
				
				/**
				 *	Get grid columns data for an entry id.
				 *	@param $field_name string
				 *	@param $col_names	array
				 *	@param $format string
				 *	@return array
				 */
				 public function entry_grid($entry_id,$field_name,$format='object')
				 {
				 			$data	= array();
				 			$select				= array();
				 			$grid_columns	= $this->grid_columns($field_name);
				 			$field_id			= $this->get_field_id($field_name);
				 			
				 			$grid_data_table	= 'channel_grid_field_' . $field_id;

				 			$select[]	= 'row_id';
					 		$select[] = 'row_order';
				 			
				 			foreach($grid_columns as $key=>$row)
				 			{
					 			
					 			$select[]	= $row->col;
					 			$select[]	=	$row->col . ' AS ' . $row->col_name;
					 			
				 			}
				 			
				 			ee()->db->select($select);
				 			ee()->db->where('entry_id',$entry_id);
				 			ee()->db->order_by('row_order','asc');
				 			$query = ee()->db->get($grid_data_table);
				 			
				 			
				 			if($format=='object')
				 			{ 
					 			return $query->result();
				 			} else {
					 			return $query->result_array();
					 		}
				 }
				 
				//----------------------------------------------------------------------------- 
				
				/**
				 *	Return upload_pref_id.
				 *	@param $str string
				 *	@return mixed integer/boolean
				 */
				 public function upload_prefs_id($str)
				 {

				 		preg_match("/\{filedir_[0-9]{1,}\}/",$str,$match);

					  if(isset($match[0]))
					  {
					  	return preg_replace("/[^[:digit:]]/",'',$match[0]);
					  } else {
						  return FALSE;
					  }

				 }
				 
				//----------------------------------------------------------------------------- 
				
				/**
				 *	Return filename.
				 *	@param $str string
				 *	@return string
				 */
				 public function file_name($str)
				 {

				 		$str	= trim($str);
				 		
				 		return substr($str,(strpos($str,'}')+1),strlen($str));
				 	

				 }
				 
				//----------------------------------------------------------------------------- 
				
				/**
				 *	Return upload_pref_id.
				 *	@param $id integer
				 *	@return mixed object/boolean
				 */
				 public function upload_prefs_row($id)
				 {

				 		ee()->db->select('*');
				 		ee()->db->where('id',$id);
				 		ee()->db->limit(1);
				 		$query	= ee()->db->get('upload_prefs');
				 		
				 		return $query->row();

				 }

				
				//----------------------------------------------------------------------------- 
				
				/**
				 *	Return URL to file.
				 *	@param $str	string
				 *	@param $img_manipulation 
				 *	@return string
				 */
				 public function file_url($str,$image_manipulation='')
				 {
					 
					 $url = '';
					 
					 $upload_prefs	= FALSE;
					 
					 $id	= $this->upload_prefs_id($str);
					 
					 
					 
					 if($id !== FALSE)
					 {
						 $upload_prefs	= $this->upload_prefs_row($id);
					 }
					 
					 if($upload_prefs !== FALSE)
					 {
						 $url	= $upload_prefs->url;
					 }
					 
					 if($image_manipulation != '' && $url != '')
					 {
						 return $url . '_' . $image_manipulation . '/' . $this->file_name($str);
					 	} else {
						  return $url . $this->file_name($str);
						}
					 
				 }
				 
				 //----------------------------------------------------------------------------- 
 }

// END CLASS

/* End of file panchco_eegrid.php */