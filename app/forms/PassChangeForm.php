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
use Nette\DI\Container;
use Nette\Application\Responses\JsonResponse;
use Nette\Environment;
use Nette\Application\ForbiddenRequestException;

/**
 * Auth form
 *
 * Class AuthForm
 * @author Jan Buriánek <burianek.jen@gmail.com>
 * @package Nette\Application\UI
 */
class PassChangeForm extends AbstractForm
{
	/**
	 * @param \Nette\DI\Container $context
	 * @param null                $parent
	 * @param null                $name
	 */
	function __construct(Container $context, $parent = NULL, $name = NULL)
	{
		parent::__construct($context, 'passChangeForm.latte', $parent, $name);

		$this->addPassword('password1', '')
			->setRequired('Both fields are required')
			->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'New password');

		$this->addPassword('password2', 'Password')
			->setRequired('Both fields are required')
			->setAttribute('class', 'form-control')
			->setAttribute('placeholder', 'Once again, please');

		$this->addSubmit('submit', 'Update my password')
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
	public function formValidation (Form $form)
	{
		$values = $form->getValues();

		\Logger::getRootLogger()->debug('User ' . $this->getPresenter()->getUser()->getIdentity()->getData()['realName'] . ' tries to change password');

		if (!$this->getPresenter()->getUser()->isLoggedIn())
		{
			$form->addError('You have been automatically disconnected after '.
				$this->context->config->session_time
				.' seconds. This happened due to keeping your user account secure. Please, <a href="'.
				Environment::getHttpRequest()->getUrl()->getBasePath().
				'">authenticate</a> yourself again and be faster :-)');
			return;
		}

		$password1 = trim($values->password1);
		$password2 = trim($values->password2);

		if ($password1 !== $password2)
		{
			$form->addError('Passwords do not match');
			\Logger::getRootLogger()->info('User ' . $this->getPresenter()->getUser()->getIdentity()->getData()['realName'] . ': Passwords do not match');
		}

		$minLength = $this->context->config->min_passwd_length;

		if (strlen($password1) < $minLength)
		{
			\Logger::getRootLogger()->info('User ' . $this->getPresenter()->getUser()->getIdentity()->getData()['realName'] . ': Password is too short; minimal required length of password is '.$minLength.' characters');
			$form->addError('Password is too short; minimal required length of password is '.$minLength.' characters');
		}
	}

	/**
	 *
	 *
	 * @param Form $form
	 */
	public function formSucceeded (Form $form)
	{
		if ($this->getPresenter()->isAjax())
		{
			$name = $this->getPresenter()->getUser()->getIdentity()->getData()['realName'];

			// Lets change the password
			$values = $form->getValues();

			try {
				$this->getPresenter()->getUser()->getAuthenticator()->updatePassword(
					$this->getPresenter()->getUser()->getId(),
					$this->getPresenter()->getUser()->getIdentity()->getData()['password'],
					$values->password1
				);
			} catch (ForbiddenRequestException $e)
			{
				$form->addError($e->getMessage());
				$this->formFailed($form);
			}

			$this->getPresenter()->getUser()->logout();

			\Logger::getRootLogger()->info('Password of user ' . $this->getPresenter()->getUser()->getIdentity()->getData()['realName'] . ' was successfully upadted');

			$this->getPresenter()->sendResponse(new JsonResponse(array(
				'success' => true,
				'realname' => $name
			)));
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
			\Logger::getRootLogger()->info('User ' . $this->getPresenter()->getUser()->getIdentity()->getData()['realName'] . ' failed to change its password');

			\Logger::getRootLogger()->debug('Sending "failed response" to client');
			$this->getPresenter()->sendResponse(new JsonResponse(array(
				"success" => false,
				"errors" => $form->getErrors()
			)));
		}
	}
}