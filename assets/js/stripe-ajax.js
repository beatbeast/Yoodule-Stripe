jQuery(document).ready(function($){

	
	$('#yoodule-table').DataTable({
		"processing":true,
       //"serverSide":true,
		ajax: {url: '/wp-json/yoodule/rest-ajax', dataSrc:""},
			columns: [
			{ data: 'object' },
			{ data: 'currency' },
			{ data: 'product' },
				{ data: 'unit_amount' },
			]
    });
});