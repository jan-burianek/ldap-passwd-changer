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

namespace App;

use Nette,
	Nette\Application\Routers\RouteList,
	Nette\Application\Routers\Route;
use Nette\DI\Container;


/**
 * Router factory.
 *
 * @author Jan Buriánek <burianek.jen@gmail.com>
 */
class RouterFactory
{

	/**
	 * @param Container $container
	 *
	 * @return \Nette\Application\IRouter
	 */
	public function createRouter(Container $container)
	{
		$router = new RouteList();

		$flags = ($container->config->enable_https)? Route::SECURED : 0;

		$router[] = new Route('<presenter>/<action>[/<id>]', 'Homepage:default', $flags);
		return $router;
	}

}
