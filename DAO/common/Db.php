<?php

include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/NameUtil.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/DebugUtil.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/Cursor.php');
/**
 * Class Db
 *
 * @package SimpleMongoPhp
 * @author Ian White (ibwhite@gmail.com)
 * @author jjzhao  (ijjzhao@gmail.com)
 * @version 1.2
 * https://github.com/ibwhite/simplemongophp
 * This is a simple library to wrap around the Mongo API and make it a little more convenient
 * to use for a PHP web application.
 *
 * To set up, all you need to do is:
 *    - include() or require() this file
 *    - call Db::addConnection()
 *
 * Example usage:
 *   $mongo = new Mongo();
 *   Db::addConnection($mongo, 'lost');
 *
 *   Db::drop('people');
 *   Db::batchInsert('people', array(
 *     array('name' => 'Jack', 'sex' => 'M', 'goodguy' => true),
 *     array('name' => 'Kate', 'sex' => 'F', 'goodguy' => true),
 *     array('name' => 'Locke', 'sex' => 'M', 'goodguy' => true),
 *     array('name' => 'Hurley', 'sex' => 'M', 'goodguy' => true),
 *     array('name' => 'Ben', 'sex' => 'M', 'goodguy' => false),
 *   ));
 *   foreach (Db::find('people', array('goodguy' => true), array('sort' => array('name' => 1))) as $p) {
 *     echo $p['name'] " is a good guy!\n";
 *   }
 *   $ben = Db::findOne('people', array('name' => 'Ben'));
 *   $locke = Db::findOne('people', array('name' => 'Locke'));
 *   $ben['enemy'] = Db::createRef('people', $locke);
 *   $ben['goodguy'] = null;
 *   Db::save('people', $ben);
 *
 * See the Dbo.php class for how you could do the same thing with data objects.
 *
 * This library may be freely distributed and modified for any purpose.
 **/
class Db {
	static $connections;
	static $read_slave = false;

	static function getConnectionInfo($collection = null, $read_only = false) {
		$info = null;

		// in read-only mode, check for a slave config if set to read slave
		if ($read_only && self::$read_slave) {
			if (isset(self::$connections["$collection/slave"])) {
				$info = self::$connections["$collection/slave"];
			} else {
				if (isset(self::$connections['/slave'])) {
					$info = self::$connections['/slave'];
				}
			}
		}

		if (!$info) {
			if (isset(self::$connections[$collection])) {
				$info = self::$connections[$collection];
			} else {
				if (isset(self::$connections[''])) {
					$info = self::$connections[''];
				} else {
					if (isset($GLOBALS['mongo'])) {
						$info = array($GLOBALS['mongo'], MONGODB_NAME); // backwards compat
					} else {
						throw new Exception('No connection configuration, call Db::addConnection()');
					}
				}
			}
		}

		return $info;
	}

	static function getDb($collection, $read_only = false) {
		$info = self::getConnectionInfo($collection, $read_only);
		list($mongo, $db_name) = $info;
		if (!$mongo->connected) {
			$mongo->connect();
		}
		return $mongo->selectDB($db_name);
	}

	static function getCollection($collection, $read_only = false) {
		$info = self::getConnectionInfo($collection, $read_only);
		list($mongo, $db_name) = $info;
		if (!$mongo->connected) {
			$mongo->connect();
		}
		return $mongo->selectCollection($db_name, $collection);
	}

	static function addConnection($mongo, $db_name, $collections = null, $slave = false) {
		$append = $slave ? '/slave' : '';
		if (!$collections) {
			self::$connections[$append] = array($mongo, $db_name);
		} else {
			foreach ($collections as $c) {
				self::$connections["$c$append"] = array($mongo, $db_name);
			}
		}
	}

	static function addSlaveConnection($mongo, $db_name, $collections = null) {
		self::addConnection($mongo, $db_name, $collections, true);
	}

	static function readSlave($setting = true) {
		self::$read_slave = $setting;
	}

