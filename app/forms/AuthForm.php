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

use Nette\Application\UI;
use	Nette\ComponentModel\IContainer;
use Nette\DI\Container;
use Nette\Application\Responses\JsonResponse;
use Nette\Security\AuthenticationException;

/**
 * Auth form
 *
 * Class AuthForm
 * @package Nette\Application\UI
 */
class AuthForm extends AbstractForm
{

	/**
	 * @param Container  $context
	 * @param IContainer $parent
	 * @param null       $name
	 */
	function __construct(Container $context, IContainer $parent = NULL, $name = NULL)
	{
		parent::__construct($context, 'authForm.latte', $parent, $name);

		$this->addText('username', 'Username')
			->setRequired('Both fields are required')
			->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'Username');

		$this->addPassword('password', 'Password')
			->setRequired('Both fields are required')
			->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'Password');

		$this->addSubmit('submit', 'Authenticate')
			->setAttribute('class', 'btn btn-lg btn-primary btn-block');

		$this->onSuccess[] = array($this, 'formSucceeded');
		$this->onValidate[] = array($this, 'formValidation');
		$this->onError[] = array($this, 'formFailed');
	}

	/**
	 *
	 *
	 * @param Form $form
	 */
	public function formSucceeded (Form $form)
	{
		$p = $this->getPresenter();

		if ($p->isAjax())
		{
			$p->sendResponse(new JsonResponse(array(
				'success' => true,
				'username' => $p->getUser()->getID(),
				'realname' => $p->getUser()->getIdentity()->getData()['realName']
			)));
		}
	}

	/**
	 *
	 *
	 * @param Form $form
	 */
	public function formValidation (Form $form)
	{
		$values = $form->getValues();

		//TODO kontrola kolik pokusů za poslední čas

		try {
			$this->getPresenter()->getUser()->login(
				$values->username,
				$values->password
			);
		} catch (AuthenticationException $e)
		{
			$form->addError($e->getMessage());
		}

		$this->getPresenter()->getUser()->setExpiration(
			$this->context->config->session_time
		);
	}

	/**
	 *
	 *
	 * @param Form $form
	 */
	public function formFailed (Form $form)
	{
		if ($this->getPresenter()->isAjax())
		{
			$this->getPresenter()->sendResponse(new JsonResponse(array(
				"success" => false,
				"errors" => $form->getErrors()
			)));
		}
	}
}
