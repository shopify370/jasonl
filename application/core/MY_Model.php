<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * MY_Model
 *
 * The standard CRUD functions that can be
 * used and overriden by module models. This helps to maintain
 * a standard interface to program to, and makes module creation
 * faster.
 *
 * @package    rc
 * @subpackage MY_Model
 * @category   Models
 *
 */
class MY_Model extends CI_Model {

    /**
     * Stores custom errors that can be used in UI error reporting.
     *
     * @var string
     */
    public $error = '';

    /**
     * The name of the db table this model primarily uses.
     *
     * @var string
     * @access protected
     */
    protected $table = '';

    /**
     * The primary key of the table. Used as the 'id' throughout.
     *
     * @var string
     * @access protected
     */
    protected $key = 'id';

    /**
     * Field prefix to use in the DB table.
     *
     * @var string
     * @access protected
     */
    protected $field_prefix = '';

    /**
     * Field name to use to the created time column in the DB table.
     *
     * @var string
     * @access protected
     */
    protected $created_field = 'created_ts';

    /**
     * Field name to use to the modified time column in the DB table.
     *
     * @var string
     * @access protected
     */
    protected $modified_field = 'modified_ts';

    /**
     * Whether or not to auto-fill a 'created_on' field on inserts.
     *
     * @var boolean
     * @access protected
     */
    protected $set_created = FALSE;

    /**
     * Whether or not to auto-fill a 'modified_on' field on updates.
     *
     * @var boolean
     * @access protected
     */
    protected $set_modified = FALSE;

    /*
      Var: $log_user
      If TRUE, will log user id for 'created_by', 'modified_by' and 'deleted_by'.

      Access:
      Protected
     */
    protected $log_user = FALSE;

    /*
      Var: $created_by_field
      Field name to use to the created by column in the DB table.

      Access:
      Protected
     */
    protected $created_by_field = 'created_by';

    /*
      Var: $modified_by_field
      Field name to use to the modified by column in the DB table.

      Access:
      Protected
     */
    protected $modified_by_field = 'modified_by';

    /**
     * The type of date/time field used for created_on and modified_on fields.
     * Valid types are: 'int', 'datetime', 'date'
     *
     * @var string
     * @access protected
     */
    protected $date_format = 'datetime';

    /**
     * Stores any selects here for use by the find* functions.
     *
     * @var string
     * @access protected
     */
    protected $selects = '';

    /*
      Var: $escape
      If FALSE, the select() method will not try to protect your field or table names with backticks.
      This is useful if you need a compound select statement.

      Access:
      Protected
     */
    protected $escape = TRUE;

    //---------------------------------------------------------------

    /**
     * Setup the DB connection if it doesn't exist
     *
     */
    public function __construct() {
        parent::__construct();

        // If we're loading the model, then we probably need the
        // database, so make sure it's loaded.
        if (!isset($this->db)) {
            $this->load->database();
        }
    }

//end __construct()

    function insert($data) {
        if ($this->_function_check(FALSE, $data) === FALSE) {
            return FALSE;
        }
        $data = $this->_filter_data($this->table, $data);

        // Add the created field
        if ($this->set_created === TRUE && !array_key_exists($this->created_field, $data)) {
            $data[$this->field_prefix . $this->created_field] = $this->set_date();
        }

        if ($this->set_created === TRUE && $this->log_user === TRUE && !array_key_exists($this->created_by_field, $data)) {
            $data[$this->field_prefix . $this->created_by_field] = $this->ion_auth->get_user_id();
        }

        // Insert it
        $status = $this->db->insert($this->table, $data);

        if ($status != FALSE) {
            return $this->db->insert_id();
        } else {
            $this->error = mysql_error();
            return FALSE;
        }
    }

    //---------------------------------------------------------------

    /**
     * Inserts a batch of data into the database.
     *
     * @param array $data an array of key/value pairs to insert.
     *
     * @return bool|mixed Either the $id of the row inserted, or FALSE on failure.
     */
    public function insert_batch($data = NULL) {
        if ($this->_function_check(FALSE, $data) === FALSE) {
            return FALSE;
        }

        $set = array();

        // Add the created field
        if ($this->set_created === TRUE) {
            $set[$this->field_prefix . $this->created_field] = $this->set_date();
        }

        if ($this->set_created === TRUE && $this->log_user === TRUE) {
            $set[$this->field_prefix . $this->created_by_field] = $this->ion_auth->get_user_id();
        }

        if (!empty($set)) {
            foreach ($data as $key => $record) {
                $data[$key] = array_merge($set, $data[$key]);
            }
        }


        // Insert it
        $status = $this->db->insert_batch($this->table, $data);

        if ($status === FALSE) {
            $this->error = mysql_error();
            return FALSE;
        }

        return TRUE;
    }

//end insert_batch()