	/**
	 * Returns a MongoId from a string, MongoId, array, or Dbo object
	 *
	 * @param mixed $obj
	 * @return MongoId
	 **/
	static function id($obj) {
		if (is_long($obj) || is_int($obj)) {
			return $obj;
		}
		if ($obj instanceof MongoId) {
			return $obj;
		}
		if (is_string($obj)) {
			return new MongoId($obj);
		}
		if (is_array($obj)) {
			return $obj['_id'];
		}
		return new MongoId($obj->_id);
	}

	/**
	 * Returns true if the value passed appears to be a Mongo database reference
	 *
	 * @param mixed $obj
	 * @return boolean
	 **/
//	static function isRef($value) {
//		if (!is_array($value)) {
//			return false;
//		}
//		return MongoDBRef::isRef($value);
//	}

	/**
	 * Returns a Mongo database reference created from a collection and an id
	 *
	 * @param string $collection
	 * @param mixed $id
	 * @return array
	 **/
//	static function createRef($collection, $id) {
//		return array('$ref' => $collection, '$id' => self::id($id));
//	}

	/**
	 * Returns the Mongo object array that a database reference points to
	 *
	 * @param array $dbref
	 * @return array
	 **/
//	static function getRef($dbref) {
//		$db = self::getDb($dbref['$ref'], true);
//		return $db->getDBRef($dbref);
//	}

	/**
	 * Recursively expands any database references found in an array of references,
	 * and returns the expanded object.
	 *
	 * @param mixed $value
	 * @return mixed
	 **/
//	static function expandRefs($value) {
//		if (is_array($value)) {
//			if (self::isRef($value)) {
//				return self::getRef($value);
//			} else {
//				foreach ($value as $k => $v) {
//					$value[$k] = self::expandRefs($v);
//				}
//			}
//		}
//		return $value;
//	}

	/**
	 * Returns a database cursor for a Mongo find() query.
	 *
	 * Pass the query and options as array objects (this is more convenient than the standard
	 * Mongo API especially when caching)
	 *
	 * $options may contain:
	 *   fields - the fields to retrieve
	 *   sort - the criteria to sort by
	 *   limit - the number of objects to return
	 *   skip - the number of objects to skip
	 *
	 * @param string $collection
	 * @param array $query
	 * @param array $options
	 * @return MongoCursor
	 **/
	static function find($collection, $query = array(), $options = array()) {
		$col = self::getCollection($collection, true);
		$fields = isset($options['fields']) ? $options['fields'] : array();

		DU::die_if_empty($query);
		$query = NameUtil::encode_click_adv_doc($collection, $query);
		$fields = NameUtil::encode_click_adv_doc_simple($collection, $fields);
		$result = $col->find($query, $fields);

		//DU::dump($result->count(true));
		if (isset($options['sort']) && $options['sort'] !== null) {
			$result->sort($options['sort']);
		}
		if (isset($options['limit']) && $options['limit'] !== null) {
			$result->limit($options['limit']);
		}
		if (isset($options['skip']) && $options['skip'] !== null) {
			$result->skip($options['skip']);
		}
		return new Cursor($result, $collection);
	}

	/**
	 * use map reduce command to disinct
	 */
	static function distinct($collection, $key, $query = array()) {
		$db = self::getDb($collection, false);

		$query = NameUtil::encode_click_adv_doc($collection, $query);
		$result = $db->command(array("distinct" => $collection, "key" => $key, 'query' => $query));
		//echo "distinct collection=$collection, key=$key";
		//DU::dump($query);
		//DU::dump($result, __FUNCTION__);

		return $result['values'];
	}

	/**
	 * Just like find, but return the results as an array (of arrays)
	 *
	 * @param string $collection
	 * @param array $query
	 * @param array $options
	 * @return array
	 **/
	static function finda($collection, $query = array(), $options = array()) {
		$result = self::find($collection, $query, $options);
		$array = array();
		foreach ($result as $val) {
			$array[] = $val;
		}
		return $array; //todo check is this decoded?
	}

