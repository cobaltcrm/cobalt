<?php
/*------------------------------------------------------------------------
# Cobalt
# ------------------------------------------------------------------------
# @author Cobalt
# @copyright Copyright (C) 2012 cobaltcrm.org All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Website: http://www.cobaltcrm.org
-------------------------------------------------------------------------*/

namespace Cobalt\Table;

use Joomla\Database\DatabaseDriver;
use Joomla\Database\DatabaseQuery;

// no direct access
defined( '_CEXEC' ) or die( 'Restricted access' );

/**
 * Abstract Database Table class
 *
 * @since  1.0
 */
class AbstractTable implements \IteratorAggregate
{
	/**
	 * Name of the database table to model.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $tableName = '';

	/**
	 * Name of the primary key fields in the table.
	 *
	 * @var    array
	 * @since  1.0
	 */
	protected $tableKeys = array();

	/**
	 * Indicates that the primary keys autoincrement.
	 *
	 * @var    boolean
	 * @since  1.0
	 */
	protected $autoIncrement = true;

	/**
	 * The fields of the database table.
	 *
	 * @var    \stdClass
	 * @since  1.0
	 */
	protected $tableFields = null;

	/**
	 * DatabaseDriver object.
	 *
	 * @var    DatabaseDriver
	 * @since  1.0
	 */
	protected $db;

	/**
	 * Object constructor to set table and key fields.
	 *
	 * In most cases this will be overridden by child classes to explicitly set the table and key fields for a particular database table.
	 *
	 * @param   string                  $table  Name of the table to model.
	 * @param   array|\stdClass|string  $keys   Name of the primary key field in the table or array of field names that compose the primary key.
	 * @param   DatabaseDriver          $db     DatabaseDriver object.
	 *
	 * @since   1.0
	 */
	public function __construct($table, $keys, DatabaseDriver $db)
	{
		// Set internal variables.
		$this->tableName   = $table;
		$this->db          = $db;
		$this->tableFields = new \stdClass;

		// Set the key to be an array.
		if (is_string($keys))
		{
			$keys = array($keys);
		}
		elseif (is_object($keys))
		{
			$keys = (array) $keys;
		}

		$this->tableKeys = $keys;

		$this->autoIncrement = (count($keys) == 1) ? true : false;

		// Initialise the table properties.
		$fields = $this->getFields();

		if ($fields)
		{
			foreach ($fields as $name => $v)
			{
				// Add the field if it is not already present.
				$this->tableFields->$name = null;
			}
		}
	}

