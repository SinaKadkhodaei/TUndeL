<?php

namespace __DataLayer\__Driver;

use \__DataLayer\__Driver\__DB;

abstract class __QueryBuilder
{
	protected static
		$table,
		$fieldTypes = null,
		$fieldCasting = null;

	public static
		$primaryKey = 'id',
		$changeValues = true,
		$hiddenFields = [];

	private static
		$allKeys = [];

	public function __construct()
	{
		if (empty(self::$allKeys[static::class] ?? [])) {
			$fieldCasting = &static::$fieldCasting ?? [];
			self::$allKeys[static::class] = array_merge(array_keys($fieldCasting), array_values($fieldCasting));
		}
	}

	public function __set($_name, $_value)
	{
		$condForChangeValue = (!in_array($_name, (static::$fieldCasting ?? [])) || !in_array($_name, self::$allKeys[static::class]));
		if (in_array($_name, static::$hiddenFields))
			return false;

		if (static::$fieldTypes !== null) { // field type
			$fieldTypes = &static::$fieldTypes;
			if (isset($fieldTypes[$_name]))
				if ($_value !== null && $condForChangeValue) {
					if ($fieldTypes[$_name] === TypeTimeStamp)
						$_value = strtotime($_value);
					else
						settype($_value, $fieldTypes[$_name]);
				}
		}

		if (static::$fieldCasting !== null) { // field casting
			$fieldCasting = &static::$fieldCasting;
			if (isset($fieldCasting[$_name]))
				$_name = $fieldCasting[$_name];
		}

		if (static::$changeValues !== false)
			if (method_exists($this, 'changeValue'))
				if ($condForChangeValue)
					$this->{'changeValue'}($_name, $_value);


		$this->{$_name} = $_value;
	}


	/**
	 * @return __SelectFrom
	 */
	public static function select($_columns = null, $_setQuote = false)
	{
		return (new __SelectFrom(get_class(new static()), static::$table, static::$fieldTypes, static::$fieldCasting))->select($_columns, $_setQuote);
	}

	/**
	 * @return int
	 */
	public static function count()
	{
		return (new __SelectFrom(get_class(new static()), static::$table, static::$fieldTypes, static::$fieldCasting))
			->count();
	}

	/**
	 * @return __SelectFrom
	 */
	public static function find($_id)
	{
		return (new __SelectFrom(get_class(new static()), static::$table, static::$fieldTypes, static::$fieldCasting))->where('`' . static::$table . '`.`' . static::$primaryKey . '`=?', [$_id])->first();
	}

	/**
	 * @return __SelectFrom
	 */
	public static function where($_conditions, array $_params = [])
	{
		return (new __SelectFrom(get_class(new static()), static::$table, static::$fieldTypes, static::$fieldCasting))->where($_conditions, $_params);
	}

	/**
	 * @return __SelectFrom
	 */
	public static function firstOrCreate(array $_params, $_returnInserted = true)
	{
		return (new __SelectFrom(get_class(new static()), static::$table, static::$fieldTypes, static::$fieldCasting, static::$primaryKey))->firstOrCreate($_params, $_returnInserted);
	}

	/**
	 * @return __InsertInto
	 */
	public static function insert(array $_params, array $_columns = null)
	{
		return (new __InsertInto(get_class(new static()), static::$table, static::$fieldTypes, static::$fieldCasting, static::$primaryKey))->insert($_params, $_columns);
	}

	/**
	 * @return __Update
	 */
	public static function update(array $_data)
	{
		return (new __Update(static::$table))->set($_data);
	}

	/**
	 * @return __Delete
	 */
	public static function delete()
	{
		return (new __Delete(static::$table));
	}
}

class __SelectFrom
{
	private
		$className,
		$table,
		$fieldTypes,
		$fieldCasting,
		$primaryKey = 'id',
		$columns = '*',
		$where = [
			// '',
			// [] //params
		],
		$joinTable = [],
		$groupBy = '',
		$having = [],
		$orderBy = '',
		$limit = '', // 0,1
		$unionWhere = [
			// '',
			// [] //params
		],
		$unionLimit = '', // 0,1
		$union = null,
		$unionOrderBy = '',
		$uniontype = '';