	/**
	 * Do a find() but return an array populated with one field value only
	 *
	 * @param string $collection
	 * @param string $field
	 * @param array $query
	 * @param array $options
	 * @return array
	 **/
	static function findField($collection, $field, $query = array(), $options = array()) {
		$options['fields'] = array($field => 1);
		$result = self::find($collection, $query, $options);
		$array = array();
		foreach ($result as $val) {
			$array[] = $val[$field];
		}
		return $array;
	}

	/**
	 * Do a find() returned as an associative array mapping one field to another
	 *
	 * @param string $collection
	 * @param string $key_field
	 * @param string $value_field
	 * @param array $query
	 * @param array $options
	 * @return array
	 **/
	static function findAssoc($collection, $key_field, $value_field, $query = array(), $options = array()) {
		$options['fields'] = array($key_field => 1, $value_field => 1);
		$result = self::find($collection, $query, $options);
		$array = array();
		foreach ($result as $val) {
			$array[$val[$key_field]] = $val[$value_field];
		}
		return $array;
	}

	/**
	 * Find a single object -- like Mongo's findOne() but you can pass an id as a shortcut
	 *
	 * @param string $collection
	 * @param mixed $query_or_id
	 * @return array
	 **/
	static function findOne($collection, $query_or_id, $fields = array()) {
		$col = self::getCollection($collection, true);

		$query = $query_or_id;
		if (!is_array($query_or_id)) {
			$query = array('_id' => self::id($query_or_id));
		}
		DU::die_if_empty($query);

		$query = NameUtil::encode_click_adv_doc($collection, $query);
		$fields = NameUtil::encode_click_adv_doc_simple($collection, $fields);
		//echo "in find one";
		DU::dump($query);
		$doc = $col->findOne($query, $fields);
		$doc = NameUtil::decode_click_adv_doc($collection, $doc);
		$doc = NameUtil::replace_id($doc, $collection);

		DU::dump($doc);
		return $doc;
	}

	/**
	 * Count the number of objects matching a query in a collection (or all objects)
	 *
	 * @param string $collection
	 * @param array $query
	 * @return integer
	 **/
	static function count($collection, $query = array()) {
		$col = self::getCollection($collection, true);
		//DU::dump($query);

		$query = NameUtil::encode_click_adv_doc($collection, $query);
		$count = $col->count($query);

		return $count;
	}

	/**
	 * Save a Mongo object -- just a simple shortcut for MongoCollection's save()
	 *
	 * @param string $collection
	 * @param array $data
	 * @return boolean
	 **/
	static function save($collection, $data) {
		$col = self::getCollection($collection);
		//DU::dump($data);
		//assert(!empty($data['_id']));
		//die("save");

		$coll_doc_id_name = NameUtil::get_id_name($collection);
		if (isset($data[$coll_doc_id_name])) {
			$data['_id'] = $data[$coll_doc_id_name];
			unset($data[$coll_doc_id_name]);
		}

		DU::die_if_empty($data);

		$data = NameUtil::encode_click_adv_doc($collection, $data);
		return $col->save($data);
	}

	static function insert($collection, $data, $save = true) {

		//check failure of id name control
		$coll_doc_id_name = NameUtil::get_id_name($collection);
		assert(empty($data[$coll_doc_id_name]));
		if (!empty($data[$coll_doc_id_name])) {
			die("save");
		}

		if (!isset($data['_id'])) {
			$data['_id'] = self::seq($collection);
		}
		$col = self::getCollection($collection);

		DU::die_if_empty($data);

		$data = NameUtil::encode_click_adv_doc($collection, $data);
		$r = $col->insert($data, $save);

		if ($r) {
			$doc = $data;

			$doc = NameUtil::decode_click_adv_doc($collection, $doc);
			$doc = NameUtil::replace_id($doc, $collection);

			return $doc;
		} else {
			return $r;
		}
	}

	static function lastError($collection = null, $read_only = false) {
		$db = self::getDb($collection, $read_only);
		return $db->lastError();
	}


	const seq_coll = 'seq';

