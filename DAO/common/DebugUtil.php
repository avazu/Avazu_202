<?php
/**
 * 调试和控制
 */

class DU {


	//用于调试错误的情况
	//todo remove this check when online
	static function die_if_empty($a) {
//		if (!is_array($a)) {
//			return;
//		}
//		foreach ($a as $k => $v) {
//			//if ($v != 0 && $v != array() && empty($v)) {
//			if (preg_match('/(user_tracking_domain|aff_campaign_url_[2345])/', $k)) {
//				continue;
//			} //确认非异常情况
//
//			if ($v === null || $v === '') {
//				self::dump($a, __FUNCTION__);
//				die("**************not allowed empty value for $k****************");
//			}
//		}
	}

	//调试临时使用，性能不好
	//__FILE__, __class__, __line__, __FUNCTION__
	static function dump(&$obj, $f_or_class = "", $line_or_func = "") {
//		$vname = self::vname($obj);
//		$f_or_class = basename($f_or_class);
//		echo "\n[" . $f_or_class . "/" . $line_or_func . "].$vname=";
//		var_dump($obj);
////		print_r($obj);
//		echo "\n.....\n";
	}


	//得到变量的名字
	private static function vname(&$obj, $scope = false, $prefix = 'unique', $suffix = 'value') {
		if ($scope) {
			$vals = $scope;
		}
		else {
			$vals = $GLOBALS;
		}
		$old = $obj;
		$obj = $new = $prefix . rand() . $suffix;
		$vname = FALSE;
		foreach ($vals as $key => $val) {
			if ($val === $new) {
				$vname = $key;
			}
		}
		$obj = $old;
		return $vname;
	}
}
