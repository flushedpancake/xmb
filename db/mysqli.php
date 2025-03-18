<?php

/**
 * eXtreme Message Board
 * XMB 1.10.00-alpha
 *
 * Developed And Maintained By The XMB Group
 * Copyright (c) 2001-2025, The XMB Group
 * https://www.xmbforum2.com/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace XMB;

use Exception;
use mysqli;
use mysqli_sql_exception;
use RuntimeException;

/**
 * Represents a single MySQL connection and provides abstracted query methods.
 */
class MySQLiDatabase implements DBStuff
{
    public const SQL_NUM = MYSQLI_NUM;
    public const SQL_BOTH = MYSQLI_BOTH;
    public const SQL_ASSOC = MYSQLI_ASSOC;

    private string     $db         = '';   // Name of the database used by this connection.
    private float      $duration   = 0.0;  // Cumulative time used by synchronous query commands.
    private int|string $last_id    = 0;    // The ID generated by INSERT or UPDATE commands, also known as LAST_INSERT_ID.  Stored for DEBUG mode only.
    private int        $last_rows  = 0;    // Number of rows affected by the last INSERT, UPDATE, REPLACE or DELETE query.  Stored for DEBUG mode only.
    private mysqli     $link;              // Connection object.
    private int        $querynum   = 0;    // Count of commands sent on this connection.
    private array      $querylist  = [];   // Log of all SQL commands sent.  Stored for DEBUG mode only.
    private array      $querytimes = [];   // Log of all SQL execution times.  Stored for DEBUG mode only.
    private float      $timer      = 0.0;  // Date/time the last query started.  Class scope not needed, just simplifies code.
    private string     $test_error = '';   // Any error message collected by testConnect().

