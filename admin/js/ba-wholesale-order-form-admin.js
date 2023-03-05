(function( $ ) {
	'use strict';

	document.addEventListener("DOMContentLoaded", function() { 

		// Create Sortable List
		const list = document.querySelector('.ba-admin-sortable');

		if ( list ) {
			let sortable = new Sortable(list, {
				group: 'nested',
				onSort: function (/**Event*/evt) {
					updateCheckedCatsInput();
				},
			});
		}

		// Add checked categories to obj and checkbox select
		let checkedCats = {};

		$('.ba-admin-checkbox-wrap input[type="checkbox"]').change(function() {

			checkedCats = {};

			// Add each checked checkbox to obj
			$('.ba-admin-checkbox-wrap input[type="checkbox"]').each(function() {
				if ( $(this).is(':checked') ) {
					checkedCats[$(this).val()] = $(this).attr('id');
				}
			});

			// Add checked obj items to sortable list
			for (const key in checkedCats) {
				const id = `${checkedCats[key]}`,
						name = `${key}`;

				if ( !$('.ba-admin-sortable li[data-id="' + id + '"]').length > 0 ) {
					$('.ba-admin-sortable').append('<li data-id="' + id + '">' + name + '</li>');
				}

			}

			removeUncheckedCats();
			updateCheckedCatsInput();

		});

		function updateCheckedCatsInput() {

			let inputObj = {};

			$('.ba-admin-sortable li').each(function() {

				inputObj[$(this).text()] = $(this).attr('data-id');

			});

			$('#orderedCats').val( JSON.stringify( inputObj ) );

		}

		function removeUncheckedCats() {

			$('.ba-admin-sortable li').each(function() {

				let id = $(this).attr('data-id');

				if ( !Object.values(checkedCats).includes(id) ) {
					$(this).remove();
				}

			});

		}

	});


})( jQuery );
