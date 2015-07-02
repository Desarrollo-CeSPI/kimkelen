<?php
/*
 *  $Id: DebugPDOStatement.php 1084 2008-11-06 15:23:49Z hans $
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
 * and is licensed under the LGPL. For more information please see
 * <http://propel.phpdb.org>.
 */

/**
 * PDOStatement that provides some enhanced functionality needed by Propel.
 *
 * Simply adds the ability to count the number of queries executed and log the queries.
 *
 * @author     Oliver Schonrock <oliver@realtsp.com>
 * @since      2007-07-12
 * @package    propel.util
 */
class DebugPDOStatement extends PDOStatement {

	/**
	 * the pdo connection from which we were created
	 * @var        DebugPDO
	 */
	protected $pdo;

	protected $typeMap = array(	PDO::PARAM_BOOL => "PDO::PARAM_BOOL",
								PDO::PARAM_INT => "PDO::PARAM_INT",
								PDO::PARAM_STR => "PDO::PARAM_STR",
								PDO::PARAM_LOB => "PDO::PARAM_LOB",
								PDO::PARAM_NULL => "PDO::PARAM_NULL",
								);

	/**
	 * Construct a new statement class with reference to main DebugPDO object.
	 */
	protected function __construct(DebugPDO $pdo)
	{
		$this->pdo = $pdo;
	}

	/**
	 * Overridden for query counting.
	 * @return     int
	 */
	public function execute($input_parameters = null)
	{
		$this->pdo->incrementQueryCount();
		return parent::execute($input_parameters);
	}

	/**
	 * Binds value to PDOStatement.
	 *
	 * @param      int $pos
	 * @param      mixed $value
	 * @param      int $type
	 * @return     boolean
	 */
	public function bindValue($pos, $value, $type = PDO::PARAM_STR)
	{
		$typestr = isset($this->typeMap[$type]) ? $this->typeMap[$type] : '(default)';
		if ($type == PDO::PARAM_LOB) {
		#	$this->pdo->log("Binding [LOB value] at position ".$pos." w/ PDO  type " . $typestr);
			$this->pdo->log("Binding " . print_r($value, true) . " at position $pos w/ PDO type " . $typestr);
		} else {
#			$this->pdo->log("Binding " . var_export($value, true) . " at position $pos w/ PDO type " . $typestr);
                        $this->pdo->log("Binding " . print_r($value, true) . " at position $pos w/ PDO type " . $typestr);

		}
		return parent::bindValue($pos, $value, $type);
	}
}