	function __construct(string $_className = '', string $_tableName = '', &$_fieldTypes = null, &$_fieldCasting = null, string $_primaryKey = 'id')
	{
		$this->className = '\\' . trim($_className, '\\');
		$this->table = '`' . trim($_tableName, '`') . '`';
		$this->fieldTypes = &$_fieldTypes;
		$this->fieldCasting = &$_fieldCasting;
		$this->primaryKey = &$_primaryKey;
	}

	/**
	 * @param mixed $_columns if string $_columns then just use $_columns else formated $_columns
	 * @return __SelectFrom $this
	 */
	public function select($_columns = null, $_setQuote = false)
	{
		if ($_columns === null)
			return $this;

		if (gettype($_columns) === 'string')
			$this->columns = $_columns;
		else {
			if ($_setQuote === true)
				$_columns =
					array_map(
						function ($_column) {
							if (gettype($_column) === 'string')
								return '`' . $_column . '`';
							else
								return $_column;
						},
						$_columns
					);

			$this->columns = implode(',', $_columns);
		}
		return $this;
	}

	/**
	 * @param mixed $_columns if string $_columns then just use $_columns else formated $_columns
	 * @return __SelectFrom $this
	 */
	public function addSelect($_columns, $_setQuote = false)
	{
		if (gettype($_columns) === 'string')
			$this->columns .= ',' . $_columns;
		else {
			if ($_setQuote === true)
				$_columns =
					array_map(
						function ($_column) {
							if (gettype($_column) === 'string')
								return '`' . $_column . '`';
							else
								return $_column;
						},
						$_columns
					);

			$this->columns .= ',' . implode(',', $_columns);
		}
		return $this;
	}

	/**
	 * @param string $_tableName
	 * @return __SelectFrom $this
	 */
	public function from(string $_tableName)
	{
		$this->table = '`' . trim($_tableName, '`') . '`';
		return $this;
	}

	/**
	 * @param mixed $_conditions if string $_conditions then just use $_conditions else formated $_conditions
	 * @return __SelectFrom $this
	 */
	public function where($_conditions, array $_params = [])
	{
		if (isset($this->where[0])) {
			$this->where[0] = $this->where[0] . ' and (' . $_conditions . ')';
			$this->where[1] = array_merge($this->where[1], $_params);
		} else {
			$this->where[0] = '(' . $_conditions . ')';
			$this->where[1] = $_params;
		}

		// if (gettype($_conditions) === 'string') {
		// 	$this->where[0] = $_conditions;
		// 	$this->where[1] = $_params;
		// }
		// else {
		// 	$conditions = '';
		// 	$_conditions =
		// 		array_map(
		// 			function ($_condition) {
		// 				if (gettype($_condition) === 'string')
		// 					return '`' . $_condition . '`';
		// 				else
		// 					return $_condition;
		// 			},
		// 			$_conditions
		// 		);

		// 	$this->columns = implode(',', $_conditions);
		// }
		/**
		 * [
		 * 	[
		 * 		'name','in',[]
		 * 	],
		 * 	[
		 * 		'id','=',5
		 * 	],
		 * ]
		 */
		return $this;
	}


	/**
	 * @param mixed $_conditions if string $_conditions then just use $_conditions else formated $_conditions
	 * @return __SelectFrom $this
	 */
	public function orWhere($_conditions, array $_params = [])
	{
		if (isset($this->where[0])) {
			$this->where[0] = $this->where[0] . ' or (' . $_conditions . ')';
			$this->where[1] = array_merge($this->where[1], $_params);
		} else {
			$this->where[0] = '(' . $_conditions . ')';
			$this->where[1] = $_params;
		}

		return $this;
	}

	public function whereExists($_conditions, array $_params = [])
	{
		if (is_array($_conditions)) {
			$_params = $_conditions[1];
			$_conditions = $_conditions[0];
		}
		if (isset($this->where[0])) {
			$this->where[0] = $this->where[0] . ' and exists(' . $_conditions . ')';
			$this->where[1] = array_merge($this->where[1], $_params);
		} else {
			$this->where[0] = 'exists(' . $_conditions . ')';
			$this->where[1] = $_params;
		}

		return $this;
	}

	/**
	 * @param mixed $_conditions if string $_conditions then just use $_conditions else formated $_conditions
	 * @return __SelectFrom $this
	 */
	public function unionWhere($_conditions, array $_params = [])
	{
		if (isset($this->unionWhere[0])) {
			$this->unionWhere[0] =  $this->unionWhere[0] . ' and (' . $_conditions . ')';
			$this->unionWhere[1] = array_merge($this->unionWhere[1], $_params);
		} else {
			$this->unionWhere[0] = '(' . $_conditions . ')';
			$this->unionWhere[1] = $_params;
		}

		return $this;
	}