    function update($id, $data, $where = NULL) {
        if ($this->_function_check($id, $data) == FALSE) {
            return FALSE;
        }
        $data = $this->_filter_data($this->table, $data);

        // Add the modified field
        if ($this->set_modified === TRUE && !array_key_exists($this->modified_field, $data)) {
            $data[$this->field_prefix . $this->modified_field] = $this->set_date();
        }

        if ($this->set_modified === TRUE && $this->log_user === TRUE && !array_key_exists($this->modified_by_field, $data)) {
            $data[$this->field_prefix . $this->modified_by_field] = $this->ion_auth->get_user_id();
        }

        $this->db->where($this->field_prefix . $this->key, $id);
        if ($this->db->update($this->table, $data)) {
            return TRUE;
        }

        return FALSE;
    }

    //---------------------------------------------------------------

    /**
     * Updates a batch of existing rows in the database.
     *
     * @param array  $data  An array of key/value pairs to update.
     * @param string $index A string value of the db column to use as the where key
     *
     * @return bool TRUE/FALSE
     */
    public function update_batch($data = NULL, $index = NULL) {
        if (is_null($index)) {
            return FALSE;
        }

        if (!is_null($data)) {
            // Add the modified field
            if ($this->set_modified === TRUE && !array_key_exists($this->modified_field, $data)) {
                foreach ($data as $key => $record) {
                    $data[$key][$this->field_prefix . $this->modified_field] = $this->set_date();
                    if ($this->log_user === TRUE && !array_key_exists($this->modified_by_field, $data[$key])) {
                        $data[$key][$this->field_prefix . $this->modified_by_field] = $this->ion_auth->get_user_id();
                    }
                }
            }

            $result = $this->db->update_batch($this->table, $data, $index);
            if (empty($result)) {
                return TRUE;
            }
        }

        return FALSE;
    }

//end update_batch()
    //--------------------------------------------------------------------

    /**
     * Performs a delete on the record specified. If $this->soft_deletes is TRUE,
     * it will attempt to set a field 'deleted' on the current record
     * to '1', to allow the data to remain in the database.
     *
     * @param mixed $id The primary_key value to match against.
     *
     * @return bool TRUE/FALSE
     */
    public function delete($id = NULL) {
        if ($this->_function_check($id) === FALSE) {
            return FALSE;
        }

        if ($this->get($id) !== FALSE) {
            $result = $this->db->delete($this->table, array($this->field_prefix . $this->key => $id));

            if ($result) {
                return TRUE;
            }

            $this->error = $this->lang->line('fc_model_db_error') . mysql_error();
        } else {
            $this->error = $this->lang->line('fc_model_db_error') . $this->lang->line('fc_model_invalid_id');
        }

        return FALSE;
    }

//end delete()
    //---------------------------------------------------------------

    /**
     * Performs a delete using any field/value pair(s) as the 'where'
     * portion of your delete statement. If $this->soft_deletes is
     * TRUE, it will attempt to set a field 'deleted' on the current
     * record to '1', to allow the data to remain in the database.
     *
     * @param array $data key/value pairs accepts an associative array or a string
     *
     * @example 1) array( 'key' => 'value', 'key2' => 'value2' )
     * @example 2) ' (`key` = "value" AND `key2` = "value2") '
     *
     * @return bool TRUE/FALSE
     */
    public function delete_where($data = NULL) {
        if (empty($data)) {
            $this->error = $this->lang->line('fc_model_no_data');
            $this->logit('[' . get_class($this) . ': ' . __METHOD__ . '] ' . $this->lang->line('fc_model_no_data'));
            return FALSE;
        }

        if (is_array($data)) {
            foreach ($data as $field => $value) {
                $this->db->where($field, $value);
            }
        } else {
            $this->db->where($data);
        }
        $this->db->delete($this->table);

        $result = $this->db->affected_rows();

        if ($result) {
            return $result;
        }

        $this->error = $this->lang->line('fc_model_db_error') . mysql_error();

        return FALSE;
    }

//end delete_where()
    //---------------------------------------------------------------

