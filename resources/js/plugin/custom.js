import $ from 'jquery';
import PerfectScrollbar from 'perfect-scrollbar';


const bestProductElement = document.querySelector(".best-product");
const topSellersListElement = document.querySelector(".top-sellers-list");

if (bestProductElement) {
    new PerfectScrollbar(bestProductElement);
}

if (topSellersListElement) {
    new PerfectScrollbar(topSellersListElement);
}



$(function() {
	"use strict";

	// $('.select2').select2();

	$('.select2').select2({
        theme: 'bootstrap4',
        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
        placeholder: $(this).data('placeholder'),
        allowClear: Boolean($(this).data('allow-clear')),
    });
  // Tooltops

    // $(function () {
    //     $('[data-bs-toggle="tooltip"]').tooltip();
    // })


    $(".nav-toggle-icon").on("click", function() {
		$(".wrapper").toggleClass("toggled")
	})

    $(".mobile-toggle-icon").on("click", function() {
		$(".wrapper").addClass("toggled")
	})

	$(function() {
		for (var e = window.location, o = $(".metismenu li a").filter(function() {
				return this.href == e
			}).addClass("").parent().addClass("mm-active"); o.is("li");) o = o.parent("").addClass("mm-show").parent("").addClass("mm-active")
	})


	$(".toggle-icon").click(function() {
		$(".wrapper").hasClass("toggled") ? ($(".wrapper").removeClass("toggled"), $(".sidebar-wrapper").unbind("hover")) : ($(".wrapper").addClass("toggled"), $(".sidebar-wrapper").hover(function() {
			$(".wrapper").addClass("sidebar-hovered")
		}, function() {
			$(".wrapper").removeClass("sidebar-hovered")
		}))
	})



	$(function() {
		$("#menu").metisMenu()
	})


	$(".search-toggle-icon").on("click", function() {
		$(".top-header .navbar form").addClass("full-searchbar")
	})
	$(".search-close-icon").on("click", function() {
		$(".top-header .navbar form").removeClass("full-searchbar")
	})


	$(".chat-toggle-btn").on("click", function() {
		$(".chat-wrapper").toggleClass("chat-toggled")
	}), $(".chat-toggle-btn-mobile").on("click", function() {
		$(".chat-wrapper").removeClass("chat-toggled")
	}), $(".email-toggle-btn").on("click", function() {
		$(".email-wrapper").toggleClass("email-toggled")
	}), $(".email-toggle-btn-mobile").on("click", function() {
		$(".email-wrapper").removeClass("email-toggled")
	}), $(".compose-mail-btn").on("click", function() {
		$(".compose-mail-popup").show()
	}), $(".compose-mail-close").on("click", function() {
		$(".compose-mail-popup").hide()
	})


	$(function() {
		$(window).on("scroll", function() {
			$(this).scrollTop() > 300 ? $(".back-to-top").fadeIn() : $(".back-to-top").fadeOut()
		}), $(".back-to-top").on("click", function() {
			return $("html, body").animate({
				scrollTop: 0
			}, 600), !1
		})
	})


	// switcher 

	$("#LightTheme").on("click", function() {
		$("html").attr("class", "light-theme")
	}),

	$("#DarkTheme").on("click", function() {
		$("html").attr("class", "dark-theme")
	}),

	$("#SemiDarkTheme").on("click", function() {
		$("html").attr("class", "semi-dark")
	}),

	$("#MinimalTheme").on("click", function() {
		$("html").attr("class", "minimal-theme")
	})


	$("#headercolor1").on("click", function() {
		$("html").addClass("color-header headercolor1"), $("html").removeClass("headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor2").on("click", function() {
		$("html").addClass("color-header headercolor2"), $("html").removeClass("headercolor1 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor3").on("click", function() {
		$("html").addClass("color-header headercolor3"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor4").on("click", function() {
		$("html").addClass("color-header headercolor4"), $("html").removeClass("headercolor1 headercolor2 headercolor3 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor5").on("click", function() {
		$("html").addClass("color-header headercolor5"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor3 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor6").on("click", function() {
		$("html").addClass("color-header headercolor6"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor3 headercolor7 headercolor8")
	}), $("#headercolor7").on("click", function() {
		$("html").addClass("color-header headercolor7"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor3 headercolor8")
	}), $("#headercolor8").on("click", function() {
		$("html").addClass("color-header headercolor8"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor3")
	})


	// new PerfectScrollbar(".header-message-list")
    // new PerfectScrollbar(".header-notifications-list")

	

});

// Customize Data Table:
$(function() {
	"use strict";


	$('#example').DataTable();


	// var table = $('#example2').DataTable( {
	// 	lengthChange: false,
	// 	buttons: [ 'copy', 'excel', 'pdf', 'print']
	// } );
	
	// table.buttons().container()
	// 	.appendTo( '#example2_wrapper .col-md-6:eq(0)' );


	// 	$('#myTable').DataTable({
	// 		// responsive: true,
	// 	});


	
	
});
// Search bar:
$(function() {
    function setupSearch(inputSelector, containerSelector, itemSelector, fields) {
        $(inputSelector).on('input', function() {
            var searchTerm = $(this).val().toLowerCase(); // Get search input value

            // Loop through all items in the container and hide or show based on the search term
            $(containerSelector).find(itemSelector).each(function() {
                var matches = fields.some(function(field) {
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
	
});


$(function () {

	// Meeting Radio Button [ open-close]
    const meetingType = document.getElementById('meeting-type');
    if (meetingType) {
        meetingType.addEventListener('change', function () {
            const label = document.getElementById('switchLabel');
            label.textContent = this.checked ? 'Open' : 'Close';
        });
    }

	// Group Radio Button [physical-online]
	const groupType = document.getElementById('group-type');
	if (groupType) {
		groupType.addEventListener('change', function () {
			const label = document.getElementById('switcGrouphLabel');
			const locationLabel = document.getElementById('location');
			locationLabel.textContent = this.checked ? 'URL' : 'location';
			label.textContent = this.checked ? 'online' : 'physical';
		});
	}
});

	




// Show Logs Details button:
// $(function(){

// 	// JavaScript to toggle visibility of transaction rows - [ index page ]:
// 	document.querySelectorAll('.transaction-row').forEach(row => {
// 		row.addEventListener('click', function() {
// 			const transactionId = this.dataset.transactionId;
// 			const transactionRow = document.getElementById(`transactions-${transactionId}`);
// 			// Toggle visibility
// 			transactionRow.style.display = transactionRow.style.display === 'none' ? 'table-row' : 'none';
// 		});
// 	});
// });

