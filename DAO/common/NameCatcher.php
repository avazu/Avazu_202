<?php


include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/NameUtil.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/DebugUtil.php');
include_once($_SERVER['DOCUMENT_ROOT'] . '/DAO/common/Db.php');

/**
 * 统一的文档键值获取与处理
 *
 */
class NameCatcher {

	/**
	 * 根据字段名称和对应的对象（文档）id，来获取对应的字段值
	 *  如，get("ip_address", 5) => "192.168.1.99"
	 *
	 * @static
	 * @param  $name_str 字段名称
	 * @param  $doc_id   对象（文档）id
	 * @return String 字段值
	 */
	public static function get($name_str, $doc_id) {
		//echo "in get";
		//var_dump($name_str);
		//var_dump($doc_id);
		//assert(is_numeric($doc_id));
		if(! is_numeric($doc_id)) return "";

		$coll_name = NameUtil::get_coll_name_by_doc_name($name_str);
		//echo "coll_name = $coll_name";
		if(empty($coll_name)) return "";

		$doc_id = (int)$doc_id;
		$doc = Db::findOne($coll_name, $doc_id, array($name_str));

		$value = $doc[$name_str];
		return $value;

	}


}