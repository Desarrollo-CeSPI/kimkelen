<?php
/*
 *  $Id: Mock.php 1080 2007-02-10 18:17:08Z romanb $
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.phpdoctrine.org>.
 */

/**
 * IBM DB2 Adapter. This class was ported from the Zend Framework
 *
 * @package     Doctrine
 * @subpackage  Adapter
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link        www.phpdoctrine.org
 * @since       1.0
 * @version     $Revision: 1080 $
 */
class Doctrine_Adapter_Db2 extends Doctrine_Adapter
{
    /**
     * User-provided configuration.
     *
     * Basic keys are:
     *
     * username   => (string)  Connect to the database as this username.
     * password   => (string)  Password associated with the username.
     * host       => (string)  What host to connect to (default 127.0.0.1)
     * dbname     => (string)  The name of the database to user
     * protocol   => (string)  Protocol to use, defaults to "TCPIP"
     * port       => (integer) Port number to use for TCP/IP if protocol is "TCPIP"
     * persistent => (boolean) Set TRUE to use a persistent connection (db2_pconnect)
     *
     * @var array
     */
    protected $_config = array(
        'dbname'       => null,
        'username'     => null,
        'password'     => null,
        'host'         => 'localhost',
        'port'         => '50000',
        'protocol'     => 'TCPIP',
        'persistent'   => false
    );

    /**
     * Stores the execution mode
     *
     * Possible values DB2_AUTOCOMMIT_ON or DB2_AUTOCOMMIT_OFF
     *
     * @var int execution flag (DB2_AUTOCOMMIT_ON or DB2_AUTOCOMMIT_OFF)
     */
    protected $_executeMode = DB2_AUTOCOMMIT_ON;

    /**
     * Table name of the last accessed table for an insert operation
     * This is a DB2-Adapter-specific member variable with the utmost
     * probability you might not find it in other adapters...
     *
     * @var string
     */
    protected $_lastInsertTable = null;

     /**
     * $config is an array of key/value pairs containing configuration
     * options.  These options are common to most adapters:
     *
     * dbname         => (string) The name of the database to user
     * username       => (string) Connect to the database as this username.
     * password       => (string) Password associated with the username.
     * host           => (string) What host to connect to, defaults to localhost
     * port           => (string) The port of the database, defaults to 50000
     * persistent     => (boolean) Whether to use a persistent connection or not, defaults to false
     * protocol       => (string) The network protocol, defaults to TCPIP
     * options        => (array)  Other database options such as autocommit, case, and cursor options
     *
     * @param array $config An array of configuration keys.
     */
    public function __construct(array $config)
    {
        if ( ! isset($config['password'])) {
            throw new Doctrine_Adapter_Exception("Configuration array must have a key for 'password' for login credentials.");
        }

        if ( ! isset($config['username'])) {
            throw new Doctrine_Adapter_Exception("Configuration array must have a key for 'username' for login credentials.");
        }

        if ( ! isset($config['dbname'])) {
            throw new Doctrine_Adapter_Exception("Configuration array must have a key for 'dbname' that names the database instance.");
        }

        // keep the config
        $this->_config = array_merge($this->_config, (array) $config);
    }

    /**
     * Creates the connection resource
     *
     * @return void
     */
    protected function _connect()
    {
        if (is_resource($this->_connection)) {
            // connection already exists
            return;
        }

        if ( ! extension_loaded('ibm_db2')) {
            throw new Doctrine_Adapter_Exception('The IBM DB2 extension is required for this adapter but not loaded');
        }

        if ($this->_config['persistent']) {
            // use persistent connection
            $conn_func_name = 'db2_pconnect';
        } else {
            // use "normal" connection
            $conn_func_name = 'db2_connect';
        }

        if ( ! isset($this->_config['options'])) {
            // config options were not set, so set it to an empty array
            $this->_config['options'] = array();
        }

        if ( ! isset($this->_config['options']['autocommit'])) {
            // set execution mode
            $this->_config['options']['autocommit'] = &$this->_executeMode;
        }

        if ($this->_config['host'] !== 'localhost') {
            // if the host isn't localhost, use extended connection params
            $dbname = 'DRIVER={IBM DB2 ODBC DRIVER}' .
                     ';DATABASE=' . $this->_config['dbname'] .
                     ';HOSTNAME=' . $this->_config['host'] .
                     ';PORT='     . $this->_config['port'] .
                     ';PROTOCOL=' . $this->_config['protocol'] .
                     ';UID='      . $this->_config['username'] .
                     ';PWD='      . $this->_config['password'] .';';
            $this->_connection = $conn_func_name(
                $dbname,
                null,
                null,
                $this->_config['options']
            );
        } else {
            // host is localhost, so use standard connection params
            $this->_connection = $conn_func_name(
                $this->_config['dbname'],
                $this->_config['username'],
                $this->_config['password'],
                $this->_config['options']
            );
        }

        // check the connection
        if ( ! $this->_connection) {
            throw new Doctrine_Adapter_Exception(db2_conn_errormsg(), db2_conn_error());
        }
    }

