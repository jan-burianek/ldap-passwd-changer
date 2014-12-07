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
use Nette\Templating\FileTemplate;
use Nette\DI\Container;
use Nette\Environment;
use Nette\Latte\Engine;

abstract class AbstractForm extends Form {

	/**
	 * @var \Nette\DI\Container
	 */
	protected $context;

	/**
	 * Where to find the template file
	 *
	 * @var
	 */
	private $templateFilename;

	function __construct(Container $context, $templateFilename, IContainer $parent = NULL, $name = NULL)
	{
		$this->context = $context;
		$this->templateFilename = $templateFilename;

		$this->setMethod('post');
	}

	/**
	 * Renders form.
	 *
	 * @return void
	 */
	public function render ()
	{
		$template = new FileTemplate();
		$template->setFile(__DIR__ . '/../templates/forms/' . $this->templateFilename);
		$template->onPrepareFilters[] = function ($template) {
			$template->registerFilter(new Engine);
		};
		$template->basePath = Environment::getHttpRequest()->getUrl()->getBasePath();
		$template->form = $this;
		$template->render();
	}
} 