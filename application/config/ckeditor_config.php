<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

$config['path'] = 'assets/lib/ckeditor';

$config['Full'] = array(
                    'path' => $config['path'],
					'config' => array(
							'filebrowserBrowseUrl' => base_url('admin/media/editor_browser'),
					)
);

$config['toolbarGroup'] = array(
                    'path' => $config['path'],
                    //Optionnal values
                    'config' => array(
                            'toolbarGroup'
                    )
);

$config['General_user'] = array(
                    'path' => $config['path'],
                    //Optionnal values
                    'config' => array(
                            'toolbar' => 'General_user',						
							'pasteFromWordPromptCleanup' => true,
							'pasteFromWordRemoveFontStyles' => true,
							'forcePasteAsPlainText' => true,
							'ignoreEmptyParagraph' => true,
							'removeFormatAttributes' => true,
                    )
);

$config['Simple'] = array(
                    'path' => $config['path'],
                    //Optionnal values
                    'config' => array(
                            'toolbar' => 'Simple',						
							'pasteFromWordPromptCleanup' => true,
							'pasteFromWordRemoveFontStyles' => true,
							'forcePasteAsPlainText' => true,
							'ignoreEmptyParagraph' => true,
							'removeFormatAttributes' => true,
                    )
);

/* End of file config.php */
/* Location: ./application/config/config.php */