    /**
     * Close the connection resource
     *
     * @return void
     */
    public function closeConnection()
    {
        db2_close($this->_connection);
        $this->_connection = null;
    }

    /**
     * Prepare a sql statement and return it
     *
     * @param   string $sql The SQL statement with placeholders.
     * @return  Doctrine_Statement_Db2
     */
    public function prepare($sql)
    {
        $this->_connect();
        $stmt = new Doctrine_Statement_Db2($this, $sql);
        $stmt->setFetchMode($this->_fetchMode);
        return $stmt;
    }

    /**
     * Get the current execution mode
     *
     * @return int the execution mode (DB2_AUTOCOMMIT_ON or DB2_AUTOCOMMIT_OFF)
     */
    public function _getExecuteMode()
    {
        return $this->_executeMode;
    }

    /**
     * Set the current execution mode
     *
     * @param   integer $mode
     * @return  void
     */
    public function _setExecuteMode($mode)
    {
        switch ($mode) {
            case DB2_AUTOCOMMIT_OFF:
            case DB2_AUTOCOMMIT_ON:
                $this->_executeMode = $mode;
                db2_autocommit($this->_connection, $mode);
                break;
            default:
                throw new Doctrine_Adapter_Exception("execution mode not supported");
                break;
        }
    }

    /**
     * Quote a raw string.
     *
     * @param   string $value Raw string
     * @return  string Quoted string
     */
    protected function _quote($value)
    {
        /**
         * Some releases of the IBM DB2 extension appear
         * to be missing the db2_escape_string() method.
         * The method was added in ibm_db2.c revision 1.53
         * according to cvs.php.net.  But the function is
         * not present in my build of PHP 5.2.1.
         */
        if (function_exists('db2_escape_string')) {
            return db2_escape_string($value);
        }
        return parent::_quote($value);
    }

    /**
     * Get the symbol used for identifier quoting
     *
     * @return string
     */
    public function getQuoteIdentifierSymbol()
    {
        $info = db2_server_info($this->_connection);
        $identQuote = $info->IDENTIFIER_QUOTE_CHAR;
        return $identQuote;
    }

    /**
     * Begin a transaction.
     *
     * @return void
     */
    protected function _beginTransaction()
    {
        $this->_setExecuteMode(DB2_AUTOCOMMIT_OFF);
    }

    /**
     * Commit a transaction.
     *
     * @return void
     */
    protected function _commit()
    {
        if ( ! db2_commit($this->_connection)) {
            throw new Doctrine_Adapter_Exception(
                db2_conn_errormsg($this->_connection),
                db2_conn_error($this->_connection));
        }

        $this->_setExecuteMode(DB2_AUTOCOMMIT_ON);
    }

    /**
     * Rollback a transaction.
     *
     * @return void
     */
    protected function _rollBack()
    {
        if ( ! db2_rollback($this->_connection)) {
            throw new Doctrine_Adapter_Exception(
                db2_conn_errormsg($this->_connection),
                db2_conn_error($this->_connection));
        }
        $this->_setExecuteMode(DB2_AUTOCOMMIT_ON);
    }

    /**
     * Set the fetch mode.
     *
     * @param  integer $mode
     * @return void
     */
    public function setFetchMode($mode)
    {
        switch ($mode) {
            case Doctrine::FETCH_NUM:   // seq array
            case Doctrine::FETCH_ASSOC: // assoc array
            case Doctrine::FETCH_BOTH:  // seq+assoc array
            case Doctrine::FETCH_OBJ:   // object
                $this->_fetchMode = $mode;
                break;
            default:
                throw new Doctrine_Adapter_Exception('Invalid fetch mode specified');
                break;
        }
    }
}