    public function __construct(private bool $debug, private bool $logErrors)
    {
        // Force older versions of PHP to behave like PHP v8.1.  This assumes there are no incompatible mysqli scripts running.
        if ($this->isInstalled()) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        }
    }

    /**
     * Checks PHP dependencies
     *
     * @since 1.9.12.06 Formerly named "dbstuff::installed"
     * @since 1.10.00
     * @return bool
     */
    public function isInstalled(): bool
    {
        return extension_loaded('mysqli');
    }

    /**
     * Establishes a connection to the MySQL server.
     *
     * @since 1.5.0
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpw
     * @param string $dbname
     * @param bool   $pconnect Keep the connection open after the script ends.
     * @param bool   $force_db Generate a fatal error if the $dbname database doesn't exist on the server.
     * @return bool  Whether or not the database was found after connecting.
     */
    public function connect(
        string $dbhost,
        string $dbuser,
        #[\SensitiveParameter]
        string $dbpw,
        string $dbname,
        bool $pconnect = false,
        bool $force_db = false,
    ): bool {
        // Verify compatiblity.
        if (! $this->isInstalled()) {
            header('HTTP/1.0 500 Internal Server Error');
            echo 'Error: The PHP mysqli extension is missing.';
            throw new RuntimeException('The PHP mysqli extension is missing.');
        }

        if ($pconnect) {
            $dbhost = "p:$dbhost";
        }

        if ($force_db) {
            $database = $dbname;
        } else {
            $database = '';
        }

        try {
            $this->link = new mysqli($dbhost, $dbuser, $dbpw, $database);
        } catch (mysqli_sql_exception $e) {
            $msg = "<h3>Database connection error!</h3>\n"
                 . "A connection to the Database could not be established.<br />\n"
                 . "Please check the MySQL username, password, database name and host.<br />\n"
                 . "Make sure <i>config.php</i> is correctly configured.<br />\n";
            $sql = '';
            $this->panic($e, $sql, $msg);
        }

        // Always force single byte mode so the PHP mysql client doesn't throw non-UTF input errors.
        try {
            $result = $this->link->set_charset('latin1');
        } catch (mysqli_sql_exception $e) {
            $msg = "<h3>Database connection error!</h3>\n"
                 . "The database connection could not be configured for XMB.<br />\n"
                 . "Please ensure the mysqli_set_charset function is working.<br />\n";
            $sql = '';
            $this->panic($e, $sql, $msg);
        }

        if ($force_db) {
            $this->db = $dbname;
            return true;
        } else {
            return $this->select_db($dbname, $force_db);
        }
    }

    /**
     * Attempts a connection and does not generate error messages.
     *
     * @since 1.9.12.06 Formerly named "dbstuff::test_connect"
     * @since 1.10.00
     * @param string $dbhost
     * @param string $dbuser
     * @param string $dbpw
     * @param string $dbname
     * @return bool  Whether or not the connection was made and the database was found.
     */
    public function testConnect(
        string $dbhost,
        string $dbuser,
        #[\SensitiveParameter]
        string $dbpw,
        string $dbname
    ): bool {
        if (! $this->isInstalled()) return false;
        try {
            $this->link = new mysqli($dbhost, $dbuser, $dbpw, $dbname);
            $this->link->set_charset('latin1');
            $this->select_db($dbname, force: 'test');
        } catch (mysqli_sql_exception $e) {
            $this->test_error = $e->getMessage();
            return false;
        }
        
        $this->test_error = '';
        return true;
    }
    
    /**
     * Gets any error message that was encountered during the last call to testConnect().
     *
     * Error messages are likely to contain sensitive file path info.
     * This method is intended for use by Super Administrators and install/upgrade scripts only.
     *
     * @since 1.9.12.06 Formerly named "dbstuff::get_test_error"
     * @since 1.10.00
     * @return string Error message or empty string.
     */
    public function getTestError(): string
    {
        return $this->test_error;
    }

    /**
     * Closes a connection that is no longer needed.
     *
     * @since 1.9.12.06
     */
    public function close()
    {
        $this->link->close();
    }

    /**
     * Sets the name of the database to be used on this connection.
     *
     * @since 1.9.1
     * @param string $database The full name of the MySQL database.
     * @param string $force Optional. Specifies error mode. Dies if 'yes'.
     * @return bool TRUE on success.
     */
    public function select_db(string $database, string $force = 'yes'): bool
    {
        try {
            $this->link->select_db($database);
            $this->db = $database;
            return true;
        } catch (mysqli_sql_exception $e) {
            switch ($force) {
                case 'yes':
                    $this->panic($e);
                case 'test':
                    throw $e;
                case 'no':
                    return false;
            }
        }
    }

    /**
     * Searches for an accessible database containing the XMB settings table.
     *
     * @since 1.9.1
     * @param string $tablepre The settings table name prefix.
     * @return bool
     */
    public function find_database(string $tablepre): bool
    {
        $dbs = $this->query('SHOW DATABASES');
        while($db = $this->fetch_array($dbs)) {
            if ('information_schema' == $db['Database']) {
                continue;
            }
            $q = $this->query("SHOW TABLES FROM `{$db['Database']}`");

            while ($table = $this->fetch_array($q)) {
                if ($tablepre.'settings' == $table[0]) {
                    if ($this->select_db($db['Database'], false)) {
                        $this->free_result($dbs);
                        $this->free_result($q);
                        return true;
                    }
                }
            }
            $this->free_result($q);
        }
        $this->free_result($dbs);
        return false;
    }

    /**
     * Fetch the last error message.
     *
     * @since 1.9.1
     */
    public function error(): string
    {
        return $this->link->error;
    }

    /**
     * Frees memory used by a result set that is no longer needed.
     *
     * @since 1.9.1
     * @param mysqli_result $result
     */
    public function free_result($result)
    {
        try {
            $result->free();
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
        return true;
    }
	
    /**
     * Fetch an array representing the next row of a result.
     *
     * The array type can be associative, numeric, or both.
     *
     * @since 1.5.0
     * @param mysqli_result $result
     * @param int $type The type of indexing to add to the array: SQL_ASSOC, SQL_NUM, or SQL_BOTH
     * @return array|null Returns an array representing the fetched row, or null if there are no more rows.
     */
    public function fetch_array($result, int $type = self::SQL_ASSOC): ?array
    {
        try {
            return $result->fetch_array($type);
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Fetch an array representing all rows of a result.
     *
     * The 2nd-level array type used within the list of rows can be associative, numeric, or both.
     *
     * @since 1.10.00
     * @param mysqli_result $result
     * @param int $type The type of indexing to add to the array: SQL_ASSOC, SQL_NUM, or SQL_BOTH
     * @return array Returns an array representing a list of row arrays.
     */
    public function fetch_all($result, int $type = self::SQL_ASSOC): array
    {
        try {
            return $result->fetch_all($type);
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Get the name of the specified field from a result set.
     *
     * @since 1.9.1
     */
    public function field_name($result, int $field): string
    {
        try {
            return $result->fetch_field_direct($field)->name;
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Returns the length of a field as specified in the database schema.
     *
     * @since 1.9.11.13
     * @param mysqli_result $result The result of a query.
     * @param int $field The field_offset starts at 0.
     * @return int
     */
    public function field_len($result, int $field): int
    {
        try {
            return $result->fetch_field_direct($field)->length;
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Handle all MySQLi errors.
     *
     * @since 1.9.8 SP3
     * @param Exception $e
     * @param string $sql Optional.  The full SQL command that caused the error, if any.
     * @param string $msg Optional.  HTML help message to display before the error.
     */
    private function panic(Exception $e, string $sql = '', string $msg = '')
    {
        if (!headers_sent()) {
            header('HTTP/1.0 500 Internal Server Error');
        }
        
        echo $msg;

        if ($this->debug || $this->logErrors) {
            $error = $e->getMessage();
            $errno = $e->getCode();
        }
        
        if ($this->logErrors) {
            $log_advice = "Please check the error log for details.<br />\n";
        } else {
            $log_advice = "Please set LOG_MYSQL_ERRORS to true in config.php.<br />\n";
        }

    	if ($this->debug && (!defined('X_SADMIN') || X_SADMIN)) {
            require_once(XMB_ROOT.'include/validate.inc.php');

            // MySQL error text may contain sensitive file path info.
            if (defined('X_SADMIN')) {
                echo 'MySQL encountered the following error: ' . cdataOut($error) . "<br />\n(errno = $errno)<br /><br />\n";
                if ($sql != '') {
                    echo "In the following query:<br />\n<pre>" . cdataOut($sql) . "</pre>\n";
                }
                echo "<strong>Stack trace:</strong>\n<pre>";
                debug_print_backtrace();
                echo '</pre>';
            } elseif ($sql != '') {
                echo "MySQL encountered an error in the following query:<br />\n<pre>" . cdataOut($sql) . "</pre>\n", $log_advice;
            } elseif ($msg != '') {
                echo $log_advice;
            } else {
                echo "MySQL encountered an error.<br />\n", $log_advice;
            }
        } else {
            echo "The system has failed to process your request.<br />\n", $log_advice;
            if (defined('X_SADMIN') && X_SADMIN && ! $this->debug) {
                echo "To display details, please set DEBUG to true in config.php.<br />\n";
            }
    	}
        if ($this->logErrors) {
            $log = "MySQL encountered the following error:\n$error\n(errno = $errno)\n";
            if ($sql != '') {
                if ((1153 == $errno || 2006 == $errno) && strlen($sql) > 16000) {
                    $log .= "In the following query (log truncated):\n" . substr($sql, 0, 16000) . "\n";
                } else {
                    $log .= "In the following query:\n$sql\n";
                }
            }

            $trace = $e->getTrace();
            $depth = 1; // Go back before dbstuff::panic() and see who called dbstuff::query().
            $filename = $trace[$depth]['file'];
            $linenum = $trace[$depth]['line'];
            $function = $trace[$depth]['function'];
            $log .= "\$db->{$function}() was called by {$filename} on line {$linenum}";

            if (!ini_get('log_errors')) {
                ini_set('log_errors', true);
                ini_set('error_log', 'error_log');
            }
            error_log($log);
        }
        exit;
    }

    /**
     * Can be used to make any expression query-safe.
     *
     * This method produces output that is safe to use in a MySQL quoted string.
     * It is not reversible via stripslashes due to C-style escape sequences.
     *
     * Example:
     *  $sqlinput = $db->escape($rawinput);
     *  $db->query("UPDATE a SET b = 'Hello, my name is $sqlinput'");
     *
     * @since 1.9.8 SP3
     * @param string $rawstring
     * @return string
     */
    public function escape(string $rawstring): string
    {
        try {
            return $this->link->real_escape_string($rawstring);
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Preferred for performance when escaping any string variable.
     *
     * Note this only works when the raw value can be discarded.
     *
     * Example:
     *  $db->escape_fast($rawinput);
     *  $db->query("UPDATE a SET b = 'Hello, my name is $rawinput'");
     *
     * @since 1.9.11.12
     * @param string $sql Read/Write Variable
     */
    public function escape_fast(string &$sql)
    {
        try {
            $sql = $this->link->real_escape_string($sql);
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Escape a string used with the LIKE operator.
     *
     * Any required wildcards must be added separately (must not be escaped by this method).
     *
     * This method produces output that is safe to use in a MySQL quoted string.
     * It is not reversible via stripslashes due to C-style escape sequences on top of limited inner slashing.
     * This mix is necessary because the real_escape_string method produces C-style sequences, but the LIKE operator itself does not understand them.
     *
     * @since 1.9.10
     * @param string $rawstring
     * @return string
     */
    public function like_escape(string $rawstring): string
    {
        try {
            return $this->link->real_escape_string(str_replace(array('\\', '%', '_'), array('\\\\', '\\%', '\\_'), $rawstring));
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Escape a string used with the REGEXP operator.
     *
     * @since 1.9.10
     */
    public function regexp_escape(string $rawstring): string
    {
        try {
            return $this->link->real_escape_string(preg_quote($rawstring));
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Executes a MySQL Query
     *
     * @since 1.5.0
     * @param string $sql Unique MySQL query (multiple queries are not supported). The query string should not end with a semicolon.
     * @param bool $panic XMB will die and use dbstuff::panic() in case of any MySQL error unless this param is set to FALSE.
     * @return mixed Returns a MySQL resource or a bool, depending on the query type and error status.
     */
    public function query(string $sql, bool $panic = true)
    {
        $this->start_timer();
        try {
            $query = $this->link->query($sql);
        } catch (mysqli_sql_exception $e) {
            if ($panic) {
                $this->panic($e, $sql);
            } else {
                $query = false;
            }
        }
        $this->querytimes[] = $this->stop_timer();
        $this->querynum++;
    	if ($this->debug) {
            if ($this->logErrors) {
                $this->last_id = $this->link->insert_id;
                $this->last_rows = $this->link->affected_rows;
                $warnings = $this->link->warning_count;

                if ($warnings > 0) {
                    if (!ini_get('log_errors')) {
                        ini_set('log_errors', TRUE);
                        ini_set('error_log', 'error_log');
                    }
                    if (strlen($sql) > 16000) {
                        $output = "MySQL generated $warnings warnings in the following query (log truncated):\n" . substr($sql, 0, 16000) . "\n";
                    } else {
                        $output = "MySQL generated $warnings warnings in the following query:\n$sql\n";
                    }
                    $query3 = $this->link->query('SHOW WARNINGS');
                    while ($row = $this->fetch_array($query3)) {
                        $output .= var_export($row, TRUE)."\n";
                    }
                    error_log($output);
                    $this->free_result($query3);
                }
            }
            if (!defined('X_SADMIN') || X_SADMIN) {
                $this->querylist[] = $sql;
            }
        }
        return $query;
    }

    /**
     * Sends a MySQL query without fetching the result rows.
     *
     * You cannot use mysqli_num_rows() and mysqli_data_seek() on a result set
     * returned from mysqli_use_result(). You also have to call
     * mysqli_free_result() before you can send a new query to MySQL.
     *
     * @since 1.9.1
     * @param string $sql Unique MySQL query (multiple queries are not supported). The query string should not end with a semicolon.
     * @param bool $panic XMB will die and use dbstuff::panic() in case of any MySQL error unless this param is set to FALSE.
     * @return mixed Returns a MySQL resource or a bool, depending on the query type and error status.
     */
    public function unbuffered_query(string $sql, $panic = true)
    {
        $this->start_timer();
        try {
            $query = $this->link->query($sql, MYSQLI_USE_RESULT);
        } catch (mysqli_sql_exception $e) {
            if ($panic) {
                $this->panic($e, $sql);
            } else {
                $query = false;
            }
        }
        $this->querynum++;
    	if ($this->debug && (!defined('X_SADMIN') || X_SADMIN)) {
            $this->querylist[] = $sql;
        }
        $this->querytimes[] = $this->stop_timer();
        return $query;
    }

    /**
     * Fetch the list of tables in a database.
     *
     * @since 1.9.1
     */
    public function fetch_tables(?string $dbname = null): array
    {
        if ($dbname === null) {
            $dbname = $this->db;
        }
        $this->select_db($dbname);

        $array = array();
        $q = $this->query("SHOW TABLES");
        while($table = $this->fetch_row($q)) {
            $array[] = $table[0];
        }
        $this->free_result($q);
        return $array;
    }

    /**
     * Retrieves the contents of one cell from a MySQL result set.
     *
     * @since 1.5.0
     * @param mysqli_result $result
     * @param int $row Optional. The zero-based row number from the result that's being retrieved.
     * @param int $field Optional. The zero-based offset of the field being retrieved.
     * @return ?string
     */
    public function result($result, int $row = 0, int $field = 0): ?string
    {
        try {
            $result->data_seek($row);
            return $result->fetch_column($field);
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Retrieves the row count from a query result.
     *
     * @since 1.5.0
     * @param mysqli_result $result
     */
    public function num_rows($result): int
    {
        $count = $result->num_rows;

        if (!is_int($count)) throw new UnexpectedValueException('Row count was not an int');

        return $count;
    }

    /**
     * Retrieves the column count from a query result.
     *
     * @since 1.9.1
     * @param mysqli_result $result
     */
    public function num_fields($result): int
    {
        return $result->field_count;
    }

    /**
     * Retrieves the ID of the last auto increment record or insert ID.
     *
     * @since 1.5.0
     * @return int|string
     */
    public function insert_id(): int|string
    {
        return $this->link->insert_id;
    }

    /**
     * Fetch an enumerated array representing the next row of a result.
     *
     * @since 1.5.0
     * @param mysqli_result $result
     * @return ?array Enumerated array of values, or null for end of result.
     */
    public function fetch_row($result): ?array
    {
        try {
            return $result->fetch_row();
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Adjusts the result pointer in a specific row in the result set.
     *
     * @since 1.9.11
     */
    public function data_seek($result, int $row): bool
    {
        try {
            return $result->data_seek($row);
        } catch (mysqli_sql_exception $e) {
            $this->panic($e);
        }
    }

    /**
     * Gets the number of rows affected by the previous query.
     *
     * @since 1.9.11
     */
    public function affected_rows(): int
    {
        $count = $this->link->affected_rows;

        if (!is_int($count)) throw new UnexpectedValueException('Row count was not an int');

        return $count;
    }

    /**
     * @since 1.9.1
     */
    private function start_timer()
    {
        $mtime = explode(" ", microtime());
        $this->timer = (float) $mtime[1] + (float) $mtime[0];
        return true;
    }

    /**
     * Calculate time since start_timer and add it to duration.
     *
     * @since 1.9.1
     * @return int Time since start_timer.
     */
    private function stop_timer()
    {
        $mtime = explode(" ", microtime());
        $endtime = (float) $mtime[1] + (float) $mtime[0];
        $taken = ($endtime - $this->timer);
        $this->duration += $taken;
        $this->timer = 0;
        return $taken;
    }

    /**
     * Retrieve the MySQL server version number.
     *
     * @since 1.9.11.11
     * @return string
     */
    public function server_version(): string
    {
        return $this->link->server_info;
    }

    /**
     * Retrieve the cumulative query time on this object.
     *
     * @since 1.9.12
     * @return float
     */
    public function getDuration(): float
    {
        return $this->duration;
    }

    /**
     * Retrieve the cumulative query count on this object.
     *
     * @since 1.9.12
     * @return int
     */
    public function getQueryCount(): int
    {
        return $this->querynum;
    }

    /**
     * Retrieve the list of queries sent by this object.
     *
     * @since 1.9.12
     * @return array
     */
    public function getQueryList(): array
    {
        return $this->querylist;
    }

    /**
     * Retrieve the list of times used by each query sent by this object.
     *
     * @since 1.9.12
     * @return array
     */
    public function getQueryTimes(): array
    {
        return $this->querytimes;
    }
}