    /**
     * Searches for a single row in the database.
     *
     * @param string $id The primary key of the record to search for.
     * @param int $return_type Choose the type of return type. 0 - Object, 1 - Array
     *
     * @return mixed An object/array representing the db row, or FALSE.
     */
    public function get($id = '', $return_type = 0) {
        if ($this->_function_check($id) === FALSE) {
            return FALSE;
        }

        $this->set_selects();

        $query = $this->db->get_where($this->table, array($this->table . '.' . $this->field_prefix . $this->key => $id));

        if ($query->num_rows()) {
            if ($return_type == 0) {
                return $query->row();
            } else {
                return $query->row_array();
            }
        }

        return FALSE;
    }

//end get()
    //---------------------------------------------------------------

    /**
     * Returns all records in the table.
     *
     * By default, there is no 'where' clause, but you can filter
     * the results that are returned by using either CodeIgniter's
     * Active Record functions before calling this function, or
     * through method chaining with the where() method of this class.
     *
     * @param int $return_type Choose the type of return type. 0 - Object, 1 - Array
     *
     * @return mixed An array of objects/arrays representing the results, or FALSE on failure or empty set.
     */
    public function get_all($return_type = 0) {
        if ($this->_function_check() === FALSE) {
            return FALSE;
        }

        $this->set_selects();

        $this->db->from($this->table);

        $query = $this->db->get();

        if (!empty($query) && $query->num_rows() > 0) {
            if ($return_type == 0) {
                return $query->result();
            } else {
                return $query->result_array();
            }
        }

        $this->error = $this->lang->line('fc_model_bad_select');
        $this->logit('[' . get_class($this) . ': ' . __METHOD__ . '] ' . $this->lang->line('fc_model_bad_select'));
        return FALSE;
    }

//end get_all()
    //---------------------------------------------------------------

