<?php

/**
 * MySQL数据库操作类
 */

!defined('IN_FILE') && exit('Access Denied');

class mysql {

	private $_link;    // MySQL连接资源
	public $dbtabpre = '';    // 数据库表前缀
	public $querynum = 0;    // 查询计数器

	/**
	 * 连接MySQL数据库
	 * @param string $dbhost 数据库主机名
	 * @param string $dbuser 数据库用户名
	 * @param string $dbpwd 数据库密码
	 * @param string $dbname 数据库名
	 * @param string $dbtabpre 数据库表前缀
	 * @param string $dbcharset 数据库字符集
	 * @param int $dbpconn 数据库持久连接(0 - 关闭, 1 - 开启)
	 * @return null
	 */
	public function connect($dbhost, $dbuser, $dbpwd, $dbname = '', $dbtabpre = '', $dbcharset = '', $dbpconn = 0) {
		if($dbpconn) {
			if(!$this->_link = @mysql_pconnect($dbhost, $dbuser, $dbpwd)) {
				$this->_halt('Can\'t connect to MySQL server!');
			}
		} else {
			if(!$this->_link = @mysql_connect($dbhost, $dbuser, $dbpwd, true)) {
				$this->_halt('Can\'t connect to MySQL server!');
			}
		}
		if($dbcharset) {
			if(!mysql_query("SET NAMES {$dbcharset}", $this->_link)) {
				$this->_halt("Unknown character set '{$dbcharset}'!");
			}
		}
		if($dbname) {
			if(!mysql_select_db($dbname, $this->_link)) {
				$this->_halt("Unknown database '{$dbname}'!");
			}
		}
		if($dbtabpre) {
			$this->dbtabpre = $dbtabpre;
		}
	}

	/**
	 * 重新选择数据库
	 * @param string $dbname 数据库名
	 * @return null
	 */
	public function select_db($dbname) {
		return mysql_select_db($dbname, $this->_link);
	}

	/**
	 * 从结果集中获取一行作为关联数组/数字数组
	 * @param resource $query 结果集
	 * @param int $result_type 结果类型(MYSQL_ASSOC - 关联数组, MYSQL_NUM - 数字数组, MYSQL_BOTH - 二者兼有)
	 * @return array 一行关联数组/数字数组
	 */
	public function fetch_array($query, $result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($query, $result_type);
	}

	/**
	 * 查询SQL并获取结果集中第一行关联数组
	 * @param string $sql SQL语句
	 * @return array 第一行关联数组
	 */
	public function fetch_first($sql) {
		return $this->fetch_array($this->query($sql));
	}

	/**
	 * 从结果集中获取一行作为枚举数组
	 * @param resource $query 结果集
	 * @return array 根据所获取的行生成的数组
	 */
	public function fetch_row($query) {
		return mysql_fetch_row($query);
	}

	/**
	 * 从结果集中获取列信息并作为对象返回
	 * @param resource $query 结果集
	 * @return object 列信息对象
	 */
	public function fetch_fields($query) {
		return mysql_fetch_field($query);
	}

	/**
	 * 获取结果数据
	 * @param resource $query 结果集
	 * @param int $row 偏移行数
	 * @return mixed 结果数据
	 */
	public function result($query, $row) {
		$query = @mysql_result($query, $row);
		return $query;
	}

	/**
	 * 查询SQL并获取结果集中第一条数据
	 * @param string $sql SQL语句
	 * @return mixed 结果集中第一条数据
	 */
	public function result_first($sql) {
		return $this->result($this->query($sql), 0);
	}

	/**
	 * 查询SQL语句
	 * @param string $sql SQL语句
	 * @param string $type 查询类型('UNBUFFERED' - 无缓冲查询, 'SILENT' - 静默查询)
	 * @return resource 结果集
	 */
	public function query($sql, $type = '') {
		$func = $type == 'UNBUFFERED' && @function_exists('mysql_unbuffered_query') ? 'mysql_unbuffered_query' : 'mysql_query';
		if(!($query = $func($sql, $this->_link)) && $type != 'SILENT') {
			$this->_halt('MySQL query error!');
		}
		$this->querynum++;
		return $query;
	}

	/**
	 * 获取前一次MySQL操作所影响的记录行数
	 * @return int 前一次MySQL操作所影响的记录行数
	 */
	public function affected_rows() {
		return mysql_affected_rows($this->_link);
	}

	/**
	 * 获取结果集中行的数目
	 * @param resource $query 结果集
	 * @return int 结果集中行的数目
	 */
	public function num_rows($query) {
		return mysql_num_rows($query);
	}

	/**
	 * 获取结果集中字段的数目
	 * @param resource $query 结果集
	 * @return int 结果集中字段的数目
	 */
	public function num_fields($query) {
		return mysql_num_fields($query);
	}

	/**
	 * 释放结果内存
	 * @param resource $query 结果集
	 * @return bool 成功与否
	 */
	public function free_result($query) {
		return mysql_free_result($query);
	}

	/**
	 * 获取最后一次插入的ID
	 * @return int 最后一次插入的ID
	 */
	public function insert_id() {
		return ($id = mysql_insert_id($this->_link)) >= 0 ? $id : $this->result($this->query("SELECT last_insert_id()"), 0);
	}

	/**
	 * 获取MySQL版本信息
	 * @return string MySQL版本信息
	 */
	public function version() {
		return mysql_get_server_info($this->_link);
	}

	/**
	 * 关闭MySQL连接
	 * @return null
	 */
	public function close() {
		return mysql_close($this->_link);
	}

	/**
	 * 错误信息
	 * @return string 上一次查询的错误信息
	 */
	private function _error() {
		return (($this->_link) ? mysql_error($this->_link) : mysql_error());
	}

	/**
	 * 错误号
	 * @return int 上一次查询的错误号
	 */
	private function _errno() {
		return intval(($this->_link) ? mysql_errno($this->_link) : mysql_errno());
	}

	/**
	 * 错误强制挂起
	 * @param string $message 提示信息
	 * @return null
	 */
	private function _halt($message = '') {
		$errinfo = '<b>Error:</b> '.$this->_error().'<br />';
		$errinfo .= '<b>Errno:</b> '.$this->_errno().'<br />';
		$errinfo = str_replace($this->dbtabpre, '[Table]', $errinfo);
		exit($errinfo);
	}

}

?>