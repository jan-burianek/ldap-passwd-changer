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

use App\Model\Security;
use Nette\Application\UI;
use	Nette\ComponentModel\IContainer;
use Nette\DI\Container;
use Nette\Application\Responses\JsonResponse;
use Nette\Security\AuthenticationException;

/**
 * Auth form
 *
 * Class AuthForm
 * @author Jan Buriánek <burianek.jen@gmail.com>
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
			\Logger::getRootLogger()->info('User ' . $form->getValues()->username . ' successfully logged in');

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
		\Logger::getRootLogger()->debug('User ' . $values->username . ' tries to log in');

		$security = new Security($this->context);

		if ($security->isBanned($values->username))
		{
			$form->addError('User ' . $values->username . ' exceeded allowed amount of login attempts. Please wait a minute and try it again.');
			\Logger::getRootLogger()->info('User ' . $values->username . ' id banned, rejecting');
			return;
		}

		$security->log($values->username);
		$security->checkAmountOfAttempts($values->username);

		try {
			$this->getPresenter()->getUser()->login(
				$values->username,
				$values->password
			);

			$this->getPresenter()->getUser()->setExpiration(
				$this->context->config->session_time
			);

			$security->remove($values->username);
		} catch (AuthenticationException $e)
		{
			$form->addError($e->getMessage());
			\Logger::getRootLogger()->info('User ' . $values->username . ': ' . $e->getMessage());
		}


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
			\Logger::getRootLogger()->info('User ' . $form->getValues()->username . ' failed to log in');

			\Logger::getRootLogger()->debug('Sending "failed response" to client');
			$this->getPresenter()->sendResponse(new JsonResponse(array(
				"success" => false,
				"errors" => $form->getErrors()
			)));
		}
	}
}