	/**
	 * Magic setter to set a table field.
	 *
	 * @param   string  $key    The key name.
	 * @param   mixed   $value  The value to set.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public function __set($key, $value)
	{
		// @TODO: Don't set a property which is not defined in database table.
		// if (!(isset($this->tableFields->$key) || is_null($this->tableFields->$key)))
		// {
		// 	throw new \InvalidArgumentException(__METHOD__ . ' - Set unknown property: ' . $key);
		// }

		$this->tableFields->$key = $value;
	}

	/**
	 * Magic getter to get a table field.
	 *
	 * @param   string  $key  The key name.
	 *
	 * @return  mixed
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public function __get($key)
	{
		if (isset($this->tableFields->$key) || is_null($this->tableFields->$key))
		{
			return $this->tableFields->$key;
		}

		throw new \InvalidArgumentException(__METHOD__ . ' - Get unknown property: ' . $key);
	}

	/**
	 * Method to provide a shortcut to binding, checking and storing a AbstractTable instance to the database table.
	 *
	 * The method will check a row in once the data has been stored and if an ordering filter is present will attempt to reorder the table rows
	 * based on the filter.  The ordering filter is an instance property name.  The rows that will be reordered are those whose value matches
	 * the AbstractTable instance for the property specified.
	 *
	 * @param   array|\stdClass  $src     An associative array or object to bind to the AbstractTable instance.
	 * @param   array|string     $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function save($src, $ignore = '')
	{
		$this
			// Attempt to bind the source to the instance.
			->bind($src, $ignore)
			// Run any sanity checks on the instance and verify that it is ready for storage.
			->check()
			// Attempt to store the properties to the database table.
			->store();

		return $this;
	}

	/**
	 * Method to bind an associative array or object to the AbstractTable instance.
	 *
	 * This method only binds properties that are publicly accessible and optionally takes an array of properties to ignore when binding.
	 *
	 * @param   array|\stdClass  $src     An associative array or object to bind to the AbstractTable instance.
	 * @param   array|string     $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	public function bind($src, $ignore = array())
	{
		// If the source value is not an array or object return false.
		if (!is_object($src) && !is_array($src))
		{
			throw new \InvalidArgumentException(sprintf('%s::bind(*%s*)', get_class($this), gettype($src)));
		}

		// If the source value is an object, get its accessible properties.
		if (is_object($src))
		{
			$src = get_object_vars($src);
		}

		// If the ignore value is a string, explode it over spaces.
		if (!is_array($ignore))
		{
			$ignore = explode(' ', $ignore);
		}

		// Bind the source value, excluding the ignored fields.
		foreach ($this->tableFields as $k => $v)
		{
			// Only process fields that are in the source array and  not in the ignore array.
			if (array_key_exists($k, $src) && !in_array($k, $ignore))
			{
				$this->tableFields->$k = $src[$k];
			}
		}

		return $this;
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields to the AbstractTable instance properties.
	 *
	 * @param   array|\stdClass|string  $keys   An optional primary key value to load the row by, or an array of fields to match.  If not set the
	 *                                          instance property value is used.
	 * @param   boolean                 $reset  True to reset the default values before loading the new row.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 * @throws  \UnexpectedValueException
	 * @throws  \InvalidArgumentException
	 */
	public function load($keys = null, $reset = true)
	{
		if (empty($keys))
		{
			$empty = true;
			$keys  = array();

			// If empty, use the value of the current key
			foreach ($this->tableKeys as $key)
			{
				$empty      = $empty && empty($this->$key);
				$keys[$key] = $this->$key;
			}

			// If empty primary key there's is no need to load anything
			if ($empty)
			{
				return $this;
			}
		}
		elseif (!is_array($keys))
		{
			// Load by primary key.
			$keyCount = count($this->tableKeys);

			if (!$keyCount)
			{
				throw new \RuntimeException('No table keys defined.');
			}

			if ($keyCount > 1)
			{
				throw new \InvalidArgumentException('Table has multiple primary keys specified, only one primary key value provided.');
			}

			$keys = array($this->getKeyName() => $keys);
		}

		if ($reset)
		{
			$this->reset();
		}

		// Initialise the query.
		$query = $this->db->getQuery(true);
		$query->select('*');
		$query->from($this->db->quoteName($this->tableName));

		foreach ($keys as $field => $value)
		{
			// Check that $field is in the table.
			if (!(isset($this->tableFields->$field) || is_null($this->tableFields->$field)))
			{
				throw new \UnexpectedValueException(sprintf('Missing field in database: %s &#160; %s.', get_class($this), $field));
			}

			// Add the search tuple to the query.
			$query->where($this->db->quoteName($field) . ' = ' . $this->db->quote($value));
		}

		$this->db->setQuery($query);

		$row = $this->db->loadAssoc();

		// Check that we have a result.
		if (empty($row))
		{
			throw new \RuntimeException(__METHOD__ . ' can not bind.');
		}

		// Bind the object with the row and return.
		return $this->bind($row);
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   string|null  $pKey  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException
	 */
	public function delete($pKey = null)
	{
		$key = $this->getKeyName();

		$pKey = (is_null($pKey)) ? $this->$key : $pKey;



		// If no primary key is given, return false.
		if ($pKey === null)
		{
			throw new \UnexpectedValueException('Null primary key not allowed.');
		}

		$query = $this->db->getQuery(true)
			->delete($this->db->quoteName($this->tableName));

		if (is_array($pKey))
		{
			$query->where($this->db->quoteName($key) . ' IN (' . implode(',', $pKey) . ')');
		}
		else
		{
			$query->where($this->db->quoteName($key) . ' = ' . $this->db->quote($pKey));
		}

		// Delete the row by primary key.
		$this->db->setQuery($query)->execute();

		return $this;
	}

	/**
	 * Method to reset class properties to the defaults set in the class definition.
	 *
	 * It will ignore the primary key as well as any private class properties.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function reset()
	{
		// Get the default values for the class from the table.
		foreach ($this->getFields() as $k => $v)
		{
			// If the property is not the primary key, reset it.
			if (!in_array($k, $this->tableKeys))
			{
				$this->$k = $v->Default;
			}
		}
	}

	/**
	 * Method to perform sanity checks on the AbstractTable instance properties to ensure they are safe to store in the database.
	 *
	 * Child classes should override this method to make sure the data they are storing in the database is safe and as expected before storage.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function check()
	{
		return $this;
	}

	/**
	 * Method to store a row in the database from the AbstractTable instance properties.
	 *
	 * If a primary key value is set the row with that primary key value will be updated with the instance property values.  If no primary key value
	 * is set a new row will be inserted into the database with the properties from the AbstractTable instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function store($updateNulls = false)
	{
		// If a primary key exists update the object, otherwise insert it.
		if ($this->hasPrimaryKey())
		{
			$this->db->updateObject($this->tableName, $this->tableFields, $this->tableKeys, $updateNulls);
		}
		else
		{
			$this->db->insertObject($this->tableName, $this->tableFields, $this->tableKeys[0]);
		}

		return $this;
	}

	/**
	 * Validate that the primary key has been set.
	 *
	 * @return  boolean  True if the primary key(s) have been set.
	 *
	 * @since   1.0
	 */
	public function hasPrimaryKey()
	{
		if ($this->autoIncrement)
		{
			$empty = true;

			foreach ($this->tableKeys as $key)
			{
				$empty = $empty && !$this->$key;
			}
		}
		else
		{
			$query = $this->db->getQuery(true);
			$query->select('COUNT(*)');
			$query->from($this->tableName);
			$this->appendPrimaryKeys($query);

			$this->db->setQuery($query);
			$count = $this->db->loadResult();

			if ($count == 1)
			{
				$empty = false;
			}
			else
			{
				$empty = true;
			}
		}

		return !$empty;
	}

	/**
	 * Method to append the primary keys for this table to a query.
	 *
	 * @param   DatabaseQuery      $query  A query object to append.
	 * @param   string|array|null  $pk     Optional primary key parameter.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function appendPrimaryKeys($query, $pk = null)
	{
		if (is_null($pk))
		{
			foreach ($this->tableKeys as $k)
			{
				$query->where($this->db->quoteName($k) . ' = ' . $this->db->quote($this->$k));
			}
		}
		else
		{
			if (is_string($pk))
			{
				$pk = array($this->tableKeys[0] => $pk);
			}

			$pk = (object) $pk;

			foreach ($this->tableKeys AS $k)
			{
				$query->where($this->db->quoteName($k) . ' = ' . $this->db->quote($pk->$k));
			}
		}

		return $this;
	}

	/**
	 * Method to get the primary key field name for the table.
	 *
	 * @param   boolean  $multiple  True to return all primary keys (as an array) or false to return just the first one (as a string).
	 *
	 * @return  array|string  Array of primary key field names or string containing the first primary key field.
	 *
	 * @since   1.0
	 */
	public function getKeyName($multiple = false)
	{
		// Count the number of keys
		if (count($this->tableKeys))
		{
			// If we want multiple keys, return the raw array.
			if ($multiple)
			{
				return $this->tableKeys;
			}

			// If we want the standard method, just return the first key.
			return $this->tableKeys[0];
		}

		return '';
	}

	/**
	 * Get the columns from database table.
	 *
	 * @return  array  An array of the field names.
	 *
	 * @since   1.0
	 * @throws  \UnexpectedValueException
	 */
	public function getFields()
	{
		static $cache = null;

		if ($cache === null)
		{
			// Lookup the fields for this table only once.
			$fields = $this->db->getTableColumns($this->tableName, false);

			if (empty($fields))
			{
				throw new \UnexpectedValueException(sprintf('No columns found for %s table', $this->tableName));
			}

			$cache = $fields;
		}

		return $cache;
	}

	/**
	 * Get the table properties
	 *
	 * @return  \stdClass
	 *
	 * @since   1.0
	 */
	public function getProperties()
	{
		return $this->tableFields;
	}

	/**
	 * Get the table name.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function getTableName()
	{
		return $this->tableName;
	}

	/**
	 * Get an iterator object.
	 *
	 * @return  \ArrayIterator
	 *
	 * @since   1.0
	 */
	public function getIterator()
	{
		return new \ArrayIterator($this->tableFields);
	}

	/**
	 * Clone the table.
	 *
	 * @return  \ArrayIterator
	 *
	 * @since   1.0
	 */
	public function __clone()
	{
		return $this->getIterator();
	}
}
