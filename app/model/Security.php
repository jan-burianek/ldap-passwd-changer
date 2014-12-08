<?php
/**
 * ldap-passwd-changer
 * Simple set of PHP scripts enabling LDAP users to change their passwords
 *
 * Copyright (C) 2015 Jan Buriánek <burianek.jen@gmail.com>
 *
 * Tento program je svobodný software: můžete jej šířit
 * a upravovat podle ustanovení Obecné veřejné licence GNU (GNU General Public Licence),
 * vydávané Free Software Foundation a to buď podle 3. verze této Licence,
 * nebo (podle vašeho uvážení) kterékoli pozdější verze.
 *
 * Tento program je rozšiřován v naději, že bude užitečný,
 * avšak BEZ JAKÉKOLIV ZÁRUKY. Neposkytují se ani odvozené záruky
 * PRODEJNOSTI anebo VHODNOSTI PRO URČITÝ ÚČEL. Další podrobnosti
 * hledejte v Obecné veřejné licenci GNU.
 *
 * Kopii Obecné veřejné licence GNU jste měli obdržet spolu s tímto programem.
 * Pokud se tak nestalo, najdete ji zde: <http://www.gnu.org/licenses/>.
 */

namespace App\Model;

use Nette\DI\Container;
use \Nette\Object;
use \Nette\Database\Context;

/**
 * Database layer for security time tracking
 *
 * Enables to track users accesses
 * as well as apply some rules on it
 *
 * Class Security
 * @author Jan Buriánek <burianek.jen@gmail.com>
 * @package App\Model
 */
class Security extends Object {

	/**
	 * @var \Nette\DI\Container;
	 */
	protected $context;

	/**
	 * @var \Nette\Database\Context;
	 */
	protected $db;

	/**
	 * @param Container $context
	 */
	function __construct(Container $context)
	{
		$this->context = $context;
		$this->db = new Context($context->database);
	}

	/**
	 * Checks whether $uid is on banned list
	 * or not and returns the result.
	 *
	 * @param string $uid
	 *
	 * @return bool
	 */
	public function isBanned ($uid)
	{
		$time = time() - $this->context->config->safe_time;

		$data = $this->db
			->table('banned')
			->where('from_time > ? AND uid = ?', $time, $uid)
			->fetchPairs('banned_id', 'from_time');

		return count($data) > 0;
	}

	/**
	 * Logs $uid's attempt to authenticate.
	 * When attempt is successful, this record
	 * is deleted from log table.
	 *
	 * @param $uid
	 */
	public function log ($uid)
	{
		$this->db->query(
			'INSERT INTO access',
			array(
				'uid' => $uid,
				'last_attempt' => time()
			)
		);
	}

	/**
	 * If user exceed number of allowed attempts
	 * defined in {allowed_attempts} and time
	 * {safe_time} has not passed yet, then
	 * lets throw him to banned list
	 *
	 * @param $uid
	 */
	public function checkAmountOfAttempts ($uid)
	{
		$time = time() - $this->context->config->safe_time;
		$data = $this->db
			->table('access')
			->where('last_attempt > ? AND uid = ?', $time, $uid)
			->limit($this->context->config->allowed_attempts)
			->fetchPairs('access_id', 'last_attempt');

		if (count($data) == $this->context->config->allowed_attempts)
		{
			# User exceeded amount of allowed login attempts
			$this->db->query(
				'INSERT INTO banned',
				array(
					'uid' => $uid,
					'from_time' => time()
				)
			);

			$this->remove($uid, array('access'));
		}
	}

	/**
	 * Removes rows with $uid from
	 * all tables, because user $uid
	 * has successfully logged in,
	 * so keeping his credentials is no
	 * longer needed
	 *
	 * @param       $uid
	 * @param array $tables
	 */
	public function remove ($uid, $tables = array('access','banned'))
	{
		foreach ($tables as $table)
		{
			$this->db
				->table($table)
				->where('uid = ?', $uid)
				->delete();
		}
	}

} 