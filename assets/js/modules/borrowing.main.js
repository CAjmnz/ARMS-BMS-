$(document).ready(function () {
	var itemStatusFilter = "";
	var borrowingStatusFilter = "";
	var dateFromFilter = "";
	var dateToFilter = "";

	var borrowerSearchRequest = null;
var borrowerDebounceTimer = null;

$('#borrowingBorrowerSearch').on('input', function () {
    var keyword = $(this).val().trim();
    clearTimeout(borrowerDebounceTimer);

    if (keyword.length < 2) {
        $('#borrowingBorrowerResults').hide().empty();
        return;
    }

    borrowerDebounceTimer = setTimeout(function () {
        performBorrowerSearch(keyword);
    }, 300);
});

$('#borrowingBorrowerSearch').on('keypress', function (e) {
    if (e.which === 13) {
        e.preventDefault();
        clearTimeout(borrowerDebounceTimer);
        var keyword = $(this).val().trim();
        if (keyword.length >= 2) {
            performBorrowerSearch(keyword, true);
        }
    }
});

function performBorrowerSearch(keyword, autoSelectIfSingle) {
    if (borrowerSearchRequest !== null) {
        borrowerSearchRequest.abort();
    }

    var $results = $('#borrowingBorrowerResults');
    $results.html('<div class="p-2 text-muted"><i class="fas fa-spinner fa-spin"></i> Searching...</div>').show();

    borrowerSearchRequest = $.ajax({
        url: BASE_URL + 'user/search_employee',
        type: 'GET',
        data: { q: keyword },
        dataType: 'json',
        cache: false,
        success: function (response) {
            var employees = (response.data && response.data.employee) ? response.data.employee : [];

            if (employees.length === 0) {
                $results.html('<div class="p-2 text-muted">No employee found.</div>').show();
                return;
            }

            if (autoSelectIfSingle && employees.length === 1) {
                selectBorrower(employees[0]);
                return;
            }

			var html = '';
			employees.forEach(function (emp) {
				var photoUrl = emp.employee_photo
					? BASE_URL + 'user/photo_proxy?path=' + encodeURIComponent(emp.employee_photo)
					: null;
			
				var nameParts = (emp.employee_name || '').split(/[\s,]+/).slice(0, 2);
				var initials = nameParts.map(function (p) { return p.charAt(0).toUpperCase(); }).join('') || '?';
			
				var avatarHtml = photoUrl
					? '<img src="' + photoUrl + '" alt="Photo" style="width:32px; height:32px; border-radius:50%; object-fit:cover; flex-shrink:0;" onerror="this.outerHTML=\'<div style=&quot;width:32px;height:32px;border-radius:50%;background:#2563B8;color:#fff;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;flex-shrink:0;&quot;>' + initials + '</div>\'">'
					: '<div style="width:32px; height:32px; border-radius:50%; background:#2563B8; color:#fff; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:600; flex-shrink:0;">' + initials + '</div>';
			
				html += '<div class="borrowerResultRow p-2" style="cursor:pointer; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; gap:10px;" data-emp=\'' + JSON.stringify(emp).replace(/'/g, "&#39;") + '\'>' +
					avatarHtml +
					'<div>' +
					'<strong>' + emp.employee_name + '</strong><br>' +
					'<small class="text-muted">' + (emp.employee_id || '') + ' &middot; ' + (emp.employee_position || '') + '</small>' +
					'</div>' +
					'</div>';
			});
			$results.html(html).show();
        },
        error: function (xhr, status) {
            if (status !== 'abort') {
                $results.html('<div class="p-2 text-danger">Search failed.</div>').show();
            }
        },
        complete: function () {
            borrowerSearchRequest = null;
        }
    });
}

$(document).on('click', '.borrowerResultRow', function () {
    var emp = JSON.parse($(this).attr('data-emp').replace(/&#39;/g, "'"));
    selectBorrower(emp);
});

function selectBorrower(emp) {
    $('#borrowing_borrower_employee_id').val(emp.employee_id);
    $('#borrowing_borrower_position').val(emp.employee_position);
    $('#borrowing_borrower_dept').val(emp.employee_dept);
    $('#borrowing_borrower_photo').val(emp.employee_photo || '');
    $('#borrowingBorrowerSearch').val(emp.employee_name);
    $('#borrowingBorrowerResults').hide().empty();
}
	// Destroy if already initialized
	if ($.fn.DataTable.isDataTable("#borrowingTable")) {
		$("#borrowingTable").DataTable().destroy();
	}

	// Init server-side DataTable
	var table = $("#borrowingTable").DataTable({
		processing: true,
		serverSide: true,
		responsive: true,
		autoWidth: false,
		lengthMenu: [
			[5, 10, 25, 50],
			[5, 10, 25, 50],
		],
		pageLength: 10,
		ajax: {
			url: BASE_URL + "borrowing/ajax_list",
			type: "POST",
			data: function (d) {
				d.item_status = itemStatusFilter;
				d.borrowing_status = borrowingStatusFilter;
				d.date_from = dateFromFilter;
				d.date_to = dateToFilter;
			},
		},
		columns: [
			{ data: 0, orderable: false },  // #
			{ data: 1 },                    // Borrower's Id
			{ data: 2 },                    // Borrower's name
			{ data: 3 },                    // Position
			{ data: 4 },                    // Item Name
			{ data: 5 },                    // Category
			{ data: 6, orderable: false },  // Quantity
			{ data: 7 },                    // Condition Before Borrowing
			{ data: 8 },                    // Borrowed Date
			{ data: 9 },                    // Due date
			{ data: 10, orderable: false }, // Borrowing status
			{ data: 11 },                   // Released by
			{ data: 12, orderable: false }  // Action
		],
		order: [[8, 'desc']],   // Borrowed Date is now index 8, not 7
		language: {
			emptyTable: "No borrowing records found.",
			processing: '<i class="fas fa-spinner fa-spin"></i> Loading...',
		},
	});
	// after: var table = $('#borrowingTable').DataTable({...});

	setInterval(function () {
		table.ajax.reload(null, false);
	}, 30000);
	// ─── Auto-apply filter from URL (e.g. from a notification click) ──
	var urlParams = new URLSearchParams(window.location.search);
	var initialFilter = urlParams.get("filter");

	if (initialFilter) {
		itemStatusFilter = initialFilter;
		$('select[name="status"]').val(initialFilter);
		table.ajax.reload();
	}
	// ─── Filters ─────────────────────────────────────────
	$("#filterForm").on("submit", function (e) {
		e.preventDefault();
		itemStatusFilter = $('select[name="status"]').val() || "";
		borrowingStatusFilter = $('select[name="condition"]').val() || "";
		dateFromFilter = $('input[name="date_from"]').val() || "";
		dateToFilter = $('input[name="date_to"]').val() || "";
		table.ajax.reload();
	});

	$("#btnReset").on("click", function () {
		$('select[name="status"]').val("");
		$('select[name="condition"]').val("");
		$('input[name="date_from"]').val("");
		$('input[name="date_to"]').val("");
		itemStatusFilter = "";
		borrowingStatusFilter = "";
		dateFromFilter = "";
		dateToFilter = "";
		table.ajax.reload();
	});

	// ─── Add Borrowing Modal ───────────────────────────────
	$('#btnAddBorrowing').click(function () {
		$('#borrowing_borrower_employee_id').val('');
		$('#borrowing_borrower_position').val('');
		$('#borrowing_borrower_dept').val('');
		$('#borrowing_borrower_photo').val('');
		$('#borrowingBorrowerSearch').val('');
		$('#borrowingBorrowerResults').hide().empty();
		$('#borrowing_item_id').val('').trigger('change');
		$('#borrowing_purpose').val('');
		$('#borrowing_due_date').val('');
		$('#unitCheckboxList').html('<span class="text-muted">Select an item first.</span>');
		$('#borrowingModal').modal('show');
	});

	// ─── Load available units when item changes ────────────
	$(document).on("change", "#borrowing_item_id", function () {
		var itemId = $(this).val();
		var $list = $("#unitCheckboxList");

		if (!itemId) {
			$list.html('<span class="text-muted">Select an item first.</span>');
			return;
		}

		$list.html('<span class="text-muted">Loading units...</span>');

		$.get(
			BASE_URL + "borrowing/get_available_units/" + itemId,
			function (res) {
				if (res.success && res.units.length > 0) {
					var html = "";
					res.units.forEach(function (unit) {
						html +=
							'<div class="form-check">' +
							'<input class="form-check-input unitCheckbox" type="checkbox" value="' +
							unit.id +
							'" id="unit_' +
							unit.id +
							'">' +
							'<label class="form-check-label" for="unit_' +
							unit.id +
							'">' +
							"Unit #" +
							unit.unit_no +
							" (" +
							unit.item_condition +
							")" +
							"</label>" +
							"</div>";
					});
					$list.html(html);
				} else {
					$list.html(
						'<span class="text-danger">No available units for this item.</span>',
					);
				}
			},
			"json",
		);
	});

	// ─── Save Borrowing ─────────────────────────────────────
	$('#btnSaveBorrowing').click(function () {
		var borrowerEmployeeId = $('#borrowing_borrower_employee_id').val();
		var borrowerName        = $('#borrowingBorrowerSearch').val();
		var borrowerPosition    = $('#borrowing_borrower_position').val();
		var borrowerDept        = $('#borrowing_borrower_dept').val();
		var borrowerPhoto       = $('#borrowing_borrower_photo').val();
		var purpose              = $('#borrowing_purpose').val().trim();
		var dueDate               = $('#borrowing_due_date').val();
		var unitIds                = [];
	
		$('.unitCheckbox:checked').each(function () {
			unitIds.push($(this).val());
		});
	
		if (!borrowerEmployeeId) {
			Swal.fire('Warning', 'Please select a borrower.', 'warning');
			return;
		}
		if (unitIds.length === 0) {
			Swal.fire('Warning', 'Please select at least one unit.', 'warning');
			return;
		}
		if (!dueDate) {
			Swal.fire('Warning', 'Please set a due date.', 'warning');
			return;
		}
	
		var now = new Date();
		if (new Date(dueDate) < now) {
			Swal.fire('Warning', 'Due date cannot be in the past.', 'warning');
			return;
		}
	
		$.post(BASE_URL + 'borrowing/store', {
			borrower_employee_id : borrowerEmployeeId,
			borrower_name         : borrowerName,
			borrower_position     : borrowerPosition,
			borrower_dept         : borrowerDept,
			borrower_photo        : borrowerPhoto,
			unit_ids               : unitIds,
			purpose                 : purpose,
			due_date                 : dueDate
		}, function (res) {
			if (res.success) {
				$('#borrowingModal').modal('hide');
				Swal.fire('Success', res.message, 'success').then(function () {
					table.ajax.reload();
				});
			} else {
				Swal.fire('Error', res.message, 'error');
			}
		}, 'json')
		.fail(function (xhr) {
			console.log('Error:', xhr.responseText);
			Swal.fire('Error', 'Something went wrong.', 'error');
		});
	});
	// ─── Open Mark Returned Modal ───────────────────────────
	$("#borrowingTable").on("click", ".btnReturn", function () {
		var id = $(this).data("id");
		$("#return_borrowing_item_id").val(id);
		$("#return_item_status").val("returned");
		$("#return_condition_after").val("good");
		$("#return_remarks").val("");
		$("#returnModal").modal("show");
	});

	// Toggle condition dropdown based on return status
	$(document).on("change", "#return_item_status", function () {
		if ($(this).val() === "returned") {
			$("#conditionAfterGroup").show();
		} else {
			$("#conditionAfterGroup").hide();
		}
	});

	// ─── Confirm Return ──────────────────────────────────────
	$("#btnConfirmReturn").click(function () {
		var id = $("#return_borrowing_item_id").val();
		var itemStatus = $("#return_item_status").val();
		var conditionAfter = $("#return_condition_after").val();
		var remarks = $("#return_remarks").val().trim();

		$.post(
			BASE_URL + "borrowing/mark_returned/" + id,
			{
				item_status: itemStatus,
				condition_after: conditionAfter,
				remarks: remarks,
			},
			function (res) {
				if (res.success) {
					$("#returnModal").modal("hide");
					Swal.fire("Success", res.message, "success").then(function () {
						table.ajax.reload();
					});
				} else {
					Swal.fire("Error", res.message, "error");
				}
			},
			"json",
		).fail(function (xhr) {
			console.log("Error:", xhr.responseText);
			Swal.fire("Error", "Something went wrong.", "error");
		});
	});
	// ─── Select2 with color-coded availability ─────────────
function formatItemOption(item) {
    if (!item.id) return item.text;

    var available = $(item.element).data('available');
    var color, label;

    if (available <= 2) {
        color = '#e74a3b'; // red
    } else if (available <= 5) {
        color = '#f6c23e'; // yellow
    } else {
        color = '#1cc88a'; // green
    }

    return $(
        '<span>' + item.text +
        ' <span style="color:' + color + '; font-weight:600;">(Available: ' + available + ')</span>' +
        '</span>'
    );
}

$('#borrowing_item_id').select2({
    theme: 'default',
    dropdownParent: $('#borrowingModal'),
    templateResult: formatItemOption,
    templateSelection: formatItemOption,
    width: '100%'
});
});
