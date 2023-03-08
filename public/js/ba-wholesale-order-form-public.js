(function( $ ) {
	'use strict';

	$(function() {

		// Expand Category Section
		$('.ba-category-row-heading').click(function() {

			$(this).parent().toggleClass('active');
			$(this).siblings('.ba-category-row-content').slideToggle();

			const catID = $(this).parent().data('id'),
				  catName = $(this).parent().data('name');
			fetchProducts(catID, catName);

		});

		function fetchProducts(catID, catName) {

			let catRow = $('.ba-category-row[data-id="' + catID + '"]'),
				catRowContent = catRow.find('.ba-category-row-content'),
				loader = catRowContent.find('svg.loader');

			// Don't fetch products if already fetched previously
			if ( catRow.hasClass('fetched') ) {
				return;
			}

			loader.show();

			$.post(ajax_params.ajax_url, {
				action: 'get_products_html_by_category',
				data: JSON.stringify({ category_id: catID, category_name: catName })
			}, function(data, status){
				  
				// Success
				let html = JSON.parse(data).html;

				catRow.addClass('fetched');
				loader.hide();
				catRowContent.append(html);

			}).fail(function(response) {

				// Failure
				loader.hide();
				catRowContent.append('<p class="ba-product-error">Error Fetching products. Please try again or contact us for help.</p>')
				console.log(response);

			});

		}

		$('.ba-product-modal').on('keyup', '.qty-wrap input[type="text"]', function() {

			let totalQuantity = 0,
				totalPrice = 0;

			$('.qty-wrap input[type="text"]').each(function() {

				if ( $(this).val().length != 0 ) {
					let val = parseInt( $(this).val() ),
						variationPrice = $(this).parent().siblings('.price').find('span').text();
					totalQuantity += val;					
					totalPrice += val * variationPrice;
				}

			});

			$('.ba-quantity-total').text(totalQuantity);
			$('.ba-price-total').text(formatter.format(totalPrice));

		});


		$('.ba-categories-container').on('click', '.ba-product-row button', function() {
			
			let parent = $(this).parent(),
				title = parent.find('.product-title').text(),
				id = parent.attr('data-id');

			resetModal();

			if ( parent.hasClass('variable-product') ) {

				let variationData = parent.data('variations');

				populateModal(title, variationData, id);

			} else {

				populateModal(title, null, id);

			}

			$('.ba-product-modal-wrap').show();

		});

		$('.ba-modal-add-to-cart').click(function(e) {

			e.preventDefault();

			$('.ba-product-modal .ba-success, .ba-product-modal .ba-error').hide();

			let variationData = [];

			$('.ba-size-table-row').each(function() {

				let quantity = $(this).find('input[type="text"]').val();

				if ( quantity > 0 ) {
					let variation = {
						id: $(this).attr('data-id'),
						quantity: $(this).find('input[type="text"').val()
					};
	
					variationData.push(variation);
				}

			});

			if ( variationData.length > 0 ) {

				$(this).prop('disabled', true);
				$('.ba-product-modal').addClass('loading');
				
				let postData = {
					product_id: $('.ba-size-table').attr('data-id'),
					variations: variationData
				};

				$.post(ajax_params.ajax_url, {
					action: 'add_to_cart',
					data: JSON.stringify(postData)
				}, function(data, status){
				  	
					// Success
					let cartCount = JSON.parse(data).count;
					$('.fusion-widget-cart-number').attr('data-cart-count', cartCount);
					$('.fusion-widget-cart-number').text(cartCount);
					$('.ba-product-modal .ba-success').show();
					$('.ba-product-modal').removeClass('loading');
					$('.ba-modal-add-to-cart').removeAttr('disabled');
					$('.ba-product-modal .qty-wrap input[type="text"]').each(function() {
						$(this).val('');
					});
					$('.ba-quantity-total').text(0);
					$('.ba-price-total').text(formatter.format(0));
					$(".ba-product-modal").animate({scrollTop: $(".ba-product-modal").scrollTop(0)});

				}).fail(function(response) {

					// Failure
					$('.ba-product-modal').removeClass('loading');
					$('.ba-modal-add-to-cart').removeAttr('disabled');
					$('.ba-product-modal .ba-error').show();
					$(".ba-product-modal").animate({scrollTop: $(".ba-product-modal").scrollTop(0)});

				});

			} else {
				alert('Enter a quantity first!');
			}

		});

		$('.ba-close-modal, .ba-continue').click(function(e) {	

			e.preventDefault();

			$('.ba-quantity-total').text(0);
			$('.ba-price-total').text(formatter.format(0));

			$('.qty-wrap input[type="text"]').each(function() {
				$(this).val('');
			});

			$('.ba-product-modal-wrap').hide();

	
		});

		function populateModal(title, variationData, id) {

			const sizeTable = $('.ba-size-table');

			sizeTable.attr('data-id', id);

			$('.ba-product-modal .ba-product-title').text(title);

			if ( variationData != null ) {
				
				// Variable Product
				for (var i = 0; i < variationData.length; i++) {

					let row = $('<div class="ba-size-table-row" data-id="' + variationData[i]['id'] + '"><p>' + variationData[i]['size'].toUpperCase() + '</p></div>');
	
					row.append('<div class="qty-wrap"><input type="text"></div>');
	
					row.append('<p class="price">$<span>' + variationData[i]['price'].toFixed(2) + '</span> each</p>')
	
					row.appendTo(sizeTable);
					
				}

			} else {

				// Simple Product
				$('.ba-product-modal').addClass('simple');

				let row = $('<div class="ba-size-table-row"></div>'),
					price = $('.ba-product-row[data-id="' + id + '"] ').find('.product-price').text().replace('$', '');

				row.append('<div class="qty-wrap"><input type="text"></div>');
				row.append('<p class="price">$<span>' + price + '</span> each</p>')
				row.appendTo(sizeTable);

			}

		}

		function resetModal() {
			$('.ba-size-table').find('*').not('.ba-size-table-headings, .ba-size-table-headings p').remove();
			$('.ba-product-modal .ba-product-title').empty();
			$('.ba-product-modal .ba-success, .ba-product-modal .ba-error').hide();
			$('.ba-product-modal').removeClass('simple');
		}

		const formatter = new Intl.NumberFormat('en-US', {
			style: 'currency',
			currency: 'USD',
		});

	});


})( jQuery );
