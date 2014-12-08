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

use Nette,
	Model;
use Nette\ComponentModel\IContainer;
use Nette\Application\UI\Presenter;

/**
 * Interactive form containing
 * both forms with all funkcionality
 *
 * Class PasswdInteractiveForm
 * @author Jan Buriánek <burianek.jen@gmail.com>
 * @package Nette\Application\UI
 */
class PasswdInteractiveForm extends Control
{
	/**
	 * @var Nette\Application\UI\Presenter
	 */
	protected $presenter;

	/**
	 * @param Nette\Application\UI\Presenter $presenter
	 * @param IContainer $parent
	 * @param null $name
	 */
	public function __construct(Presenter $presenter, IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->presenter = $presenter;
	}

	/**
	 * Renders the component
	 */
	public function render ()
	{
		$template = $this->template;
		$template->setFile(__DIR__ . '/../templates/components/passwdInteractiveForm.latte');
		$template->render();
	}

	/**
	 * @return Form
	 */
	protected function createComponentAuthForm() {
		\Logger::getRootLogger()->debug('Creating Auth Form');
		return new AuthForm($this->getPresenter()->getContext());
	}

	/**
	 * @return Form
	 */
	protected function createComponentPassChangeForm() {
		\Logger::getRootLogger()->debug('Creating Pass Change Form');
		return new PassChangeForm($this->presenter->getContext());
	}
}