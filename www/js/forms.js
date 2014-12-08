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

$(document).ready(function () {
	$( '.pass-change-form').addClass('transform-hide-immediately');
	$( '#end-message').addClass('transform-hide-immediately');

	$( '.auth-form' ).ajaxForm ({
		beforeSend: function() {
			$('.auth-form').find('input').prop( 'disabled', true );
			$('.auth-form').find('input[type="submit"]').hide();
			$('.auth-form .loader').show();
		},
		complete: function(xhr) {

			var data = JSON.parse(xhr.responseText);

			if (data.success)
			{

				$('.pass-change-form .real-name').html(data.realname);
				$('.pass-change-form .user-name').html(data.username);

				$('.auth-form .img-loader').hide();
				$('.auth-form .img-OK').show();

				$( 'form.auth-form').addClass('transform-hide');

				setTimeout( function () {
					$( '.auth-form').remove();
					$( '.pass-change-form')
						.show()
						.addClass('transform-show');
				}, 1000);
			}
			else
			{
				$('.auth-form input').prop( 'disabled', false );
				$('.auth-form .loader').hide();
				$('.auth-form input[type="submit"]').show();
				$('.auth-form input[name="password"]').val('').focus();

				$('.auth-form .alert')
					.removeClass('alert-info')
					.addClass('alert-warning')
					.html(makeList(data.errors) + '<p>I am sorry... Try it again.</p>');
			}
		}
	});

	$( '.pass-change-form' ).ajaxForm ({
		beforeSend: function() {
			$('.pass-change-form').find('input').prop( 'disabled', true );
			$('.pass-change-form').find('input[type="submit"]').hide();
			$('.pass-change-form .loader').show();
		},
		complete: function(xhr) {

			var data = JSON.parse(xhr.responseText);

			if (data.success)
			{
				$('.pass-change-form .img-loader').hide();
				$('.pass-change-form .img-OK').show();

				$( '.pass-change-form')
					.removeClass('transform-hide-immediately')
					.removeClass('transform-show')
					.addClass('transform-hide');

				setTimeout( function () {
					$( '.pass-change-form').remove();

					$( '#end-message')
						.show()
						.addClass('transform-show');

				}, 1000);
			}
			else
			{
				$( '.pass-change-form .alert')
					.removeClass('alert-info')
					.addClass('alert-warning')
					.html(makeList(data.errors) + '<p>I am sorry... Try it again.</p>');

				$('.pass-change-form input').prop( 'disabled', false );
				$('.pass-change-form .loader').hide();
				$('.pass-change-form input[type="submit"]').show();

				$('input[type="password"]').val('');
				$('input[name="password1"]').focus();
			}
		}
	});

	/**
	 * Transforms JS array into
	 * string HTML list
	 *
	 * @param arr
	 * @returns {string}
	 */
	var makeList = function ( arr ) {
		var a = '<ul>',
			b = '</ul>',
			m = '';

		for (var i = 0; i < arr.length; i += 1)
		{
			m += '<li>' + arr[i] + '</li>';
		}

		return a + m + b;
	}
});