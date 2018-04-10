<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

 function imager($img_path, $width, $height, $aspect_ratio = NULL) {
		$CI = &get_instance();
//		$CI->load->library('encryption');
        $img_fpath = 'imager/';
        $img_fpath .= $width . "-" . $height.($aspect_ratio? "-" .$aspect_ratio:'');
		$img_path = $img_path;//$CI->encryption->encode($img_path);
        return site_url().$img_fpath . '/' . $img_path;
    }



/* End of file image_helper.php */
/* Location: ./application/helpers/image_helper.php */