	/**
	 * auto increment id sequence generator
	 *
	 * @static
	 * @param  $collection 需要增长主键的coll名
	 * @return
	 */
	static public function seq($collection) {
		//$col = self::getCollection('seq');
		//$col->
		//echo "\n" . $collection;
		$db = self::getDb(self::seq_coll, false);
		// $inc => array('abc' => n): 如果记录的该节点abc存在，让该节点的数值加n；如果该节点不存在，让该节点值等于n
		$seq = $db->command(array('findandmodify' => self::seq_coll,
		                         'query' => array('_id' => $collection),
		                         'update' => array('$inc' => array('seq' => 1)),
		                         'new' => TRUE,
		                         'upsert' => TRUE));
		//DU::dump($seq);
		return $seq['value']['seq'];
	}

	/**
	 * 原子操作：查找并更新一个doc，如果找不到就创建，然后返回更新后的结果
	 * job = db.jobs.findAndModify({
	query: {inprogress:false},
	sort:{priority:-1},
	update: {$set: {inprogress: true, started: new Date()}}
	});
	 *
	 * @static
	 * @param  $collection
	 * @param  $query
	 * @param  $newobj
	 * @return 更新或者创建的doc
	 */
	static public function findAndModify($collection, $query, $newobj, $upsert = false) {

		DU::dump($newobj);
		DU::die_if_empty($query);
		DU::die_if_empty($newobj);

		//DU::dump($query_un_encoded);
		$query = NameUtil::encode_click_adv_doc($collection, $query);
		//DU::dump($query);
		$newobj = NameUtil::encode_click_adv_doc($collection, $newobj);


		$db = self::getDb($collection, false);
		$seq = $db->command(array('findandmodify' => $collection,
		                         'query' => $query,
		                         'update' => $newobj,
		                         'new' => TRUE,
		                         'upsert' => $upsert));
		//DU::dump($seq);
		$doc = $seq['value'];

		$doc = NameUtil::decode_click_adv_doc($collection, $doc);
		$doc = NameUtil::replace_id($doc, $collection);
		return $doc;
	}

	/**
	 * Shortcut for MongoCollection's update() method
	 *
	 * @param string $collection
	 * @param array $query
	 * @param array $newobj
	 * @param boolean $upsert
	 * @return boolean
	 **/
	static function update($collection, $query, $newobj, $options = array('multiple' => true, 'safe' => true)) {
		$col = self::getCollection($collection);

		if (!isset($options['multiple'])) {
			$options['multiple'] = true;
		}
		if (!isset($options['safe'])) {
			$options['safe'] = true;
		}
		//todo catch exception for safe=true

		DU::die_if_empty($query);
		DU::die_if_empty($newobj);

		$query = NameUtil::encode_click_adv_doc($collection, $query);
		$newobj = NameUtil::encode_click_adv_doc($collection, $newobj);

		DU::dump($query);
		$affacted_num = $col->count($query);
		if ($affacted_num == 0 || $col->update($query, $newobj, $options)) {
			return $affacted_num;
		} else {
			false;
		}
	}

	/**
	 * 用findandmodify命令实现更新文档并返回它
	 * 如果文档不存在，不创建
	 * @static
	 * @param  $collection
	 * @param  $query
	 * @param  $newobj
	 * @return array|doc
	 */
	//todo 不知道这个的性能和update one then find one相比如何
	static function updateOne($collection, $query_or_id, $newobj) {
		$query = $query_or_id;
		if (!is_array($query_or_id)) {
			$query = array('_id' => self::id($query_or_id));
		}
		DU::die_if_empty($query);

		$doc = self::findAndModify($collection, $query, $newobj, false);

		return $doc;
	}

	/**
	 * 用findandmodify命令来实现 performing an upsert
	 *   query中必须含有_id，这样才可以避开update中不能用upsert的问题（_id会生成MongoId样式）
	 *
	 * @param string $collection
	 * @param array $query
	 * @param array $newobj
	 * @return boolean or doc updated/created
	 */
	static function upsertById($collection, $newobj) {

		$query = array('_id' => $newobj['_id']);

		//只是修改部分属性
		unset($newobj['_id']); // Mod on _id not allowed
		if (!array_key_exists('$set', $newobj)) {
			$newobj = array('$set' => $newobj);
		}

		$doc = self::findAndModify($collection, $query, $newobj, true);

		return $doc;

	}

