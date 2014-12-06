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

/**
 * Interactive form containing
 * both forms with all funkcionality
 *
 * Class PasswdInteractiveForm
 * @package Nette\Application\UI
 */
class PasswdInteractiveForm extends Control
{
	/**
	 * @var Nette\DI\Container
	 */
	protected $context;

	/**
	 * @param Nette\DI\Container $context
	 * @param IContainer $parent
	 * @param null $name
	 */
	public function __construct(Nette\DI\Container $context, IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($parent, $name);

		$this->context = $context;
	}

	/**
	 *
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
		$form = new Nette\Application\UI\AuthForm();

		$form->onSuccess[] = $this->AuthFormSucceeded;
		$form->onError[] = $this->AuthFormError;

		return $form;
	}

	/**
	 * @param $form
	 */
	public function AuthFormSucceeded($form) {}

	/**
	 *
	 *
	 * @param $form
	 */
	public function AuthFormError ($form) {}
}