<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/NameUtil.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/DebugUtil.php');
/**
 * Wrapper for the PHP MongoCursor class.
 * 主要目的是在get next返回时的doc中把_id替换成coll_doc_id的形式
 *
 */
//todo check click advance encode and decode
class Cursor implements Iterator {
	/** The PHP MongoCursor being wrapped */
	protected $_cursor;
	protected $_coll;

	/**
	 * Create a new MongoCursor which wraps around a given PHP MongoCursor.
	 *
	 * @param MongoCursor $_cursor The cursor being wrapped.
	 */
	public function __construct(MongoCursor $_cursor, $_coll) {
		$this->_cursor = $_cursor;
		$this->_coll = $_coll;
	}

	/**
	 * Returns the MongoCursor instance being wrapped.
	 *
	 * @return MongoCursor $_cursor The MongoCursor instance being wrapped.
	 */
	public function getMongoCursor() {
		return $this->_cursor;
	}

	public function current() {
		$doc = $this->_cursor->current();
		$doc = NameUtil::decode_click_adv_doc($this->_coll, $doc);
		$doc = NameUtil::replace_id($doc, $this->_coll);
		return $doc;
	}

	public function getNext() {
		$doc = $this->_cursor->getNext();
		if (!empty($doc)) {
			$doc = NameUtil::decode_click_adv_doc($this->_coll, $doc);
			$doc = NameUtil::replace_id($doc, $this->_coll);
		}
		return $doc;
	}

	public function hasNext() {
		return $this->_cursor->hasNext();
	}

	public function next() {
		return $this->_cursor->next();
	}

	public function limit($num) {
		$this->_cursor->limit($num);
		return $this;
	}


	public function skip($num) {
		$this->_cursor->skip($num);
		return $this;
	}


	public function key() {
		return $this->_cursor->key();
	}


	public function dead() {
		return $this->_cursor->dead();
	}


	public function explain() {
		return $this->_cursor->explain();
	}


	public function fields(array $f) {
		$f = NameUtil::encode_click_adv_doc_simple($this->_coll, $f);
		$this->_cursor->fields($f);
		return $this;
	}


	public function hint(array $keyPattern) {
		$this->_cursor->hint($keyPattern);
		return $this;
	}


	public function immortal($liveForever = true) {
		$this->_cursor->immortal($liveForever);
		return $this;
	}


	public function info() {
		return $this->_cursor->info();
	}


	public function rewind() {
		return $this->_cursor->rewind();
	}


	public function reset() {
		return $this->_cursor->reset();
	}


	public function count($foundOnly = false) {
		return $this->_cursor->count($foundOnly);
	}


	public function addOption($key, $value) {
		$this->_cursor->addOption($key, $value);
		return $this;
	}


	public function batchSize($num) {
		$this->_cursor->batchSize($num);
		return $this;
	}


	public function slaveOkay($okay = true) {
		$this->_cursor->slaveOkay($okay);
		return $this;
	}


	public function snapshot() {
		$this->_cursor->snapshot();
		return $this;
	}


	public function sort($fields) {
		//		foreach ($fields as $fieldName => $order) {
		//			if (is_string($order)) {
		//				$order = strtolower($order) === 'asc' ? 1 : -1;
		//			}
		//			$order = (int)$order;
		//			$fields[$fieldName] = $order;
		//		}
		$this->_cursor->sort($fields);
		return $this;
	}


	public function tailable($tail = true) {
		$this->_cursor->tailable($tail);
		return $this;
	}


	public function timeout($ms) {
		$this->_cursor->timeout($ms);
		return $this;
	}


	public function valid() {
		return $this->_cursor->valid();
	}

	/**
	 * todo fix 在处理替换name和id之前不要使用
	 *
	 * @return array
	 */
	public function toArray() {
		return iterator_to_array($this);
	}

	/**
	 * Get the first single result from the cursor.
	 *  not used function till now
	 *
	 * @return array $document  The single document.
	 */
	public function getSingleResult() {
		$result = null;
		$this->valid() ? null : $this->next();
		if ($this->valid()) {
			$result = $this->current();
		}
		$this->reset();
		return $result ? $result : null;
	}
}
