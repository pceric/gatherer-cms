<?php
/**
 * This helper scrapes a web page and displays the results
 * 
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class My_View_Helper_Scraper extends Zend_View_Helper_Abstract
{
    public function scraper($args = NULL) {
        // Configure our Zend Framework cache
        $frontendOptions = array('lifetime' => 1800);
        $backendOptions = array('cache_dir' => APPLICATION_PATH . '/../data/cache/');
        $cache = Zend_Cache::factory('Output', 'File', $frontendOptions, $backendOptions);

        $ch = curl_init($args['url']);  // create a new cURL resource
        if ($ch === FALSE) {
            echo "Error: Bad URL";
            return;
        }
        else {
            if (isset($args['id']))
                $id = $args['id'];
            else
                $id = md5($args['url']);
        }

        if(!$cache->start($id)) {
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            $data = curl_exec($ch);
            if (preg_match($args['pattern'], $data, $matches)) {
                // Fix URLs
                $url = parse_url($args['url']);
                $out = preg_replace('@href="\/(.*?)"@isu', 'href="' . $url['scheme'] . '://' . $url['host'] . '/$1"', $matches[1]);
                // Output fixed HTML using Tidy if possible
                if (function_exists('tidy_repair_string'))
                    echo tidy_repair_string($out, array('output-xhtml' => true, 'show-body-only' => true));
                else
                    echo $out;
            } else {
                echo "Error: No Data";
            }
            $cache->end();
        }
        curl_close($ch);
    }
}
?>