	// 如果query中没有_id只适用于MongoId类型 _id
//	static function upsert($collection, $query, $newobj) {
//
//		//只是修改部分属性
//		if (!array_key_exists('$set', $newobj)) {
//			$newobj = array('$set' => $newobj);
//		}
//
//		$doc = self::findAndModify($collection, $query, $newobj, true);
//
//		return $doc;
//	}


//	static function updateConcurrent($collection, $criteria, $newobj, $options = array()) {
//		$col = self::getCollection($collection);
//		if (!isset($options['multiple'])) {
//			$options['multiple'] = false;
//		}
//		$i = 0;
//		foreach ($col->find($criteria, array('fields' => array('_id' => 1))) as $obj) {
//			$col->update(array('_id' => $obj['_id']), $newobj);
//			if (empty($options['multiple'])) {
//				return;
//			}
//			if (!empty($options['count_mod']) && $i % $options['count_mod'] == 0) {
//				if (!empty($options['count_callback'])) {
//					call_user_func($options['count_callback'], $i);
//				} else {
//					echo '.';
//				}
//			}
//			$i++;
//		}
//	}


	/**
	 * Shortcut for MongoCollection's remove() method, with the option of passing an id string
	 *
	 * @param string $collection
	 * @param array $query
	 * @param boolean $just_one
	 * @return boolean
	 **/
	static function remove($collection, $query, $just_one = false) {
		$col = self::getCollection($collection);
		if (!is_array($query)) {
			$query = array('_id' => self::id($query));
		}

		$query = NameUtil::encode_click_adv_doc($collection, $query);
		return $col->remove($query, $just_one);
	}

	/**
	 * Shortcut for MongoCollection's drop() method
	 *
	 * @param string $collection
	 * @return boolean
	 **/
	static function drop($collection) {
		$col = self::getCollection($collection);
		return $col->drop();
	}

	/**
	 * Shortcut for MongoCollection's batchInsert() method
	 *
	 * @param string $collection
	 * @param array $array
	 * @return boolean
	 **/
	//todo fix be careful not to used for click advance
	static function batchInsert($collection, $array) {
		$col = self::getCollection($collection);
		return $col->batchInsert($array);
	}

	//deprecated for group can't used in cluster envireoment
//	static function group($collection, array $keys, array $initial, $reduce, array $condition = array()) {
//		$col = self::getCollection($collection, true);
//		return $col->group($keys, $initial, $reduce, $condition);
//	}

	/**
	 * Shortcut for MongoCollection's ensureIndex() method
	 *
	 * @param string $collection
	 * @param array $keys
	 * @return boolean
	 **/
	static function ensureIndex($collection, $keys, $options = array()) {
		$col = self::getCollection($collection);

		$keys = NameUtil::encode_click_adv_doc($collection, $keys);
		return $col->ensureIndex($keys, $options);
	}

	/**
	 * Ensure a unique index
	 *
	 * @param string $collection
	 * @param array $keys
	 * @return boolean
	 **/
	static function ensureUniqueIndex($collection, $keys, $options = array()) {
		$options['unique'] = true;

		$keys = NameUtil::encode_click_adv_doc($collection, $keys);
		return self::ensureIndex($collection, $keys, $options);
	}

	/**
	 * Shortcut for MongoCollection's getIndexInfo() method
	 *
	 * @param string $collection
	 * @return array
	 **/
	static function getIndexInfo($collection) {
		$col = self::getCollection($collection, true);
		return $col->getIndexInfo();
	}

	/**
	 * Shortcut for MongoCollection's deleteIndexes() method
	 *
	 * @param string $collection
	 * @return boolean
	 **/
	static function deleteIndexes($collection) {
		$col = self::getCollection($collection);
		return $col->deleteIndexes();
	}


}