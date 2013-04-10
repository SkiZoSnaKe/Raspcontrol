<?php

namespace lib;

class Memory {
  
  public static function ram() {

		$result = array();

		exec('free -mo', $out);
		preg_match_all('/\s+([0-9]+)/', $out[1], $matches);
		list($total, $used, $free, $shared, $buffers, $cached) = $matches[1];

		$result['percentage'] = round(($used - $buffers - $cached) / $total * 100);
		if ($result['percentage'] > '80')
			$result['alert'] = 'warning';
		else
			$result['alert'] = 'success';
		  
		$result['free'] = $free + $buffers + $cached;
		$result['used'] = $used - $buffers - $cached;
		$result['total'] = $total;

		return $result;
  }

  public static function swap() {

		$result = array();

		exec('free -mo', $out);
		preg_match_all('/\s+([0-9]+)/', $out[2], $matches);
		list($total, $used, $free) = $matches[1];
			
		$result['percentage'] = round($used / $total * 100);
		if ($result['percentage'] > '80')
			$result['alert'] = 'warning';
		else
			$result['alert'] = 'success';

		$result['free'] = $free;
		$result['used'] = $used;
		$result['total'] = $total;

		return $result;
  }

}

?>