    /**
     * A convenience method that combines a where() and get_all() call into a single call.
     *
     * @param mixed  $field The table field to search in.
     * @param mixed  $value The value that field should be.
     * @param string $type  The type of where clause to create. Either 'and' or 'or'.
     * @param int $return_type Choose the type of return type. 0 - Object, 1 - Array
     *
     * @return bool|mixed An array of objects representing the results, or FALSE on failure or empty set.
     */
    public function find_all_by($field = NULL, $value = NULL, $type = 'and', $return_type = 0) {
        if (empty($field))
            return FALSE;

        // Setup our field/value check
        if (is_array($field)) {
            foreach ($field as $key => $value) {
                if ($type == 'or') {
                    $this->db->or_where($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        } else {
            $this->db->where($field, $value);
        }

        $this->set_selects();

        return $this->get_all($return_type);
    }

//end find_all_by()
    //--------------------------------------------------------------------

    /**
     * Returns the first result that matches the field/values passed.
     *
     * @param string $field Either a string or an array of fields to match against. If an array is passed it, the $value parameter is ignored since the array is expected to have key/value pairs in it.
     * @param string $value The value to match on the $field. Only used when $field is a string.
     * @param string $type  The type of where clause to create. Either 'and' or 'or'.
     * @param int $return_type Choose the type of return type. 0 - Object, 1 - Array
     *
     * @return bool|mixed An object representing the first result returned.
     */
    public function find_by($field = '', $value = '', $type = 'and', $return_type = 0) {
        if (empty($field) || (!is_array($field) && empty($value))) {
            $this->error = $this->lang->line('fc_model_find_error');
            $this->logit('[' . get_class($this) . ': ' . __METHOD__ . '] ' . $this->lang->line('fc_model_find_error'));
            return FALSE;
        }

        if (is_array($field)) {
            foreach ($field as $key => $value) {
                if ($type == 'or') {
                    $this->db->or_where($key, $value);
                } else {
                    $this->db->where($key, $value);
                }
            }
        } else {
            $this->db->where($field, $value);
        }

        $this->set_selects();

        $query = $this->db->get($this->table);

        if ($query && $query->num_rows() > 0) {
            if ($return_type == 0) {
                return $query->row();
            } else {
                return $query->row_result();
            }
        }

        return FALSE;
    }

//end find_by()

    /**
     * A utility function to allow child models to use the type of
     * date/time format that they prefer. This is primarily used for
     * setting created_on and modified_on values, but can be used by
     * inheriting classes.
     *
     * The available time formats are:
     * * 'int'		- Stores the date as an integer timestamp.
     * * 'datetime'	- Stores the date and time in the SQL datetime format.
     * * 'date'		- Stores teh date (only) in the SQL date format.
     *
     * @param mixed $user_date An optional PHP timestamp to be converted.
     *
     * @access protected
     *
     * @return int|null|string The current/user time converted to the proper format.
     */
    protected function set_date($user_date = NULL) {
        $curr_date = !empty($user_date) ? $user_date : time();

        switch ($this->date_format) {
            case 'int':
                return $curr_date;
                break;
            case 'datetime':
                return date('Y-m-d H:i:s', $curr_date);
                break;
            case 'date':
                return date('Y-m-d', $curr_date);
                break;
        }
    }

//end set_date()
    //--------------------------------------------------------------------

    /**
     * Allows you to set the table to use for all methods during runtime.
     *
     * @param string $table The table name to use (do not include the prefix!)
     *
     * @return void
     */
    public function set_table($table = '') {
        $this->table = $table;
    }

//end set_table()
    //--------------------------------------------------------------------

    /**
     * Sets the date_format to use for setting created_on and modified_on values.
     *
     * @param string $format String describing format. Valid values are: 'int', 'datetime', 'date'
     *
     * @return bool
     */
    public function set_date_format($format = 'int') {
        if ($format != 'int' && $format != 'datetime' && $format != 'date') {
            return FALSE;
        }

        $this->date_format = $format;

        return TRUE;
    }

//end set_date_format()
    //--------------------------------------------------------------------

    /**
     * Sets whether to auto-create modified_on dates in the update method.
     *
     * @param bool $modified
     *
     * @return bool
     */
    public function set_modified($modified = TRUE) {
        if ($modified !== TRUE && $modified !== FALSE) {
            return FALSE;
        }

        $this->set_modified = $modified;

        return TRUE;
    }

//end set_modified
    //--------------------------------------------------------------------
    // !CHAINABLE UTILITY METHODS
    //--------------------------------------------------------------------

    /**
     * Sets the where portion of the query in a chainable format.
     *
     * @param mixed  $field The field to search the db on. Can be either a string with the field name to search, or an associative array of key/value pairs.
     * @param string $value The value to match the field against. If $field is an array, this value is ignored.
     *
     * @return FC_Model An instance of this class.
     */
    public function where($field = NULL, $value = NULL, $escape = TRUE) {
        if (!empty($field)) {
            if (is_string($field)) {
                $this->db->where($field, $value, $escape);
            } else if (is_array($field)) {
                $this->db->where($field);
            }
        }

        return $this;
    }

//end where()
    //--------------------------------------------------------------------

    /**
     * Sets the select portion of the query in a chainable format. The value
     * is stored for use in the find* methods so that child classes can
     * have more flexibility in joins and what is selected.
     *
     * @param string $selects A string representing the selection.
     * @param string $escape  A string representing the escape.
     *
     * @return MY_Model An instance of this class.
     */
    public function select($selects = NULL, $escape = NULL) {
        if (!empty($selects)) {
            $this->selects = $selects;
        }
        if ($escape === FALSE) {
            $this->escape = $escape;
        }

        return $this;
    }

//end select()
    //--------------------------------------------------------------------

    /**
     * Sets the limit portion of the query in a chainable format.
     *
     * @param int $limit  An int showing the max results to return.
     * @param int $offset An in showing how far into the results to start returning info.
     *
     * @return FC_Model An instance of this class.
     */
    public function limit($limit = 0, $offset = 0) {
        $this->db->limit($limit, $offset);

        return $this;
    }

//end limit()
    //--------------------------------------------------------------------

    /**
     * Generates the JOIN portion of the query.
     *
     * @param string $table A string containing the table name.
     * @param string $cond  A string with the join condiction.
     * @param string $type  A string containing the type of join - INNER, OUTER etc.
     *
     * @return FC_Model An instance of this class.
     */
    public function join($table, $cond, $type = '') {
        $this->db->join($table, $cond, $type);

        return $this;
    }

//end join()
    //--------------------------------------------------------------------

    /**
     * Inserts a chainable order_by method from either a string or an
     * array of field/order combinations. If the $field value is an array,
     * it should look like:
     *
     * array(
     *     'field1' => 'asc',
     *     'field2' => 'desc'
     * );
     *
     * @param string $field The field to order the results by.
     * @param string $order Which direction to order the results ('asc' or 'desc')
     *
     * @return FC_Model An instance of this class.
     */
    public function order_by($field = NULL, $order = 'asc') {
        if (!empty($field)) {
            if (is_string($field)) {
                $this->db->order_by($field, $order);
            } else if (is_array($field)) {
                foreach ($field as $f => $o) {
                    $this->db->order_by($f, $o);
                }
            }
        }

        return $this;
    }

//end order_by()
    //--------------------------------------------------------------------
    //---------------------------------------------------------------
    // !UTILITY FUNCTIONS
    //---------------------------------------------------------------

    /**
     * A utility method that does some error checking and cleanup for other methods:
     *
     * * Makes sure that a table has been set at $this->table.
     * * If passed in, will make sure that $id is of the valid type.
     * * If passed in, will verify the $data is not empty.
     *
     * @param mixed      $id   The primary_key value to match against.
     * @param array|bool $data Array of data
     *
     * @access protected
     *
     * @return bool
     */
    protected function _function_check($id = FALSE, &$data = FALSE) {

        // Does the model have a table set?
        if (empty($this->table)) {
            $this->error = $this->lang->line('fc_model_no_table');
            $this->logit('[' . get_class($this) . ': ' . __METHOD__ . '] ' . $this->lang->line('fc_model_no_table'), 'error');
            return FALSE;
        }

        // Check the ID, but only if it's a non-FALSE value
        if ($id !== FALSE) {
            if (empty($id) || $id == 0) {
                $this->error = $this->lang->line('fc_model_invalid_id');
                $this->logit('[' . get_class($this) . ': ' . __METHOD__ . '] ' . $this->lang->line('fc_model_invalid_id'));
                return FALSE;
            }
        }

        // Check the data
        if ($data !== FALSE) {
            if (!is_array($data) || count($data) == 0) {
                $this->error = $this->lang->line('fc_model_no_data');
                $this->logit('[' . get_class($this) . ': ' . __METHOD__ . '] ' . $this->lang->line('fc_model_no_data'));
                return FALSE;
            }
        }

        return TRUE;
    }

//end _function_check

    public function _filter_data($table, $data) {
        $filtered_data = array();
        $columns = $this->db->list_fields($table);

        if (is_array($data)) {
            foreach ($columns as $column) {
                if (array_key_exists($column, $data))
                    $filtered_data[$column] = $data[$column];
            }
        }

        return $filtered_data;
    }

    //--------------------------------------------------------------------

    /**
     * Takes the string in $this->selects, if not empty, and sets it
     * with the ActiveRecord db class. If $this->escape is FALSE it
     * will not try to protect your field or table names with backticks.
     *
     * Clears the string afterword to make sure it's clean for the next call.
     *
     * @access protected
     */
    protected function set_selects() {
        if (!empty($this->selects) && $this->escape === FALSE) {
            $this->db->select($this->selects, FALSE);

            // Clear it out for the next process.
            $this->selects = NULL;
            $this->escape = NULL;
        } elseif (!empty($this->selects)) {
            $this->db->select($this->selects);

            // Clear it out for the next process.
            $this->selects = NULL;
        }
    }

//end set_selects()
    //--------------------------------------------------------------------

    /**
     * Logs an error to the Console (if loaded) and to the log files.
     *
     * @param string $message The string to write to the logs.
     * @param string $level   The log level, as per CI log_message method.
     *
     * @access protected
     *
     * @return mixed
     */
    protected function logit($message = '', $level = 'debug') {
        if (empty($message)) {
            return FALSE;
        }

        if (class_exists('Console')) {
            Console::log($message);
        }

        log_message($level, $message);
    }

//end logit()

    public function found_rows() {
        $query = $this->db->query('SELECT FOUND_ROWS() AS found_rows');

        return $query->row()->found_rows;
    }

    public function list_fields() {
        return $this->db->list_fields($this->table);
    }

    //--------------------------------------------------------------------
}
