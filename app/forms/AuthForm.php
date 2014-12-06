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

namespace Nette\Application\UI;

use Nette\Application\UI,
	Nette\ComponentModel\IContainer;
use Nette\Templating\FileTemplate;

/**
 * Auth form
 *
 * Class AuthForm
 * @package Nette\Application\UI
 */
class AuthForm extends Form
{
	/**
	 * @param IContainer $parent
	 * @param null $name
	 */
	function __construct(IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->setMethod('post');
		$this->setMethod('post');

		$this->addText('username', 'LDAP Username')
#			->setOption('description', '')
			->setRequired()
			->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'LDAP Username');

		$this->addPassword('password', 'Password')
#			->setOption('description', '')
			->setRequired()
			->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'Password');

		$this->addSubmit('submit', 'Sign in')
			->setAttribute('class', 'btn btn-lg btn-primary btn-block');
	}

	/**
	 * Renders the form
	 */
	public function render ()
	{
		$template = new FileTemplate();
		$template->setFile(__DIR__ . '/../templates/forms/authForm.latte');
		$template->onPrepareFilters[] = function ($template) {
			$template->registerFilter(new \Nette\Latte\Engine);
		};
		$template->basePath = \Nette\Environment::getHttpRequest()->getUrl()->getBasePath();
		$template->component = $this;
		$template->render();
	}
}
