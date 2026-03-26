import PerfectScrollbar from 'perfect-scrollbar';


window.jQuery(function () {
	"use strict";
	var $ = window.jQuery;

	// Initialize PerfectScrollbar
	const bestProductElement = document.querySelector(".best-product");
	const topSellersListElement = document.querySelector(".top-sellers-list");

	if (bestProductElement) {
		new PerfectScrollbar(bestProductElement);
	}

	if (topSellersListElement) {
		new PerfectScrollbar(topSellersListElement);
	}



	// Initialize Select2
	$('.select2').select2({
		theme: 'bootstrap4',
		width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
		placeholder: $(this).data('placeholder'),
		allowClear: Boolean($(this).data('allow-clear')),
	});

	// Toggle Sidebar
	$(".nav-toggle-icon").on("click", function () {
		$(".wrapper").toggleClass("toggled")
	});

	$(".mobile-toggle-icon").on("click", function () {
		$(".wrapper").addClass("toggled")
	});

	$(".toggle-icon").click(function () {
		$(".wrapper").hasClass("toggled") ? ($(".wrapper").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($(".wrapper").addClass("toggled"), $(".sidebar-wrapper").hover(function () {
			$(".wrapper").addClass("sidebar-hovered")
		}, function () {
			$(".wrapper").removeClass("sidebar-hovered")
		}))
	});

	// Search Bar Toggle
	$(".search-toggle-icon").on("click", function () {
		$(".top-header .navbar form").addClass("full-searchbar")
	});
	$(".search-close-icon").on("click", function () {
		$(".top-header .navbar form").removeClass("full-searchbar")
	});

	// Chat/Email Toggles
	$(".chat-toggle-btn").on("click", function () {
		$(".chat-wrapper").toggleClass("chat-toggled")
	}), $(".chat-toggle-btn-mobile").on("click", function () {
		$(".chat-wrapper").removeClass("chat-toggled")
	}), $(".email-toggle-btn").on("click", function () {
		$(".email-wrapper").toggleClass("email-toggled")
	}), $(".email-toggle-btn-mobile").on("click", function () {
		$(".email-wrapper").removeClass("email-toggled")
	}), $(".compose-mail-btn").on("click", function () {
		$(".compose-mail-popup").show()
	}), $(".compose-mail-close").on("click", function () {
		$(".compose-mail-popup").hide()
	});

	// Back to Top
	$(window).on("scroll", function () {
		$(this).scrollTop() > 300 ? $(".back-to-top").fadeIn() : $(".back-to-top").fadeOut()
	}), $(".back-to-top").on("click", function () {
		return $("html, body").animate({
			scrollTop: 0
		}, 600), !1
	});

	// Theme Switcher
	$("#LightTheme").on("click", function () {
		$("html").attr("class", "light-theme")
	}),
		$("#DarkTheme").on("click", function () {
			$("html").attr("class", "dark-theme")
		}),
		$("#SemiDarkTheme").on("click", function () {
			$("html").attr("class", "semi-dark")
		}),
		$("#MinimalTheme").on("click", function () {
			$("html").attr("class", "minimal-theme")
		});

	// Header Colors
	$("#headercolor1").on("click", function () {
		$("html").addClass("color-header headercolor1"), $("html").removeClass("headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor2").on("click", function () {
		$("html").addClass("color-header headercolor2"), $("html").removeClass("headercolor1 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor3").on("click", function () {
		$("html").addClass("color-header headercolor3"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor4").on("click", function () {
		$("html").addClass("color-header headercolor4"), $("html").removeClass("headercolor1 headercolor2 headercolor3 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor5").on("click", function () {
		$("html").addClass("color-header headercolor5"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor3 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor6").on("click", function () {
		$("html").addClass("color-header headercolor6"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor3 headercolor7 headercolor8")
	}), $("#headercolor7").on("click", function () {
		$("html").addClass("color-header headercolor7"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor3 headercolor8")
	}), $("#headercolor8").on("click", function () {
		$("html").addClass("color-header headercolor8"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor3")
	});


	// DataTables Initialization
	// DataTables Initialization
	var tableSelector = 'table.display, table.data-table, .main-tables';

	console.log('Custom.js: Checking for tables with selector:', tableSelector);
	var $foundTables = $(tableSelector);
	console.log('Custom.js: Found ' + $foundTables.length + ' tables.');

	if ($foundTables.length) {
		$foundTables.each(function () {
			var $table = $(this);
			console.log('Custom.js: Initializing DataTable for table ID:', $table.attr('id'));
			var isServerSide = $table.data('server-pagination') === true;
			console.log('Custom.js: Server-side pagination?', isServerSide);

			$table.DataTable({
				paging: !isServerSide,  // Disable paging if server-side
				info: !isServerSide,    // Disable info if server-side
				ordering: true,         // Explicitly enable sorting
				destroy: true,          // Allow re-initialization if it happened elsewhere
				initComplete: function () {
					console.log('Custom.js: DataTable initialization complete for', $table.attr('id'));
					this.api()
						.columns()
						.every(function () {
							var column = this;
							var footer = column.footer();
							if (footer) {
								var title = footer.textContent;
								// Create input element
								var input = document.createElement('input');
								input.placeholder = title;
								input.className = 'form-control form-control-sm'; // Bootstrap styling
								footer.replaceChildren(input);

								// Event listener for user input
								input.addEventListener('keyup', () => {
									if (column.search() !== input.value) {
										column.search(input.value).draw();
									}
								});
								// Stop propagation of click events
								input.addEventListener('click', function (e) {
									e.stopPropagation();
								});
							}
						});
				}
			});
		});
	}

	// Custom Search Function
	function setupSearch(inputSelector, containerSelector, itemSelector, fields) {
		$(inputSelector).on('input', function () {
			var searchTerm = $(this).val().toLowerCase(); // Get search input value

			// Loop through all items in the container and hide or show based on the search term
			$(containerSelector).find(itemSelector).each(function () {
				var matches = fields.some(function (field) {
					var fieldValue = $(this).find(field).text().toLowerCase();
					return fieldValue.includes(searchTerm);
				}, this);

				if (matches) {
					$(this).show();  // Show item
					$(this).find('.group-divider').show();  // Show divider (if applicable)
				} else {
					$(this).hide();  // Hide item
					$(this).find('.group-divider').hide();  // Hide divider (if applicable)
				}
			});
		});
	}

	// Meeting search
	setupSearch('#search-input', '.meetings-container', '.meeting-item', [
		'.meeting-day',
		'.meeting-topic',
		'.meeting-start-time',
		'.meeting-end-time',
		'.meeting-type'
	]);

	// Groups search
	setupSearch('#search-input', '.groups-container', '.group-item', [
		'.group-name',
		'.group-neighborhood',
		'.group-service-body'
	]);

	// Meeting/Group Radio Buttons
	const meetingType = document.getElementById('meeting-type');
	if (meetingType) {
		meetingType.addEventListener('change', function () {
			const label = document.getElementById('switchLabel');
			label.textContent = this.checked ? 'Open' : 'Closed';
		});
	}

	const groupType = document.getElementById('group-type');
	if (groupType) {
		groupType.addEventListener('change', function () {
			const label = document.getElementById('switcGrouphLabel');
			const locationLabel = document.getElementById('location');
			locationLabel.textContent = this.checked ? 'URL' : 'location';
			label.textContent = this.checked ? 'online' : 'physical';
		});
	}

	// MetisMenu Removed - Switched to Bootstrap Collapse

});


