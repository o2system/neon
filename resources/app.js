/**
 * This file is part of the O2System Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

import Espresso from "o2system-espresso";
import "./app.scss";

(function() {
	window.addEventListener("load", function() {
		// Fetch all the forms we want to apply custom Bootstrap validation styles to
		const forms = document.getElementsByClassName("needs-validation");
		// Loop over them and prevent submission
		Array.prototype.filter.call(forms, function(form) {
			form.addEventListener("submit", function(event) {
				if (form.checkValidity() === false) {
					event.preventDefault();
					event.stopPropagation();
				}
				form.classList.add("was-validated");
			}, false);
		});
	}, false);
})();