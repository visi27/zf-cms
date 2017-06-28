<?php

/**
 *
 * @author Administrator
 *        
 */

class Utility_Xml {
	
	/**
	 * The main function for converting to an XML document.
	 * Pass in a multi dimensional array and this recursively loops through and builds up an XML document.
	 *
	 * @param array $data
	 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
	 * @param SimpleXMLElement $xml - should only be used recursively
	 * @return string XML
	 * @todo reqursion should be revised. Should be created a private method.
	 */
    public static function toXML( $data, $rootNodeName = 'ResultSet', &$xml=null , $xslt=null) {

    	header("Content-type: text/xml"); 
    	
        // turn off compatibility mode as simple xml throws a wobbly if you don't.
        if ( ini_get('zend.ze1_compatibility_mode') == 1 ) ini_set ( 'zend.ze1_compatibility_mode', 0 );
        
        if( !is_null($xslt)) 
        	$xsltStyle = "<?xml-stylesheet href=\"xsl/".$xslt.".xsl\" type=\"text/xsl\"?>";
        
        if ( is_null( $xml ) ) //$xml = simplexml_load_string( "" );
        $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?> 
        				$xsltStyle
            		<$rootNodeName />");

        // loop through the data passed in.
        foreach( $data as $key => $value ) {

            $numeric = false;
            
            // no numeric keys in our xml please!
            if ( is_numeric( $key ) ) {
                $numeric = 1;
                $key = $rootNodeName;
            }

            // delete any char not allowed in XML element names
            $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);

            // if there is another array found recrusively call this function
            if ( is_array( $value ) ) {
                $node = Utility_Xml::isAssoc( $value ) || $numeric ? $xml->addChild( $key ) : $xml;

                // recrusive call.
                if ( $numeric ) $key = 'anon';
                Utility_Xml::toXml( $value, $key, $node );
            } else {

                // add single node.
                $value =  htmlspecialchars(trim($value));
                $xml->addChild( $key, $value );
            }
        }


        $doc = new DOMDocument('1.0');
        $doc->preserveWhiteSpace = false;
        $doc->loadXML( $xml->asXML() );
        
        return $doc->saveXML();
         
       }

		
   /**
     * Convert an XML document to a multi dimensional array
     * Pass in an XML document (or SimpleXMLElement object) and this recrusively loops through and builds a representative array
     *
     * @param string $xml - XML document - can optionally be a SimpleXMLElement object
     * @return array ARRAY
     */
    public static function toArray( $xml ) {
        if ( is_string( $xml ) ) $xml = new SimpleXMLElement( $xml );
        $children = $xml->children();
        if ( !$children ) return (string) $xml;
        $arr = array();
        foreach ( $children as $key => $node ) {
            $node = Utility_Xml::toArray( $node );

            // support for 'anon' non-associative arrays
            if ( $key == 'anon' ) $key = count( $arr );

            // if the node is already set, put it into an array
            if ( isset( $arr[$key] ) ) {
                if ( !is_array( $arr[$key] ) || $arr[$key][0] == null ) $arr[$key] = array( $arr[$key] );
                $arr[$key][] = $node;
            } else {
                $arr[$key] = $node;
            }
        }
        return $arr;
    }
       	
    // determine if a variable is an associative array
    public static function isAssoc( $array ) {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }
    	
	
}

?>