	/**
	 * @return int $this
	 */
	public function count()
	{
		if ($this->union === null && empty($this->having)) {
			$tmp = $this->columns;
			$this->columns .= ',count(1) as cnt';
			$res = ((int) ($this->get()[0]->cnt ?? 0));
			$this->columns = $tmp;
		} else {
			$q = $this->sql();
			$q[0] = 'select count(1) as cnt from (' . $q[0] . ') as depth_count';
			$res = __DB::instance()->read($this->className, $q[0], $q[1]);
			$res = ((int) $res[0]->cnt);
		}
		return $res;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function innerJoin(string $_tableName)
	{
		$this->joinTable[] = [
			$_tableName,
			'inner',
		];
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function rightJoin(string $_tableName)
	{
		$this->joinTable[] = [
			$_tableName,
			'right',
		];
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function leftJoin(string $_tableName)
	{
		$this->joinTable[] = [
			$_tableName,
			'left',
		];
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function join(string $_tableName)
	{
		$this->joinTable[] = [
			$_tableName,
			'',
		];
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function on($_conditions, array $_params = [])
	{
		$point = abs(count($this->joinTable) - 1);
		$this->joinTable[$point][2] = $_conditions;
		$this->joinTable[$point][3] = $_params;
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function orderBy(array $_columns = [])
	{
		$res = '';
		foreach ($_columns as $key => $column) {
			if (is_numeric($key))
				$res .= $column . ',';
			else
				$res .= $key . ' ' . $column . ',';
		}

		$res = trim($res, ',');
		$this->orderBy = $res;
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function groupBy(array $_columns = [])
	{
		$this->groupBy = implode(',', $_columns);
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function having(string $_conditions, array $_params = [])
	{
		if (isset($this->having[0])) {
			$this->having[0] = $this->having[0] . ' and (' . $_conditions . ')';
			$this->having[1] = array_merge($this->having[1], $_params);
		} else {
			$this->having[0] = '(' . $_conditions . ')';
			$this->having[1] = $_params;
		}
		return $this;
	}

	/**
	 * @return __SelectFrom new __SelectFrom()
	 */
	public function union($_all = false)
	{
		$this->union = (new __SelectFrom()); // raw select from
		if ($_all === true)
			$this->uniontype = 'all';

		return $this->union;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function unionOrderBy(array $_columns = [])
	{
		$res = '';
		foreach ($_columns as $key => $column) {
			if (is_numeric($key))
				$res .= $column . ',';
			else
				$res .= $key . ' ' . $column . ',';
		}

		$res = trim($res, ',');
		$this->unionOrderBy = $res;
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function limit(int $_skip, int  $_take)
	{
		$this->limit = implode(',', [$_skip, $_take]);
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function withoutLimit()
	{
		$this->limit = '';
		return $this;
	}

	/**
	 * @return __SelectFrom $this
	 */
	public function unionLimit(int $_skip, int  $_take)
	{
		$this->unionLimit = implode(',', [$_skip, $_take]);
		return $this;
	}

	public function sql()
	{
		$params = [];
		$query = 'select ' . $this->columns . ' from ' . $this->table;

		foreach ($this->joinTable as $join) {
			$query .=  $join[1] . ' join ' . $join[0];
			$query .= ' on (' . $join[2] . ')';
			$params = array_merge($params, $join[3]);
		}

		if (count($this->where) > 0) {
			$query .= ' where (' . $this->where[0] . ')';
			$params = array_merge($params, $this->where[1]);
		}

		if ($this->groupBy !== '')
			$query .= ' group by ' . $this->groupBy;

		if (count($this->having) > 0) {
			$query .= ' having (' . $this->having[0] . ')';
			$params = array_merge($params, $this->having[1]);
		}

		if ($this->orderBy !== '')
			$query .= ' order by ' . $this->orderBy;

		if ($this->limit !== '')
			$query .= ' limit ' . $this->limit;

		if ($this->union !== null) {
			$unionQuery = $this->union->sql();
			$query = '(' . $query . ') union ' . $this->uniontype . ' (' . $unionQuery[0] . ')';
			$params = array_merge($params, $unionQuery[1]);

			if (count($this->unionWhere) > 0) {
				$query = 'select * from (' . $query . ') as depth_1';
				$query .= ' where (' . $this->unionWhere[0] . ')';
				$params = array_merge($params, $this->unionWhere[1]);
			}

			if ($this->unionOrderBy !== '')
				$query .= ' order by ' . $this->unionOrderBy;

			if ($this->unionLimit !== '')
				$query .= ' limit ' . $this->unionLimit;
		}

		return [
			$query,
			$params,
		];
	}

	public function get(bool $_returnArray = false)
	{
		$q = $this->sql();
		$res = __DB::instance()->read($this->className, $q[0], $q[1]);

		return $res;
	}

	/**
	 * @return object $this
	 */
	public function first(bool $_returnArray = false)
	{
		if ($this->union === null)
			$this->limit = '0,1';
		else
			$this->unionLimit = '0,1';

		$res = $this->get($_returnArray);
		if (count($res) > 0)
			$res = $res[0];
		else
			$res = null;
		return $res;
	}

	/**
	 * @return object $this
	 */
	public function firstOrCreate(array $_params, $_returnInserted = true)
	{
		$values = array_values($_params);
		$params = [];
		$q = 'insert into ' . $this->table;
		$q .= ' (' . implode(',', array_keys($_params)) . ')';
		$q .= ' select * from';

		$tmpColumns = [];
		foreach ($_params as $k => $v)
			$tmpColumns[] = '? as ' . $k;

		$q .= ' (select ' . implode(',', $tmpColumns) . ') as depth_1';
		$params = array_merge($params, $values);
		$keys = array_map(
			function ($_value) {
				return 'depth_2.' . $_value . '=?';
			},
			array_keys($_params)
		);
		$keys = implode(' and ', $keys);
		$q .= ' where not exists(select 1 from ' . $this->table . ' as depth_2 where (' . $keys . '))';
		$params = array_merge($params, $params);
		$q .= ' limit 0,1';
		$res = __DB::instance()->write($q, $params, true);

		if ($res['status'] === false) {
			if ($res['rowCount'] < 1) { // if true then query was no effected
				$keys = array_map(
					function ($_value) {
						return $_value . '=?';
					},
					array_keys($_params)
				);
				$keys = implode(' and ', $keys);
				$tmp = (new __SelectFrom($this->className, $this->table, $this->fieldTypes, $this->fieldCasting))
					->where($keys, $values)
					->first();

				if ($_returnInserted === false) {
					$id = $this->primaryKey;
					if ($this->fieldCasting !== null)
						$id = $this->fieldCasting[$this->primaryKey] ?? null;

					$res['insertedKeys'] = $tmp->{$id} ?? null;
				} else
					$res = $tmp;
			}
		} else {
			if ($_returnInserted === true)
				$res = (new __SelectFrom($this->className, $this->table, $this->fieldTypes, $this->fieldCasting))
					->where($this->primaryKey . '=' . $res['lastId'])
					->first();
			else
				$res['insertedKeys'] = $res['lastId'];
		}
		return $res;
	}
}

class __InsertInto
{
	private
		$className,
		$table,
		$fieldTypes,
		$fieldCasting,
		$primaryKey,
		$params = [],
		$columns = [],
		$joinTable = [],
		$joinOn =  [
			// [ // first join
			// 	'',
			// 	[] //params
			// ]
		],
		$groupBy = '',
		$having = [],
		$onDuplicateParams = [],
		$onDuplicateConditions = '',
		$whereConditions = [];

	function __construct(string $_className, string $_tableName, &$_fieldTypes = null, &$_fieldCasting = null, string $_primaryKey = 'id')
	{
		$this->className = '\\' . $_className;
		$this->table = '`' . trim($_tableName, '`') . '`';
		$this->fieldTypes = &$_fieldTypes;
		$this->fieldCasting = &$_fieldCasting;
		$this->primaryKey = '`' . $_primaryKey . '`';
	}

	/**
	 * @param array $_data data that want to insert
	 * @return __InsertInto $this
	 */
	public function insert(array $_params, array $_columns = null)
	{
		$this->params = array_values($_params);

		if (is_null($_columns)) {
			if (isset($_params[0]))
				$this->columns = array_keys($_params[0]);
			else
				$this->columns = array_keys($_params);
		} else
			$this->columns = $_columns;

		return $this;
	}

	/**
	 * @param array $_data data that want to insert
	 * @return __InsertInto $this
	 */
	public function onDuplicate($_conditions, array $_params = [])
	{
		$this->onDuplicateParams = $_params;
		$this->onDuplicateConditions = $_conditions;
		return $this;
	}

	/**
	 * @return __InsertInto $this
	 */
	public function join(string $_tableName)
	{
		$this->joinTable[] = $_tableName;
		return $this;
	}

	/**
	 * @return __InsertInto $this
	 */
	public function on($_conditions, array $_params = [])
	{
		$this->joinOn[][0] = $_conditions;
		$this->joinOn[count($this->joinOn) - 1][1] = $_params;
		return $this;
	}

	/**
	 * @return __InsertInto $this
	 */
	public function groupBy(array $_columns = [])
	{
		$this->groupBy = implode(',', $_columns);
		return $this;
	}

	/**
	 * @return __InsertInto $this
	 */
	public function having(string $_conditions, array $_params = [])
	{
		$this->having[0] = $_conditions;
		$this->having[1] = $_params;
		return $this;
	}


	/**
	 * @param array $_data data that want to insert
	 * @return __InsertInto $this
	 */
	public function where($_conditions, array $_params = [])
	{
		if (isset($this->whereConditions[0])) {
			$this->whereConditions[0] = $this->whereConditions[0] . ' and (' . $_conditions . ')';
			$this->whereConditions[1] = array_merge($this->whereConditions[1], $_params);
		} else {
			$this->whereConditions[0] = '(' . $_conditions . ')';
			$this->whereConditions[1] = $_params;
		}

		return $this;
	}

	/**
	 * @param array $_data data that want to insert
	 * @return __InsertInto $this
	 */
	public function orWhere($_conditions, array $_params = [])
	{
		if (isset($this->whereConditions[0])) {
			$this->whereConditions[0] = $this->whereConditions[0] . ' or (' . $_conditions . ')';
			$this->whereConditions[1] = array_merge($this->whereConditions[1], $_params);
		} else {
			$this->whereConditions[0] = '(' . $_conditions . ')';
			$this->whereConditions[1] = $_params;
		}

		return $this;
	}

	// /**
	//  * @param array $_columns columns that want to insert
	//  * @return __InsertInto $this
	//  */
	// public function into(array $_columns = [])
	// {
	// 	$this->columns = $_columns;
	// 	return $this;
	// }

	public function sql()
	{
		$params = [];
		$data = null;
		$query = '';
		if (empty($this->whereConditions) && count($this->joinTable) < 1) {
			if (gettype($this->params[0]) === 'array') {

				$params = [];
				$sample = '(' . implode(',', array_fill(0, count($this->params[0]), '?')) . ')';
				$data =
					array_map(
						function ($_row) use (&$params, &$sample) {
							$params = array_merge($params, array_values($_row));
							return $sample;
						},
						$this->params
					);

				$data = implode(',', $data);
			} else {
				$data = '(' . implode(',', array_fill(0, count($this->params), '?')) . ')';
				$params = $this->params;
			}

			$query = 'insert into ' . $this->table . ' ';

			if (count($this->columns) > 0)
				$query .=
					'(' .
					implode(
						',',
						array_map(
							function ($_column) {
								return '`' . $_column . '`';
							},
							$this->columns
						)
					)
					. ')';

			$query .= 'values ' . $data;
		} else {
			$query = 'insert into ' . $this->table . ' ';
			if (count($this->columns) > 0)
				$query .=
					'(' .
					implode(
						',',
						array_map(
							function ($_column) {
								return '`' . $_column . '`';
							},
							$this->columns
						)
					)
					. ')';

			$query .= 'select depth_1.* from ';
			$columns = $this->params;

			if (!isset($columns[0])) {
				normalColumns: {
					array_walk(
						$columns,
						function (&$_value, $_key) use (&$params) {
							$params[] = $_value;
							$_value = '? as ' . $this->columns[$_key];
						}
					);
					$columns = implode(',', $columns);
				}
			} else {
				if (!is_array($columns[0]))
					goto normalColumns;

				array_walk(
					$columns,
					function (&$_value) use (&$params, &$columns) {
						array_walk(
							$_value,
							function (&$_value, $_key) use (&$params) {
								$params[] = $_value;
								$_value = '? as ' . $this->columns[$_key];
							}
						);
						$_value = implode(',', $_value);
					}
				);
				$columns = implode(' union select ', $columns);
			}

			$query .= ' (select ' . $columns . ') as depth_1 ';

			$i4J = 0;
			foreach ($this->joinTable as $table) {
				$query .= ' join ' . $table;
				$query .= ' on (' . $this->joinOn[$i4J][0] . ')';
				$params = array_merge($params, $this->joinOn[$i4J][1]);
				$i4J++;
			}

			if (!empty($this->whereConditions)) {
				$query .= ' where (' . $this->whereConditions[0] . ')';
				$params = array_merge($params, $this->whereConditions[1]);
			}

			if ($this->groupBy !== '')
				$query .= ' group by ' . $this->groupBy;

			if (count($this->having) > 0) {
				$query .= ' having (' . $this->having[0] . ')';
				$params = array_merge($params, $this->having[1]);
			}
		}

		if ($this->onDuplicateConditions !== '') {
			$query .= 'on duplicate key update ' . $this->onDuplicateConditions;
			$params = array_merge($params, array_values($this->onDuplicateParams));
		}

		return [
			$query,
			$params,
		];
	}

	public function run(bool $_returnInserted = false, int $_pkStep = 1, bool $_detail = true)
	{
		$q = $this->sql();
		$res = __DB::instance()->write($q[0], $q[1], true);

		if ($res['status'] === false)
			if ($res['rowCount'] < 1) { // if true then query was no effected
				$err = ((int) $res['error'][0]);
				if ($err === 0)
					return -1;
				elseif ($err === 23000)
					return -2;
				return false;
			}

		if ($_returnInserted === true)
			$res = (new __SelectFrom($this->className, $this->table, $this->fieldTypes, $this->fieldCasting))
				->where($this->primaryKey . '>=' . $res['lastId'])
				->get();
		else
			$res['insertedKeys'] = range($res['lastId'], ($res['lastId'] - 1) + $res['rowCount'], $_pkStep);

		return $res;
	}
}

class __Update
{
	private
		$table,
		$data = [],
		$joinTable = [],
		$joinOn =  [
			// [ // first join
			// 	'',
			// 	[] //params
			// ]
		],
		$where = [
			// '',
			// [] //params
		],
		$groupBy = '',
		$having = [];


	function __construct(string $_tableName)
	{
		$this->table = '`' . trim($_tableName, '`') . '`';
	}

	/**
	 * @param array $_data data that want to insert
	 * @return __Update $this
	 */
	public function set(array $_data)
	{
		$this->data = $_data;
		return $this;
	}

	/**
	 * @return __Update $this
	 */
	public function join(string $_tableName)
	{
		$this->joinTable[] = $_tableName;
		return $this;
	}

	/**
	 * @return __Update $this
	 */
	public function on($_conditions, array $_params = [])
	{
		$this->joinOn[][0] = $_conditions;
		$this->joinOn[count($this->joinOn) - 1][1] = $_params;
		return $this;
	}

	/**
	 * @return __Update $this
	 */
	public function where($_conditions, array $_params = [])
	{
		if (isset($this->where[0])) {
			$this->where[0] = $this->where[0] . ' and (' . $_conditions . ')';
			$this->where[1] = array_merge($this->where[1], $_params);
		} else {
			$this->where[0] = '(' . $_conditions . ')';
			$this->where[1] = $_params;
		}

		return $this;
	}

	/**
	 * @return __Update $this
	 */
	public function orWhere($_conditions, array $_params = [])
	{
		if (isset($this->where[0])) {
			$this->where[0] = $this->where[0] . ' or (' . $_conditions . ')';
			$this->where[1] = array_merge($this->where[1], $_params);
		} else {
			$this->where[0] = '(' . $_conditions . ')';
			$this->where[1] = $_params;
		}

		return $this;
	}

	public function whereExists($_conditions, array $_params = [])
	{
		if (is_array($_conditions)) {
			$_params = $_conditions[1];
			$_conditions = $_conditions[0];
		}
		if (isset($this->where[0])) {
			$this->where[0] = $this->where[0] . ' and exists(' . $_conditions . ')';
			$this->where[1] = array_merge($this->where[1], $_params);
		} else {
			$this->where[0] = 'exists(' . $_conditions . ')';
			$this->where[1] = $_params;
		}

		return $this;
	}

	/**
	 * @return __Update $this
	 */
	public function groupBy(array $_columns = [])
	{
		$this->groupBy = implode(',', $_columns);
		return $this;
	}

	/**
	 * @return __Update $this
	 */
	public function having(string $_conditions, array $_params = [])
	{
		$this->having[0] = $_conditions;
		$this->having[1] = $_params;
		return $this;
	}


	public function sql()
	{
		$params = [];
		$query = 'update ' . $this->table;

		$i4J = 0;
		foreach ($this->joinTable as $table) {
			$query .= ' join ' . $table;
			$query .= ' on (' . $this->joinOn[$i4J][0] . ')';
			$params = array_merge($params, $this->joinOn[$i4J][1]);
			$i4J++;
		}

		array_walk(
			$this->data,
			function (&$_value, $_field) use (&$params) {
				$params[] = $_value;
				$_value = $_field . '=?';
			}
		);

		$sets = implode(',', $this->data);
		$query .=  ' set ' . $sets;

		if (count($this->where) > 0) {
			$query .= ' where (' . $this->where[0] . ')';
			$params = array_merge($params, $this->where[1]);
		}

		if ($this->groupBy !== '')
			$query .= ' group by ' . $this->groupBy;

		if (count($this->having) > 0) {
			$query .= ' having (' . $this->having[0] . ')';
			$params = array_merge($params, $this->having[1]);
		}

		return [
			$query,
			$params,
		];
	}

	public function run()
	{
		$q = $this->sql();
		$res = __DB::instance()->write($q[0], $q[1], true);

		if ($res['status'] === false)
			if ($res['rowCount'] < 1) { // if true then query was no effected
				$err = ((int) $res['error'][0]);
				if ($err === 0)
					return -1;
				elseif ($err === 23000)
					return -2;
				return false;
			}

		return $res['rowCount'];
	}
}

class __Delete
{
	private
		$table,
		$joinTable = [],
		$joinOn =  [
			// [ // first join
			// 	'',
			// 	[] //params
			// ]
		],
		$where = [
			// '',
			// [] //params
		];


	function __construct(string $_tableName)
	{
		$this->table = '`' . trim($_tableName, '`') . '`';
	}


	/**
	 * @return __Delete $this
	 */
	public function join(string $_tableName)
	{
		$this->joinTable[] = $_tableName;
		return $this;
	}

	/**
	 * @return __Delete $this
	 */
	public function on($_conditions, array $_params = [])
	{
		$this->joinOn[][0] = $_conditions;
		$this->joinOn[count($this->joinOn) - 1][1] = $_params;
		return $this;
	}

	/**
	 * @return __Delete $this
	 */
	public function where($_conditions, array $_params = [])
	{
		if (isset($this->where[0])) {
			$this->where[0] = $this->where[0] . ' and (' . $_conditions . ')';
			$this->where[1] = array_merge($this->where[1], $_params);
		} else {
			$this->where[0] = '(' . $_conditions . ')';
			$this->where[1] = $_params;
		}

		return $this;
	}

	/**
	 * @return __Delete $this
	 */
	public function orWhere($_conditions, array $_params = [])
	{
		if (isset($this->where[0])) {
			$this->where[0] = $this->where[0] . ' or (' . $_conditions . ')';
			$this->where[1] = array_merge($this->where[1], $_params);
		} else {
			$this->where[0] = '(' . $_conditions . ')';
			$this->where[1] = $_params;
		}

		return $this;
	}

	public function sql()
	{
		$params = [];

		$query = 'delete ' . $this->table . ' from ' . $this->table;

		$i4J = 0;
		foreach ($this->joinTable as $table) {
			$query .= ' join ' . $table;
			$query .= ' on (' . $this->joinOn[$i4J][0] . ')';
			$params = array_merge($params, $this->joinOn[$i4J][1]);
			$i4J++;
		}

		if (count($this->where) > 0) {
			$query .= ' where (' . $this->where[0] . ')';
			$params = array_merge($params, $this->where[1]);
		}

		return [
			$query,
			$params,
		];
	}

	public function run()
	{
		$q = $this->sql();
		$res = __DB::instance()->write($q[0], $q[1], true);

		if ($res['status'] === false)
			if ($res['rowCount'] < 1) { // if true then query was no effected
				if (((int) $res['error'][0]) === 0)
					return -1;
				return false;
			}

		return $res['rowCount'];
	}
}
