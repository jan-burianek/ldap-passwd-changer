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

namespace App\Presenters;

use Nette;
use Nette\Application\UI\PasswdInteractiveForm;

/**
 * Homepage presenter.
 * The only one presenter this app has.
 *
 * @author Jan Buriánek <burianek.jen@gmail.com>
 * @package App\Presenters
 */
class HomepagePresenter extends Nette\Application\UI\Presenter
{

	/**
	 * Logs access to this app
	 */
	function __construct()
	{
		\Logger::getLogger('access')->info('Access report');
	}

	public function renderDefault() {}

	/**
	 * Vytvoří komponentu bublinek
	 *
	 * @return Nette\Application\UI\Bubbles
	 */
	public function createComponentPasswdInteractiveForm ()
	{
		return new PasswdInteractiveForm($this);
	}
}
