(function( $ ) {
	'use strict';

	document.addEventListener("DOMContentLoaded", function() {

		const categoryOrderForm = $('.ba-category-order-form'),
			productOrderForm = $('.ba-product-order-form');

		if ( categoryOrderForm.length > 0 ) {
			categoryOrderPage();
		}

		if ( productOrderForm.length > 0 ) {
			productOrderPage();
		}

		function categoryOrderPage() {

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

		}

		function productOrderPage() {

			// Create Sortable List
			const list = document.querySelector('.ba-admin-sortable');

			if ( list ) {
				let sortable = new Sortable(list, {
					group: 'nested',
					onSort: function (/**Event*/evt) {
						updateProductOrder();
					},
				});
			}

			$('.ba-product-order-form #cat').change(function() {

				let catID = $(this).val(),
					url = '/wp-json/wp/v2/product/?per_page=100&product_cat=' + catID;

				$('.ba-admin-sortable li').remove();
				$('.ba-admin-sortable-wrap svg').show();
				$('.ba-product-order-form button[type="submit"]').hide();
				$('.ba-admin-error').hide();

				let postData = {
					category_id: catID,
				};

				$.post(ajax_params.ajax_url, {
					action: 'get_products_by_category',
					data: JSON.stringify(postData)
				}, function(data, status){
				  	
					// Success
					let products = JSON.parse(data);

					$('.ba-admin-sortable-wrap svg').hide();

					for (var i = 0; i < products.length; i++) {

						let li = '<li data-id="' + products[i].ID + '">' + products[i].post_title + '</li>';

						$('.ba-admin-sortable').append(li);

					}

					updateProductOrder();

					$('.ba-product-order-form button[type="submit"]').show();

				}).fail(function(response) {

					// Failure
					console.log(response);
					$('.ba-admin-sortable-wrap svg').hide();
					$('.ba-admin-error').show();

				});

			});

			function updateProductOrder() {

				let ids = [];

				$('.ba-admin-sortable li').each(function() {

					let id = $(this).attr('data-id');

					ids.push( id );

				});

				$('#productIDs').val( JSON.stringify(ids) );

			}


		}

	});


})( jQuery );
