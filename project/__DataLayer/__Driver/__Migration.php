<?php

namespace __DataLayer\__Driver;

abstract class __Migration
{
	public static $sql = '';
	public static $tmpSql = '';

	/**
	 * @return __FieldTypes
	 */
	public function field(string $_fieldName)
	{
		return (new __FieldTypes($_fieldName));
	}

	public function table($_name)
	{
		self::$tmpSql = '';
		self::$sql .= "\n CREATE TABLE IF NOT EXISTS `" . $_name . "` (\n";
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function primaryKey(...$_fields)
	{
		self::$sql = rtrim(self::$sql, ',') . ' , primary key(' . implode(',', $_fields) . ')';
	}

	public function endTable()
	{
		$sql = rtrim(rtrim(self::$sql, ',') . ',' . self::$tmpSql, ',');
		$sql .= ")\n ENGINE=InnoDB DEFAULT CHARSET=utf8; \n";
		self::$sql = $sql;
	}
}

class __FieldTypes
{
	private
		$fieldName;

	function __construct(string $_fieldName)
	{
		$this->fieldName = $_fieldName;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function string($_limit = null)
	{
		$tmp = ($_limit === null) ? 'text' : 'varchar(' . $_limit . ')';
		__Migration::$sql .= "\n `" . $this->fieldName . '` ' . $tmp . ' CHARACTER SET utf8 COLLATE utf8_persian_ci,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function bool()
	{
		__Migration::$sql .= "\n `" . $this->fieldName . '` tinyint(1) unsigned,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function float($_beforePoint = null, $_afterPoint = null)
	{
		$tmp = ($_beforePoint === null) ? '' : '(' . ($_beforePoint + $_afterPoint) . ',' . $_afterPoint . ')';
		__Migration::$sql .= "\n `" . $this->fieldName . '` float' . $tmp . ',';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function tinyInt($_limit = null)
	{
		$tmp = ($_limit === null) ? '' : '(' . $_limit . ')';
		__Migration::$sql .= "\n `" . $this->fieldName . '` tinyint' . $tmp . ',';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function smallInt($_limit = null)
	{
		$tmp = ($_limit === null) ? '' : '(' . $_limit . ')';
		__Migration::$sql .= "\n `" . $this->fieldName . '` smallint' . $tmp . ',';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function mediumInt($_limit = null)
	{
		$tmp = ($_limit === null) ? '' : '(' . $_limit . ')';
		__Migration::$sql .= "\n `" . $this->fieldName . '` mediumint' . $tmp . ',';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function int($_limit = null)
	{
		$tmp = ($_limit === null) ? '' : '(' . $_limit . ')';
		__Migration::$sql .= "\n `" . $this->fieldName . '` int' . $tmp . ',';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function bigInt($_limit = null)
	{
		$tmp = ($_limit === null) ? '' : '(' . $_limit . ')';
		__Migration::$sql .= "\n `" . $this->fieldName . '` bigint' . $tmp . ',';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function date()
	{
		__Migration::$sql .= "\n `" . $this->fieldName . '` date,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function timestamp()
	{
		__Migration::$sql .= "\n `" . $this->fieldName . '` timestamp,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function time()
	{
		__Migration::$sql .= "\n `" . $this->fieldName . '` time,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function unsigned()
	{
		__Migration::$sql = rtrim(__Migration::$sql, ',') . ' UNSIGNED,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function increment()
	{
		__Migration::$sql = rtrim(__Migration::$sql, ',') . ' AUTO_INCREMENT,';
		return $this;
	}


	/**
	 * @return __FieldTypes $this
	 */
	public function primaryKey()
	{
		__Migration::$sql = rtrim(__Migration::$sql, ',') . ' primary key,';
		return $this;
	}
	/**
	 * @return __FieldTypes $this
	 */
	public function nullable()
	{
		__Migration::$sql = rtrim(__Migration::$sql, ',') . ' null,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function notNull()
	{
		__Migration::$sql = rtrim(__Migration::$sql, ',') . ' not null,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function zeroFill()
	{
		// $tmp = trim(__Migration::$sql);
		// $tmp = preg_replace('/^(.+)(not null)|(null),$/i', '$1', $tmp);
		// __Migration::$sql = rtrim(__Migration::$sql, ',') . ' zerofill,';
		__Migration::$sql = rtrim(__Migration::$sql, ',') . ' zerofill,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function defaultValue($_default, $is_raw = false)
	{
		$tmp = ($is_raw ? $_default : "('$_default')");
		__Migration::$sql = rtrim(__Migration::$sql, ',') . " default $tmp,";
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function unique()
	{
		__Migration::$tmpSql = __Migration::$tmpSql . ' UNIQUE(' . $this->fieldName . ') ,';
		return $this;
	}

	/**
	 * @return __FieldTypes $this
	 */
	public function comment($_text)
	{
		__Migration::$sql = rtrim(__Migration::$sql, ',') . " comment '" . $_text . "' ,";
		return $this;
	}

	/**
	 * @return __ForeignKey $this
	 */
	public function foreignKey()
	{
		return (new __ForeignKey($this->fieldName));
	}
}

class __ForeignKey
{
	private
		$fieldName;

	function __construct(string $_fieldName)
	{
		$this->fieldName = $_fieldName;
	}

	/**
	 * @return __ForeignKey $this
	 */
	public function references(string $_table, string $_fieldName, string $_db = null)
	{
		if (is_null($_db))
			$_table = '`' . $_table . '`';
		else
			$_table = '`' . $_db . '`.`' . $_table . '`';

		__Migration::$tmpSql .= ' FOREIGN KEY (`' . $this->fieldName . '`) REFERENCES ' . $_table . ' (`' . $_fieldName . '`) ,';
		return $this;
	}

	/**
	 * @return __ForeignKeyOptions
	 */
	public function onDelete()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' on delete ,';
		return (new __ForeignKeyOptions());
	}

	/**
	 * @return __ForeignKeyOptions
	 */
	public function onUpdate()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' on update ,';
		return (new __ForeignKeyOptions());
	}

	/**
	 * @return __ForeignKeyOptionsForAll
	 */
	public function onAction()
	{
		return (new __ForeignKeyOptionsForAll());
	}
}

class __ForeignKeyOptions
{
	public function cascade()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' cascade ,';
	}

	public function restrict()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' restrict ,';
	}

	public function noAction()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' no action ,';
	}

	public function setNull()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' set null ,';
	}
}

class __ForeignKeyOptionsForAll
{
	public function cascade()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' on delete cascade on update cascade ,';
	}

	public function restrict()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' on delete cascade on update restrict ,';
	}

	public function noAction()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' on delete cascade on update no action ,';
	}

	public function setNull()
	{
		__Migration::$tmpSql = rtrim(__Migration::$tmpSql, ',') . ' on delete cascade on update set null ,';
	}
}
