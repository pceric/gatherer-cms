<?php
/**
 * This is a class to manage configuration for GCMS using key => value pairs.
 *
 * @author Eric Hokanson
 * @license http://www.gnu.org/licenses/lgpl.txt
 */
class GCMS_Config implements ArrayAccess, IteratorAggregate, Countable {
    protected $db;
    protected $config;

    function __construct(&$db) {
        $this->db = $db;
        $this->config = $this->db->fetchPairs('SELECT * FROM config');
    }
   
    // ArrayAccess isset($config)
    function offsetExists($offset) {
        return isset($this->config[$offset]);
    }

    // ArrayAccess $config[$key]
    function offsetGet($offset) {
        return isset($this->config[$offset]) ? $this->config[$offset] : null;
    }
    
    // ArrayAccess $config[$key] = $val
    function offsetSet($offset, $value) {
        $this->write($key, $value);
    }
    
    // ArrayAccess unset($config[$key])
    function offsetUnset($offset) {
        $this->remove($offset);
    }

    // IteratorAggregate
    function getIterator() {
        return new ArrayIterator($this->config);
    }

    // Countable
    function count() {
        return count($this->config);
    }

    function write($key, $value = null) {
        if (!is_array($key)) {
            $key = array($key => $value);
        }
        foreach ($key as $k => $v) {
            if (isset($this->config[$k]) || array_key_exists($k, $this->config)) {
                if ($this->config[$k] != $v)
                    $this->db->update('config', array('value' => $v), $this->db->quoteIdentifier('key') . ' = ' . $this->db->quote($k));
            } else {
                $this->db->insert('config', array('key' => $k, 'value' => $v));
            }
            $this->config[$k] = $v;
        }
    }

    function read($key) {
        return $this->config[$key];
    }
    
    function remove($key) {
        if ($this->db->delete('config', 'key = ' . $this->db->quote($key)) > 0) {
            unset($this->config[$key]);
        }
    }
}
