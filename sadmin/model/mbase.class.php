<?php

/**
 * 基础模型
 */

!defined('IN_FILE') && exit('Access Denied');

class mbase {

	protected $_db;
	protected $_dbtabpre;
	public $timestamp;
	public $onlineip;

	/**
	 * 构造方法
	 * @param object $db MySQL数据库操作对象
	 * @return null
	 */
	public function __construct(&$db) {
		$this->_db = $db;
		$this->_dbtabpre = $db->dbtabpre;
		$this->timestamp = $GLOBALS['timestamp'];
		$this->onlineip = $GLOBALS['onlineip'];
	}

	/**
	 * 插入数据
	 * @param array $fields 字段及值数组
	 * @param array $table 表名
	 * @param string $tabpre 指定表前缀
	 * @return bool 成功与否
	 */
	protected function _insertRecord($fields, $table, $tabpre = '') {
		$setsql = $this->_getFieldsListString($fields);
		$tabpre == '' && $tabpre = $this->_dbtabpre;
		return $this->_db->query("REPLACE INTO `{$tabpre}{$table}` SET {$setsql}");
	}

	/**
	 * 更新数据
	 * @param array $fields 字段及值数组
	 * @param array $wherefields 条件字段及值数组
	 * @param array $table 表名
	 * @param string $tabpre 指定表前缀
	 * @return bool 成功与否
	 */
	protected function _updateRecord($fields, $wherefields, $table, $tabpre = '') {
		$setsql = $this->_getFieldsListString($fields);
		$wheresql = $this->_getFieldsListString($wherefields, 'WHERE');
		$tabpre == '' && $tabpre = $this->_dbtabpre;
		return $this->_db->query("UPDATE `{$tabpre}{$table}` SET {$setsql} WHERE {$wheresql}");
	}

	/**
	 * 构造字段及值SQL
	 * @param array $fields 字段及值数组
	 * @param string $type 构造类型
	 * @return string 字段及值SQL
	 */
	protected function _getFieldsListString($fields, $type = 'SET') {
		$fieldslist = '';
		foreach($fields as $k => $v) {
			$v = ($v === null || $v === false) ? 'NULL' : '\''.mysqlesc($v).'\'';
			$fieldslist .= ($fieldslist == '' ? '' : ($type != 'SET' ? ' AND ' : ', ')).'`'.$k.'`='.$v;
		}
		return $fieldslist;
	}

	/**
	 * 清空表数据 !!!!危险方法,谨慎使用!!!!
	 * @param array $table 表名
	 * @param string $tabpre 指定表前缀
	 * @return bool 成功与否
	 */
	protected function _truncateTable($table, $tabpre = '') {
		$tabpre == '' && $tabpre = $this->_dbtabpre;
		return $this->_db->query("TRUNCATE TABLE `{$tabpre}{$table}`");
	}

	/**
	 * 优化表结构及数据
	 * @param array $table 表名
	 * @param string $tabpre 指定表前缀
	 * @return bool 成功与否
	 */
	protected function _optimizeTable($table, $tabpre = '') {
		$tabpre == '' && $tabpre = $this->_dbtabpre;
		return $this->_db->query("OPTIMIZE TABLE `{$tabpre}{$table}`");
	}

	/**
	 * 最后插入数据的自增ID
	 * @return int 最后插入数据的自增ID
	 */
	public function lastInertId() {
		return $this->_db->insert_id();
	}

	/**
	 * 获取查询SQL的所有记录
	 * @param string $sql SQL
	 * @return array 所有记录
	 */
	protected function _getAllRecords($sql) {
		$query = $this->_db->query($sql);
		$results = array();
		while($row = $this->_db->fetch_array($query)) {
			$results[] = $row;
		}
		$this->_db->free_result($query);
		return $results;
	}

	/**
	 * 获取查询SQL的第一条记录
	 * @param string $sql SQL
	 * @return array 第一条记录
	 */
	protected function _getFirstRecord($sql) {
		return $this->_db->fetch_first($sql);
	}

	/**
	 * 获取查询SQL的第一条记录的第一列数据
	 * @param string $sql SQL
	 * @return array 第一条记录
	 */
	protected function _getFirstResult($sql) {
		return $this->_db->result_first($sql);
	}

}

?>