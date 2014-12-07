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

use Nette;
use Nette\DI\Container;
use Nette\Security\AuthenticationException;
use Nette\Security\Identity;
use Nette\Application\ForbiddenRequestException;

/**
 *
 *
 * Class LDAPUserManager
 * @package App\Model
 */
class LDAPUserManager extends Nette\Object implements Nette\Security\IAuthenticator {

	/**
	 * @var LDAP connection
	 */
	private $connection;

	private $context;

	public function __construct(Container $context)
	{
		$this->context = $context;
	}

	/**
	 * Performs an authentication.
	 *
	 * @param array $credentials
	 *
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		@list($username, $password) = $credentials;

		$c = $this->context->config;

		$this->establishConnection();

		$searchResult = ldap_search(
			$this->connection,
			$c->dn,
			'('.$c->username.'='.$username.')',
			array($c->real_name, $c->mail)
		);

		$records = ldap_get_entries($this->connection, $searchResult);

		if ($records["count"] != 1 || @ldap_bind($this->connection, $records[0]['dn'], $password) == false)
		{
			throw new AuthenticationException('Wrong username or password', self::IDENTITY_NOT_FOUND);
		}

		return new Identity($username, null, array(
			'realName' => $records[0]["cn"][0],
			'password' => $password
		));
	}

	/**
	 * Updates password in LDAP
	 *
	 * @param $u     Username (uid or mail)
	 * @param $op    Old password (current)
	 * @param $np    New password (future)
	 *
	 * @throws ForbiddenRequestException
	 */
	public function updatePassword ($u, $op, $np)
	{
		$this->establishConnection();
		$c = $this->context->config;

		$searchResult = ldap_search(
			$this->connection,
			$c->dn,
			'('.$c->username.'='.$u.')',
			array($c->real_name, $c->mail)
		);

		$records = ldap_get_entries($this->connection, $searchResult);
		@ldap_bind($this->connection, $records[0]["dn"], $op);

		$entry = array();
		$entry[$c->password] =
			'{SHA}' . base64_encode(
				pack( 'H*', sha1( $np ) )
			);

		if (ldap_modify($this->connection,$records[0]["dn"],$entry) == false){
			throw new ForbiddenRequestException("Your password cannot be change, please contact the administrator.");
		}
	}

	/**
	 *
	 */
	private function establishConnection ()
	{
		$this->connection = ldap_connect($this->context->config->host);
		ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
	}
} 