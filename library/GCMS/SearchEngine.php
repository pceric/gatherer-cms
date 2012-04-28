<?php
/**
 * Singleton class to control our Lucene based search engine
 *
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class GCMS_SearchEngine
{
    private static $_instance;
    private $_index;

    private function __construct() {
        if (file_exists(APPLICATION_PATH . '/../data/lucene'))
            $this->_index = Zend_Search_Lucene::open(APPLICATION_PATH . '/../data/lucene');
        else
            $this->_index = Zend_Search_Lucene::create(APPLICATION_PATH . '/../data/lucene');
    }
    private function __clone() {}
    private function __wakeup() {}
    
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            $className = __CLASS__;
            self::$_instance = new $className;
        }
        return self::$_instance;
    }
    
    /**
     * Destroys and reindexs every article and news post
     */
    public function rebuildIndex() {
        $db = Zend_Registry::get('db');
        $zd = new Zend_Date();

        // First delete all records
        for ($i = 0; $i < $this->_index->maxDoc(); $i++) {
            $this->_index->delete($i);
        }
        $this->_index->commit();
        $this->_index->optimize();
        
        $pages = array_merge($db->fetchAll("SELECT *,'article' as module FROM articles WHERE published = 1"),
                             $db->fetchAll("SELECT *,'news' as module FROM news WHERE published = 1"),
                             $db->fetchAll("SELECT id,title,summary as content,tags,pubdate,'news' as module,'reader' as type FROM reader"));

        // Now insert all records
        foreach ($pages as $v) {
            if (isset($v['moddate']))
                $time = $zd->set($v['moddate'])->get();
            else
                $time = $zd->set($v['pubdate'])->get();
            if (isset($v['type']))
                $type = '/type/' . $v['type'];
            else
                $type = '';
            $this->addItem($v['title'], $v['content'], $time, $v['tags'], $v['module'] . '/index/index/id/' . $v['id'] . $type);
        }
    }

    /**
     * Adds an item to our lucene db
     *
     * @param string $title Document title
     * @param string $content Document content
     * @param string $url Document URL
     */
    public function addItem($title, $content, $time, $tags, $url) {
        $doc = new Zend_Search_Lucene_Document();
        
        // Index and store title
        $doc->addField(Zend_Search_Lucene_Field::Text('title', $title));
        // Index document contents
        $doc->addField(Zend_Search_Lucene_Field::UnStored('contents', strip_tags($content)));
        // Store timestamp
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('timestamp', $time));
        // Index tags
        $doc->addField(Zend_Search_Lucene_Field::UnStored('tags', $tags));
        // Store document URL to identify it in the search results
        $doc->addField(Zend_Search_Lucene_Field::UnIndexed('url', $url));

        $this->_index->addDocument($doc);
    }

    /**
     * Deletes an item from our lucene db
     *
     * @param string $url Url of record to delete
     */
    public function deleteItem($url) {
        $hits = $this->_index->find('url:' . $url);
        foreach ($hits as $hit) {
            $this->_index->delete($hit->id);
        }
        $this->_index->commit();
    }
    
    /**
     * Updates an item in our lucene db
     *
     * @see addItem()
     */
    public function updateItem($title, $content, $time, $tags, $url) {
        $this->deleteItem($url);
        $this->addItem($title, $content, $time, $tags, $url);
    }
    
    /**
     * Simple interface for Zend_Search_Lucene::find()
     *
     * @see Zend_Search_Lucene::find()
     */
    public function find($query) {
        return $this->_index->find($query);
    }
    
    /**
     * This function performs some magic on our query to make it more search engine like
     * 
     * @param string $query Search query
     * @return Zend_Search_Lucene_Search_QueryHit
     */
    public function search($query) {
        $special = array('+', '-', '&&', '||', '!', '(', ')', '{', '}', '[', ']', '^', '~', '*', '?', ':', '\\');
        $replace = array('\+', '\-', '\&&', '\||', '\!', '\(', '\)', '\{', '\}', '\[', '\]', '\^', '\~', '\*', '\?', '\:', '\\\\');
        $query = str_replace($special, $replace, $query);
        $tmp = $query;
        $tmp = preg_replace('/".*?"/', '', $tmp);
        $tmp_pieces = explode(' ', $tmp);
        for ($i = 0; $i < count($tmp_pieces); $i++) {
            if (strlen($tmp_pieces[$i]) > 2 && substr($tmp_pieces[$i], -1) != '~')
                $query = str_replace($tmp_pieces[$i], $tmp_pieces[$i] . '~', $query);
        }
        return $this->_index->find($query);
    }

}
