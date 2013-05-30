<?php
/*
 *  $Id: Firebird.php 5801 2009-06-02 17:30:27Z piccoloprincipe $
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
 * @package     Doctrine
 * @subpackage  Import
 * @license     http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @author      Konsta Vesterinen <kvesteri@cc.hut.fi>
 * @author      Lorenzo Alberton <l.alberton@quipo.it> (PEAR MDB2 Interbase driver)
 * @author      Lukas Smith <smith@pooteeweet.org> (PEAR MDB2 library)
 * @version     $Revision: 5801 $
 * @link        www.phpdoctrine.org
 * @since       1.0
 */
class Doctrine_Import_Firebird extends Doctrine_Import
{
    /**
     * list all tables in the current database
     *
     * @return array        data array
     */
    public function listTables($database = null)
    {
        $query = 'SELECT RDB$RELATION_NAME FROM RDB$RELATIONS WHERE RDB$SYSTEM_FLAG=0 AND RDB$VIEW_BLR IS NULL';

        return $this->conn->fetchColumn($query);
    }

    /**
     * list all fields in a tables in the current database
     *
     * @param string $table name of table that should be used in method
     * @return mixed data array on success, a MDB2 error on failure
     * @access public
     */
    public function listTableColumns($table)
    {
        $table = $this->conn->quote(strtoupper($table), 'text');
        $query = 'SELECT RDB$FIELD_NAME FROM RDB$RELATION_FIELDS WHERE UPPER(RDB$RELATION_NAME) = ' . $table;

        return $this->conn->fetchColumn($query);
    }

    /**
     * list all users
     *
     * @return array            data array containing all database users
     */
    public function listUsers()
    {
        return $this->conn->fetchColumn('SELECT DISTINCT RDB$USER FROM RDB$USER_PRIVILEGES');
    }

    /**
     * list the views in the database
     *
     * @return array            data array containing all database views
     */
    public function listViews($database = null)
    {
        return $this->conn->fetchColumn('SELECT DISTINCT RDB$VIEW_NAME FROM RDB$VIEW_RELATIONS');
    }

    /**
     * list the views in the database that reference a given table
     *
     * @param string $table     table for which all references views should be found
     * @return array            data array containing all views for given table
     */
    public function listTableViews($table)
    {
        $query  = 'SELECT DISTINCT RDB$VIEW_NAME FROM RDB$VIEW_RELATIONS';
        $table  = $this->conn->quote(strtoupper($table), 'text');
        $query .= ' WHERE UPPER(RDB$RELATION_NAME) = ' . $table;

        return $this->conn->fetchColumn($query);
    }

    /**
     * list all functions in the current database
     *
     * @return array              data array containing all availible functions
     */
    public function listFunctions()
    {
        $query = 'SELECT RDB$FUNCTION_NAME FROM RDB$FUNCTIONS WHERE RDB$SYSTEM_FLAG IS NULL';

        return $this->conn->fetchColumn($query);
    }

    /**
     * This function will be called to get all triggers of the
     * current database ($this->conn->getDatabase())
     *
     * @param  string $table      The name of the table from the
     *                            previous database to query against.
     * @return array              data array containing all triggers for given table
     */
    public function listTableTriggers($table)
    {
        $query = 'SELECT RDB$TRIGGER_NAME FROM RDB$TRIGGERS WHERE RDB$SYSTEM_FLAG IS NULL OR RDB$SYSTEM_FLAG = 0';

        if ( ! is_null($table)) {
            $table = $this->conn->quote(strtoupper($table), 'text');
            $query .= ' WHERE UPPER(RDB$RELATION_NAME) = ' . $table;
        }

        return $this->conn->fetchColumn($query);
    }
}