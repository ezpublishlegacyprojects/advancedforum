<?php
class bb2ezfilter extends eZImportConverter {

	function bb2ezfilter()
	{
		
	}
	function filter ( &$data )
	{
		$search = array ( '/\[url=(.*?)\/\]/USsi',  
						  '/\[url\]/Ui',
						  '/\[\/url\]/Ui',
						  '/\[img.*\]/Ui',
						  '/\[\/img.*\]/Ui',
						  '/\[b.*\]/Ui',
						  '/\[\/b.*\]/Ui',
						  '/\[code.*\]/Ui',
						  '/\[\/code.*\]/Ui',
						  '/\[i.*\]/Ui',
						  '/\[\/i.*\]/Ui',
						  '/\[color.*\]/Ui',
						  '/\[\/color.*\]/Ui',
						  #  '/\&quot/Ui',
						  '/\[quote.*\]/Ui',
						  '/\[\/quote.*\]/Ui' );

		$replace = array (	'$1',
							'',
							'',
							'',
							'',
							'<strong>',
							'</strong>',
							'<literal class="code">',
							'</literal>',
							'<i>',
							'</i>',
							'',
							'',
						#	'"',
							'<literal class="quote">',
							'</literal>' );

		$data = preg_replace( $search, $replace, $data );
		$data = bb2ezfilter::removeHTMLEntities( $data );
	}
	function removeHTMLEntities( $string )
    {
        // replace numeric entities
        $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string );
        $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $string );
        // replace literal entities
   		$trans_tbl = get_html_translation_table(HTML_ENTITIES);
   		$trans_tbl = array_flip( $trans_tbl );
   		return strtr( $string, $trans_tbl );
	}	
}